<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Dependent-dropdown lookups used by the application forms.
 *
 * The Input facade these methods relied on was removed in Laravel 6, and the
 * query builder's lists() helper was removed back in 5.3 — both are replaced
 * with their current equivalents ($request->input() and pluck()).
 */
class AjaxController extends Controller
{
    /**
     * Areas belonging to a district (looked up by district name).
     */
    public function postUnit(Request $request)
    {
        return DB::table('area')
            ->where('district', $request->input('district'))
            ->pluck('area')
            ->toArray();
    }

    /**
     * Units belonging to an area.
     */
    public function postArea(Request $request)
    {
        return DB::table('unit')
            ->where('area_id', (int) $request->input('area'))
            ->orderBy('unit', 'asc')
            ->pluck('unit', 'id')
            ->toArray();
    }

    /**
     * All categories, keyed by id.
     */
    public function getCategory()
    {
        return DB::table('categories')->pluck('catname', 'id')->toArray();
    }

    /**
     * Enabled courses within a category.
     */
    public function postCategory(Request $request)
    {
        return DB::table('courses')
            ->where('cat_id', (int) $request->input('category'))
            ->where('course_enabled', 1)
            ->pluck('coursename', 'id')
            ->toArray();
    }

    /**
     * Areas within a district.
     */
    public function postDistrict(Request $request)
    {
        return DB::table('area')
            ->where('district_id', (int) $request->input('district'))
            ->orderBy('area', 'asc')
            ->pluck('area', 'id')
            ->toArray();
    }

    /**
     * Areas across several districts.
     */
    public function postDistrictAll(Request $request)
    {
        return DB::table('area')
            ->whereIn('district_id', array_map('intval', (array) $request->input('district', [])))
            ->orderBy('area', 'asc')
            ->pluck('area', 'id')
            ->toArray();
    }

    /**
     * Units within an area.
     */
    public function postAreaId(Request $request)
    {
        return DB::table('unit')
            ->where('area_id', (int) $request->input('area'))
            ->pluck('unit', 'id')
            ->toArray();
    }
}
