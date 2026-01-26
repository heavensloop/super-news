<?php

namespace App\Http\Requests;

use App\Enum\FilterType;
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

                $types = array_keys($decoded);

                foreach ($types as $type) {
                    if (!in_array($type, array_map(fn(FilterType $ft) => $ft->value, FilterType::cases()), true)) {
                        return $fail('The filter type ' . $type . ' is invalid.');
                    }
                }

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

        if (!isset($data['filters'])) {
            return [];
        }

        $data = json_decode($data['filters'], true);
        $filters = [];

        foreach ($data as $key => $value) {
            $filters[] = [
                'type' => FilterType::from($key),
                'value' => $value,
            ];
        }

        return $filters;
    }
}
