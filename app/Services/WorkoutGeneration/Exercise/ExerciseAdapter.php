<?php

namespace App\Services\WorkoutGeneration\Exercise;

use App\Models\Exercise;
use App\Services\WorkoutGeneration\Context\UserContext;
use App\Services\ExerciseLoadService;
use Illuminate\Support\Facades\Log;

//Это класс, который подстраивается под реакции пользователя
class ExerciseAdapter
{
    protected RestPhaseDetector $restPhaseDetector;
    protected ExerciseReplacementDecider $replacementDecider;
    protected AlternativeExerciseFinder $alternativeFinder;
    protected ExerciseParameterCalculator $parameterCalculator;
    protected ExerciseAdjuster $adjuster;
    protected ExerciseLoadService $loadService;

    public function __construct(
        RestPhaseDetector $restPhaseDetector,
        ExerciseReplacementDecider $replacementDecider,
        AlternativeExerciseFinder $alternativeFinder,
        ExerciseParameterCalculator $parameterCalculator,
        ExerciseAdjuster $adjuster,
        ExerciseLoadService $loadService
    ) {
        $this->restPhaseDetector = $restPhaseDetector;
        $this->replacementDecider = $replacementDecider;
        $this->alternativeFinder = $alternativeFinder;
        $this->parameterCalculator = $parameterCalculator;
        $this->adjuster = $adjuster;
        $this->loadService = $loadService;
    }

    /**
     * Адаптирует одно упражнение. Возвращает объект упражнения с заполненным pivot,
     * либо null, если упражнение исключено (фаза отдыха).
     */
    public function adapt(Exercise $exercise, UserContext $context): ?object
    {
        // 1. Фаза отдыха?
        $history = $context->getReactionHistory($exercise->id, 30);
        $rest = $this->restPhaseDetector->shouldRest($history);
        if ($rest) {
            Log::info("Упражнение {$exercise->id} исключено (фаза отдыха {$rest['duration']} дней) для пользователя {$context->user->id}");
            return null;
        }

        // 2. Проверка доступности оборудования
        $equipmentId = $context->parameters->equipment_id ?? null;
        if ($exercise->equipment_id && $exercise->equipment_id !== $equipmentId) {
            $alternative = $this->alternativeFinder->find($exercise, $equipmentId);
            if ($alternative) {
                return $this->buildAlternative($alternative, $exercise);
            }
            // Если альтернативы нет – оставляем исходное, но, возможно, нужно залогировать
        }

        // 3. Замена из-за плохих реакций
        $history14 = $context->getReactionHistory($exercise->id, 14);
        if ($this->replacementDecider->shouldReplace($history14)) {
            $alternative = $this->alternativeFinder->find($exercise, $equipmentId);
            if ($alternative) {
                return $this->buildAlternative($alternative, $exercise);
            }
        }

        // 4. Адаптация параметров
        $baseParams = $this->parameterCalculator->calculate($exercise, $context);
        $adjustedParams = $this->adjuster->adjust($exercise, $baseParams, $context);

        // 5. Клонируем и добавляем данные
        $adapted = clone $exercise;
        $adapted->pivot = (object) [
            'sets' => $adjustedParams['sets'],
            'reps' => $adjustedParams['reps'],
            'order_number' => $exercise->pivot->order_number,
        ];
        $adapted->user_weight = $context->getUserExerciseWeight($exercise->id);

        return $adapted;
    }

    protected function buildAlternative(Exercise $alternative, Exercise $original): object
    {
        $alternative->pivot = (object) [
            'sets' => $original->pivot->sets,
            'reps' => $original->pivot->reps,
            'order_number' => $original->pivot->order_number,
        ];
        $alternative->is_alternative = true;
        $alternative->original_exercise_id = $original->id;
        return $alternative;
    }
}
