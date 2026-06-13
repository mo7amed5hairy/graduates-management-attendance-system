<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PointsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1|max:999999',
            'type' => 'required|in:add,deduct',
            'reason' => 'nullable|string|max:500',
            'task_id' => 'nullable|exists:tasks,id',
        ];
    }
}
