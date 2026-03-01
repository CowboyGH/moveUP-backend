<?php

namespace Database\Factories;

use App\Models\Testing;
use App\Models\TestingExercise;
use App\Models\TestingTestExercise;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestingTestExerciseFactory extends Factory
{
    /**
     * Хранит все использованные комбинации (testing_id => [exercise_id => true]).
     * Инициализируется существующими записями из БД и пополняется новыми.
     */
    protected static array $usedCombinations = [];

    protected static bool $initialized = false;

    /**
     * Загружает существующие связи из таблицы в статическое свойство.
     */
    protected static function initializeUsedCombinations(): void
    {
        if (self::$initialized) {
            return;
        }

        // Загружаем все существующие записи
        $existing = TestingTestExercise::all(['testing_id', 'testing_exercise_id']);
        foreach ($existing as $record) {
            self::$usedCombinations[$record->testing_id][$record->testing_exercise_id] = true;
        }

        self::$initialized = true;
    }

    /**
     * Определение данных по умолчанию для фабрики.
     */
    public function definition(): array
    {
        self::initializeUsedCombinations();

        static $orderNumbers = []; // для генерации order_number в рамках одного запуска

        // 1. Выбираем подходящий testing_id (с числом связей < 6)
        $testingId = $this->getAvailableTestingId();

        // 2. Выбираем уникальное для этого теста testing_exercise_id
        $testingExerciseId = $this->getAvailableExerciseId($testingId);

        // 3. Определяем order_number
        if (!isset($orderNumbers[$testingId])) {
            // Для первого добавления к тесту берём максимальный существующий номер + 1
            $maxOrder = TestingTestExercise::where('testing_id', $testingId)->max('order_number') ?? 0;
            $orderNumbers[$testingId] = $maxOrder + 1;
        } else {
            $orderNumbers[$testingId]++;
        }

        // 4. Запоминаем новую комбинацию, чтобы следующие вызовы её учитывали
        self::$usedCombinations[$testingId][$testingExerciseId] = true;

        return [
            'testing_id'          => $testingId,
            'testing_exercise_id' => $testingExerciseId,
            'order_number'        => $orderNumbers[$testingId],
        ];
    }

    /**
     * Выбирает ID теста, у которого ещё менее 6 связанных упражнений.
     * При необходимости создаёт новый тест.
     */
    protected function getAvailableTestingId(): int
    {
        // Сначала ищем среди уже известных (из БД и созданных в этом запуске)
        $candidates = [];
        foreach (self::$usedCombinations as $id => $exercises) {
            if (count($exercises) < 6) {
                $candidates[] = $id;
            }
        }
        if (!empty($candidates)) {
            return $candidates[array_rand($candidates)];
        }

        // Если таких нет, пробуем найти в БД тест, у которого реально меньше 6 записей
        // (например, тест без записей ещё не попал в $usedCombinations)
        $testing = Testing::withCount('testingTestExercises')
            ->having('testing_test_exercises_count', '<', 6)
            ->inRandomOrder()
            ->first();

        if ($testing) {
            // Загружаем его существующие связи (их может и не быть) в $usedCombinations
            $existingForTesting = TestingTestExercise::where('testing_id', $testing->id)->get();
            foreach ($existingForTesting as $record) {
                self::$usedCombinations[$testing->id][$record->testing_exercise_id] = true;
            }
            return $testing->id;
        }

        // Если ничего не нашли — создаём новый тест
        return Testing::factory()->create()->id;
    }

    /**
     * Выбирает ID упражнения, которое ещё не использовано для указанного теста.
     * Если все существующие упражнения уже заняты, создаёт новое.
     */
    protected function getAvailableExerciseId(int $testingId): int
    {
        $used = self::$usedCombinations[$testingId] ?? [];

        // Пытаемся найти случайное упражнение, не входящее в список использованных
        $available = TestingExercise::whereNotIn('id', array_keys($used))
            ->inRandomOrder()
            ->first();

        if ($available) {
            return $available->id;
        }

        // Если свободных упражнений нет — создаём новое
        return TestingExercise::factory()->create()->id;
    }
}
