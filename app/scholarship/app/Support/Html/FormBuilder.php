<?php

namespace App\Support\Html;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;

/**
 * Minimal in-repo replacement for laravelcollective/html's Form facade.
 *
 * That package was abandoned and never supported Laravel 9+, which blocked the
 * framework upgrade. The views only ever used Form::select() and Form::radio(),
 * so just those two are reimplemented here — deliberately matching the original
 * markup, attribute ordering and old-input repopulation so the rendered HTML is
 * byte-for-byte identical to what the package produced.
 */
class FormBuilder
{
    public function __construct(
        private Request $request,
        private ?Session $session = null,
    ) {
    }

    /**
     * Build a <select> element.
     *
     * @param  array<string|int, mixed>  $list
     * @param  array<string, mixed>  $selectAttributes
     * @param  array<string|int, array<string, mixed>>  $optionsAttributes
     */
    public function select(
        string $name,
        array $list = [],
        mixed $selected = null,
        array $selectAttributes = [],
        array $optionsAttributes = [],
    ): HtmlString {
        // Old input and the current request win over the passed-in value, so a
        // failed validation round-trip keeps the user's selection.
        $selected = $this->getValueAttribute($name, $selected);

        $selectAttributes['id'] = $selectAttributes['id'] ?? null;

        if (! isset($selectAttributes['name'])) {
            $selectAttributes['name'] = $name;
        }

        $html = [];

        if (isset($selectAttributes['placeholder'])) {
            $html[] = $this->placeholderOption($selectAttributes['placeholder'], $selected);
            unset($selectAttributes['placeholder']);
        }

        foreach ($list as $value => $display) {
            $html[] = $this->option($display, $value, $selected, $optionsAttributes[$value] ?? []);
        }

        return new HtmlString(
            '<select'.$this->attributes($selectAttributes).'>'.implode('', $html).'</select>'
        );
    }

    /**
     * Build a radio <input>.
     *
     * @param  array<string, mixed>  $options
     */
    public function radio(string $name, mixed $value = null, mixed $checked = null, array $options = []): HtmlString
    {
        if (is_null($value)) {
            $value = $name;
        }

        if ($this->getRadioCheckedState($name, $value, $checked)) {
            $options['checked'] = 'checked';
        }

        if (! isset($options['name'])) {
            $options['name'] = $name;
        }

        // Attribute order follows the original package exactly: the caller's
        // options, then checked, name, and finally type/value/id.
        $options = array_merge($options, [
            'type' => 'radio',
            'value' => $value,
            'id' => $options['id'] ?? null,
        ]);

        return new HtmlString('<input'.$this->attributes($options).'>');
    }

    /**
     * A radio is checked from old input or the request when either is present,
     * otherwise it falls back to the caller's default.
     */
    protected function getRadioCheckedState(string $name, mixed $value, mixed $checked): bool
    {
        $request = $this->requestValue($name);

        if (is_null($this->old($name)) && is_null($request)) {
            return (bool) $checked;
        }

        return $this->getValueAttribute($name) == $value;
    }

    protected function option(mixed $display, mixed $value, mixed $selected, array $attributes = []): string
    {
        $options = array_merge(
            ['value' => $value, 'selected' => $this->getSelectedValue($value, $selected)],
            $attributes,
        );

        $string = '<option'.$this->attributes($options).'>';

        if ($display !== null) {
            $string .= e($display, false).'</option>';
        }

        return $string;
    }

    protected function placeholderOption(mixed $display, mixed $selected): string
    {
        return '<option'.$this->attributes([
            'selected' => $this->getSelectedValue(null, $selected),
            'value' => '',
        ]).'>'.e($display, false).'</option>';
    }

    protected function getSelectedValue(mixed $value, mixed $selected): string|bool|null
    {
        if (is_array($selected)) {
            return in_array($value, $selected, true) || in_array((string) $value, $selected, true)
                ? 'selected'
                : null;
        }

        // Quirk carried over from the original package: when a caller passes a
        // boolean as the selected value (several views pass `false`), it returns
        // a bool rather than null. A false here renders as an empty attribute,
        // which is why unselected options are emitted as `<option value="1" >`.
        // Reproduced so the markup stays byte-identical to the previous output.
        if (is_int($value) && is_bool($selected)) {
            return (string) $value == $selected;
        }

        return ((string) $value === (string) $selected) ? 'selected' : null;
    }

    /**
     * Resolve a field's value: old input, then request, then the given default.
     */
    protected function getValueAttribute(?string $name, mixed $value = null): mixed
    {
        if (is_null($name)) {
            return $value;
        }

        $old = $this->old($name);

        if (! is_null($old)) {
            return $old;
        }

        $request = $this->requestValue($name);

        if (! is_null($request)) {
            return $request;
        }

        return $value;
    }

    protected function old(string $name): mixed
    {
        if (! $this->session instanceof Session) {
            return null;
        }

        return $this->session->getOldInput($this->transformKey($name));
    }

    protected function requestValue(string $name): mixed
    {
        return $this->request->input($this->transformKey($name));
    }

    /**
     * Convert bracketed field names (e.g. "foo[bar]") into dot notation.
     */
    protected function transformKey(string $key): string
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }

    /**
     * Render an attribute array, mirroring HtmlBuilder::attributes().
     *
     * @param  array<string|int, mixed>  $attributes
     */
    protected function attributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if (! is_null($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' '.implode(' ', $html) : '';
    }

    protected function attributeElement(string|int $key, mixed $value): ?string
    {
        // A numeric key means the value is itself a bare boolean attribute.
        if (is_numeric($key)) {
            return $value;
        }

        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="'.implode(' ', $value).'"';
        }

        if (! is_null($value)) {
            return $key.'="'.e($value, false).'"';
        }

        return null;
    }
}
