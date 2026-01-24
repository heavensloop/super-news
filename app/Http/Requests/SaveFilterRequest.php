<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveFilterRequest extends FormRequest
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
            'is_default' => 'sometimes|boolean',
            'name' => 'required|string|max:255',

            'filters.*.value' => ['required', function($attribute, $value, $fail) {
                if (is_array($value)) {
                    if (!isset($value['from']) || !isset($value['to'])) {
                        $fail($attribute . ' must have both "from" and "to" keys when it is an array.');
                    } elseif (!is_string($value['from']) || !is_string($value['to'])) {
                        $fail($attribute . ' "from" and "to" must be strings.');
                    }
                } elseif (!is_string($value)) {
                    $fail($attribute . ' must be a string or an array with "from" and "to" keys.');
                }
            }],
            'filters.*.value.from' => 'required_with:value.to|string',
            'filters.*.value.to' => 'required_with:value.from|string',
            'filters.*.type.id' => 'required|string',
            'filters.*.type.label' => 'required|string',
            'filters.*.type.inputType' => 'sometimes|string',
        ];
    }
}
