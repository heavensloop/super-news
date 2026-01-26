<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveFilterRequest;
use App\Http\Resources\UserFilterResource;
use App\Models\UserFilter;
use Illuminate\Http\Request;

class UserFiltersController extends Controller
{
    public function index()
    {
        $filters = UserFilter::all();

        return UserFilterResource::collection($filters);
    }

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

    public function update(Request $request, UserFilter $filter)
    {
        $isDefault = $request->get('is_default', false);
        $defaultQuery = UserFilter::where('user_id', $filter->user_id)
                ->where('is_default', true);

        $currentDefault = $defaultQuery->first();

        if (null !== $currentDefault && $currentDefault->id !== $filter->id) {
            $defaultQuery->update(['is_default' => false]);
        }

        $data = $request->only(['is_default', 'is_subscribed']);

        $filter->fill($data);
        $filter->save();

        return response()->json(['message' => 'Filter updated successfully'], 200);
    }
}
