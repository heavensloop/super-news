<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilteredArticleRequest extends FormRequest
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
            'filters' => ['string', function($attribute, $value, $fail) {
                $decoded = json_decode($value, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return $fail('The ' . $attribute . ' must be a valid JSON string.');
                }

                if (!is_array($decoded)) {
                    return $fail('The ' . $attribute . ' must decode to an array.');
                }
            }, 'sometimes'],
            'page' => 'integer|min:1|sometimes',
        ];
    }

    public function getFilters(): array
    {
        $data = $this->validated();

        if (isset($data['filters'])) {
            return json_decode($data['filters'], true);
        }

        return [];
    }
}
