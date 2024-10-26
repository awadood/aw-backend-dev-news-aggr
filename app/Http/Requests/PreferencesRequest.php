<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PreferencesRequest handles validation for storing user preferences.
 *
 * This form request is used to validate the data when a user sets their
 * preferences for attributes e.g. sources, categories, and authors of articles.
 * It ensures that the data provided is complete and correctly formatted before
 * proceeding to store it in the database.
 */
class PreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'preferences' => 'required|array',
            'preferences.*.name' => 'required|string',
            'preferences.*.value' => 'required|string',
        ];
    }
}
