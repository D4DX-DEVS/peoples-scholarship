<?php

namespace App\Support\Html;

use Illuminate\Support\Facades\Facade;

/**
 * The "Form" alias used throughout the Blade views.
 *
 * @method static \Illuminate\Support\HtmlString select(string $name, array $list = [], mixed $selected = null, array $selectAttributes = [], array $optionsAttributes = [])
 * @method static \Illuminate\Support\HtmlString radio(string $name, mixed $value = null, mixed $checked = null, array $options = [])
 *
 * @see \App\Support\Html\FormBuilder
 */
class FormFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FormBuilder::class;
    }
}
