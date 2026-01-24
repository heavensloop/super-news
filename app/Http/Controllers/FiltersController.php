<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveFilterRequest;
use App\Models\UserFilter;

class FiltersController extends Controller
{
    public function save(SaveFilterRequest $request)
    {
        $data = $request->validated();

        $userFilter = new UserFilter();
        $userFilter->user_id = $request->user()->id;
        $userFilter->name = $data['name'];
        $userFilter->settings = $data['filters'];
        $userFilter->is_default = $data['is_default'] ?? false;
        $userFilter->save();

        // Logic to save user filters
        return response()->json(['message' => 'Filters saved successfully'], 200);
    }
}
