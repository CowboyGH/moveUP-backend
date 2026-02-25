<?php

namespace App\Http\Requests\Admin\Workout;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phase_id' => 'nullable|exists:phases,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'duration_minutes' => 'sometimes|integer|min:1|max:300',
            'is_active' => 'sometimes|boolean',
            'exercises' => 'sometimes|array',
            'exercises.*.exercise_id' => 'required_with:exercises|exists:exercises,id',
            'exercises.*.sets' => 'required_with:exercises|integer|min:1|max:100',
            'exercises.*.reps' => 'required_with:exercises|string|max:50',
            'exercises.*.order_number' => 'required_with:exercises|integer|min:1',
            'warmups' => 'sometimes|array',
            'warmups.*.warmup_id' => 'required_with:warmups|exists:warmups,id',
            'warmups.*.order_number' => 'required_with:warmups|integer|min:1',
        ];
    }
}
