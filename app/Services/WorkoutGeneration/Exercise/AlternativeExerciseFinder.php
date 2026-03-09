<?php

namespace App\Services\WorkoutGeneration\Exercise;

use App\Models\Exercise;

class AlternativeExerciseFinder
{
    /**
     * @param Exercise $original
     * @param int $equipmentId доступное оборудование пользователя
     * @param array $excludeIds ID упражнений, которые исключаем (например, в фазе отдыха)
     * @return Exercise|null
     */
    public function find(Exercise $original, int $equipmentId, array $excludeIds = []): ?Exercise
    {
        $query = Exercise::where('muscle_group', $original->muscle_group)
            ->where('equipment_id', $equipmentId)
            ->where('id', '!=', $original->id);

        if (!empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        return $query->inRandomOrder()->first();
    }
}
