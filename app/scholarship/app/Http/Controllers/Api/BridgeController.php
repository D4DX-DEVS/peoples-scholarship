<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Area;
use App\Category;
use App\Course;
use App\District;
use App\Http\Controllers\Controller;
use App\Person;
use App\Unit;
use App\Yearsetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Ingests scholarship applications submitted through the People-ERP
 * beneficiary portal.
 *
 * The portal is a separate application on its own database. Rather than have
 * it write into these collections directly — which would mean reimplementing
 * this system's integer ids, reference numbers and year handling in another
 * language, and keeping the two implementations in step forever — it posts a
 * flat payload here and this system allocates everything itself.
 *
 * Ingest is idempotent on external_id (the portal's own application id), so
 * the portal can safely retry a push it is not sure landed. A repeat push
 * refreshes the applicant's details but never touches status, grant state or
 * the reference number: once an application is here, its progress through the
 * workflow belongs to the admin dashboard alone.
 */
class BridgeController extends Controller
{
    /**
     * Category and course lists, so the portal's form can offer the same
     * options this system stores ids for.
     */
    public function master(): JsonResponse
    {
        return response()->json([
            'categories' => Category::orderBy('catname')->get(['id', 'catname'])
                ->map(fn ($c) => ['id' => (int) $c->id, 'name' => $c->catname])
                ->values(),
            'courses' => Course::where('course_enabled', 1)->orderBy('coursename')->get(['id', 'coursename', 'cat_id'])
                ->map(fn ($c) => ['id' => (int) $c->id, 'name' => $c->coursename, 'category_id' => (int) $c->cat_id])
                ->values(),
        ]);
    }

    /**
     * Translate portal location names into this system's integer ids.
     *
     * The portal's location tree was seeded separately and its names have
     * drifted from these ones, so anything unrecognised is created rather
     * than rejected — an application must never be lost because a unit was
     * spelled differently. The portal caches what it gets back, so each
     * location costs one call here exactly once.
     */
    public function resolveLocations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'district' => 'required|string|max:255',
            'area' => 'nullable|string|max:300',
            'unit' => 'nullable|string|max:300',
        ]);

        return response()->json($this->resolveLocationIds($validated));
    }

    public function storeApplication(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'external_id' => 'required|string|max:64',
            'external_number' => 'nullable|string|max:64',
            'scheme_code' => 'nullable|string|max:64',

            'person.name' => 'required|string|max:255',
            'person.mobile' => 'required|string|max:20',
            'person.aadhar' => 'nullable|string|max:20',
            'person.gender' => 'nullable|string|max:10',
            'person.age' => 'nullable|integer',
            'person.dob' => 'nullable|string|max:20',
            'person.guardian' => 'nullable|string|max:255',
            'person.address' => 'nullable|string',
            'person.pin' => 'nullable|string|max:10',
            'person.email' => 'nullable|string|max:255',
            'person.phone2' => 'nullable|string|max:20',
            'person.photo_url' => 'nullable|url|max:2048',

            // Required, not optional: the admin views dereference an
            // application's unit and area without guarding for null, and the
            // portal marks all three mandatory on its own applications, so
            // there is no case where accepting a partial location helps.
            'location.district' => 'required|string|max:255',
            'location.area' => 'required|string|max:300',
            'location.unit' => 'required|string|max:300',
            'location.district_id' => 'nullable|integer',
            'location.area_id' => 'nullable|integer',
            'location.unit_id' => 'nullable|integer',

            'application.cat_id' => 'nullable|integer',
            'application.course_id' => 'nullable|integer',
            'application.course_other' => 'nullable|string|max:255',
            'application.institution' => 'nullable|string|max:255',
            'application.documents' => 'nullable|array',
            'application.documents.*.name' => 'nullable|string|max:255',
            'application.documents.*.url' => 'nullable|url|max:2048',
        ]);

        $yearsetting = Yearsetting::orderBy('id', 'desc')->first();

        if (is_null($yearsetting)) {
            return response()->json(['message' => 'No application year is configured.'], 409);
        }

        $person = $this->resolvePerson($validated['person']);

        // The portal sends ids it has already learned and names as a fallback,
        // so a location only needs looking up the first time it is seen.
        $locationIds = $this->cachedLocationIds($validated['location']);

        $application = Application::where('external_id', $validated['external_id'])->first();
        $isNew = is_null($application);

        if ($isNew) {
            $application = new Application;
            $application->external_id = $validated['external_id'];
            $application->external_source = 'erp';
            $application->year_id = (int) $yearsetting->id;
            $application->persid = (int) $person->id;
            $application->status = 1; // Registered
        }

        $documents = $validated['application']['documents'] ?? [];

        $application->external_number = $validated['external_number'] ?? null;
        $application->external_scheme_code = $validated['scheme_code'] ?? null;
        $application->external_documents = $documents;
        $application->attachments_exist = count($documents) > 0 ? 1 : 0;
        $application->applicant_name = $validated['person']['name'];
        $application->cat_id = $validated['application']['cat_id'] ?? null;
        $application->course_id = $validated['application']['course_id'] ?? null;
        $application->course_other = $validated['application']['course_other'] ?? null;
        $application->institution = $validated['application']['institution'] ?? null;
        $application->district_id = $locationIds['district_id'];
        $application->area_id = $locationIds['area_id'];
        $application->unit_id = $locationIds['unit_id'];

        $application->save();

        // Needs the id, so it can only be built once the row exists. Set on
        // insert only — an admin may have corrected it since.
        if ($isNew) {
            $application->refno = Application::makeRefno($yearsetting, $application->id);
            $application->save();
        }

        return response()->json([
            'refno' => $application->refno,
            'application_id' => (int) $application->id,
            'person_id' => (int) $person->id,
            'created' => $isNew,
            'location_ids' => $locationIds,
        ], $isNew ? 201 : 200);
    }

    /**
     * Find the applicant, or create them.
     *
     * Matched on mobile first and aadhaar second, which is the same pairing
     * the public form treats as identifying. That is what keeps an applicant
     * who used the old form and then the portal from becoming two people.
     */
    private function resolvePerson(array $data): Person
    {
        $person = Person::where('mobile', $data['mobile'])->first();

        if (is_null($person) && ! empty($data['aadhar'])) {
            $person = Person::where('aadhar', $data['aadhar'])->first();
        }

        if (is_null($person)) {
            $person = new Person;
        }

        $person->personname = $data['name'];
        $person->mobile = $data['mobile'];

        foreach (['aadhar', 'gender', 'age', 'dob', 'guardian', 'address', 'pin', 'email'] as $field) {
            if (isset($data[$field])) {
                $person->{$field} = $data[$field];
            }
        }

        if (isset($data['phone2'])) {
            $person->phone2 = $data['phone2'];
        }

        if (! empty($data['photo_url'])) {
            $person->photo_external_url = $data['photo_url'];
        }

        $person->applied_date = date('Y-m-d H:i:s');
        $person->save();

        return $person;
    }

    /**
     * @return array{district_id: int|null, area_id: int|null, unit_id: int|null}
     */
    private function cachedLocationIds(array $location): array
    {
        if (! empty($location['district_id'])) {
            return [
                'district_id' => (int) $location['district_id'],
                'area_id' => isset($location['area_id']) ? (int) $location['area_id'] : null,
                'unit_id' => isset($location['unit_id']) ? (int) $location['unit_id'] : null,
            ];
        }

        return $this->resolveLocationIds($location);
    }

    /**
     * @return array{district_id: int|null, area_id: int|null, unit_id: int|null}
     */
    private function resolveLocationIds(array $names): array
    {
        $district = $this->firstOrCreateByName(District::query(), 'district', $names['district']);

        $area = null;
        if (! empty($names['area'])) {
            $area = $this->firstOrCreateByName(
                Area::where('district_id', (int) $district->id),
                'area',
                $names['area'],
                ['district_id' => (int) $district->id],
            );
        }

        $unit = null;
        if (! empty($names['unit']) && $area) {
            $unit = $this->firstOrCreateByName(
                Unit::where('area_id', (int) $area->id),
                'unit',
                $names['unit'],
                ['district_id' => (int) $district->id, 'area_id' => (int) $area->id],
            );
        }

        return [
            'district_id' => (int) $district->id,
            'area_id' => $area ? (int) $area->id : null,
            'unit_id' => $unit ? (int) $unit->id : null,
        ];
    }

    /**
     * Match a name within an already-scoped query, ignoring case, spacing and
     * punctuation, and create the row if nothing matches.
     *
     * The comparison is done in PHP rather than as a query because the stored
     * names contain trailing spaces and inconsistent punctuation that no
     * single Mongo predicate matches reliably. These collections hold at most
     * a few thousand rows, so loading a scoped slice is cheap.
     */
    private function firstOrCreateByName($query, string $column, string $name, array $attributes = [])
    {
        $wanted = $this->normalise($name);

        foreach ($query->get() as $row) {
            if ($this->normalise((string) $row->{$column}) === $wanted) {
                return $row;
            }
        }

        $model = $query->getModel()->newInstance();
        $model->{$column} = trim($name);

        foreach ($attributes as $key => $value) {
            $model->{$key} = $value;
        }

        $model->save();

        return $model;
    }

    private function normalise(string $value): string
    {
        return preg_replace('/[^A-Z0-9]/', '', strtoupper($value));
    }
}
