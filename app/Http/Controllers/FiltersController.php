<?php

namespace App\Http\Controllers;

use App\Enum\FilterType;

class FiltersController extends Controller
{
    public function getTypes()
    {
        $types = collect(FilterType::cases())->map(fn (FilterType $type) => [
            'id' => $type->value,
            'label' => $type->getReadable(),
            'inputType' => $type->getInputType(),
        ])->values();

        return response()->json(['data' => $types], 200);
    }
}
