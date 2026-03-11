<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/workouts",
 *     summary="Получить список всех тренировок с фильтрацией",
 *     description="Возвращает список всех тренировок с детальной информацией. Поддерживает поиск, фильтрацию и пагинацию",
 *     operationId="getWorkoutsList",
 *     tags={"Admin Workouts"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Поиск по названию и описанию тренировки",
 *         required=false,
 *         @OA\Schema(type="string", maxLength=100, example="утренняя")
 *     ),
 *     @OA\Parameter(
 *         name="phase_id",
 *         in="query",
 *         description="Фильтр по ID фазы",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="duration_min",
 *         in="query",
 *         description="Минимальная длительность в минутах",
 *         required=false,
 *         @OA\Schema(type="string", minimum=1, example=20)
 *     ),
 *     @OA\Parameter(
 *         name="duration_max",
 *         in="query",
 *         description="Максимальная длительность в минутах",
 *         required=false,
 *         @OA\Schema(type="string", minimum=1, example=60)
 *     ),
 *     @OA\Parameter(
 *         name="exercises_count_min",
 *         in="query",
 *         description="Минимальное количество упражнений",
 *         required=false,
 *         @OA\Schema(type="integer", minimum=0, example=3)
 *     ),
 *     @OA\Parameter(
 *         name="exercises_count_max",
 *         in="query",
 *         description="Максимальное количество упражнений",
 *         required=false,
 *         @OA\Schema(type="integer", minimum=0, example=10)
 *     ),
 *     @OA\Parameter(
 *         name="is_active",
 *         in="query",
 *         description="Фильтр по статусу активности",
 *         required=false,
 *         @OA\Schema(type="boolean", example=true)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Количество элементов на странице (1-100)",
 *         required=false,
 *         @OA\Schema(type="integer", default=15, minimum=1, maximum=100)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Номер страницы",
 *         required=false,
 *         @OA\Schema(type="integer", default=1, minimum=1)
 *     ),
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         description="Поле для сортировки",
 *         required=false,
 *         @OA\Schema(type="string", enum={"id", "title", "duration_minutes", "is_active", "created_at", "updated_at"}, default="created_at")
 *     ),
 *     @OA\Parameter(
 *         name="sort_dir",
 *         in="query",
 *         description="Направление сортировки",
 *         required=false,
 *         @OA\Schema(type="string", enum={"asc", "desc"}, default="desc")
 *     ),
 *     @OA\Parameter(
 *         name="date_from",
 *         in="query",
 *         description="Начальная дата создания (Y-m-d)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-01-01")
 *     ),
 *     @OA\Parameter(
 *         name="date_to",
 *         in="query",
 *         description="Конечная дата создания (Y-m-d)",
 *         required=false,
 *         @OA\Schema(type="string", format="date", example="2026-12-31")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="title", type="string", example="Утренняя зарядка"),
 *                     @OA\Property(property="description", type="string", example="Комплекс упражнений для пробуждения"),
 *                     @OA\Property(property="duration_minutes", type="string", example=30),
 *                     @OA\Property(property="image", type="string", nullable=true, example="workouts/morning-workout.jpg"),
 *                     @OA\Property(property="image_url", type="string", nullable=true, example="http://localhost/storage/workouts/morning-workout.jpg"),
 *                     @OA\Property(property="is_active", type="boolean", example=true),
 *                     @OA\Property(
 *                         property="phase",
 *                         type="object",
 *                         nullable=true,
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="name", type="string", example="Начальный уровень")
 *                     ),
 *                     @OA\Property(property="exercises_count", type="integer", example=5),
 *                     @OA\Property(property="warmups_count", type="integer", example=2),
 *                     @OA\Property(property="user_workouts_count", type="integer", example=15),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=5),
 *                 @OA\Property(property="per_page", type="integer", example=15),
 *                 @OA\Property(property="total", type="integer", example=75),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="to", type="integer", example=15)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации параметров",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class WorkoutPaths {}

/**
 * @OA\Post(
 *     path="/api/admin/workouts",
 *     summary="Создать новую тренировку",
 *     description="Создает новую тренировку с упражнениями и разминками",
 *     operationId="createWorkout",
 *     tags={"Admin Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StoreWorkoutRequest")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Тренировка успешно создана",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тренировка успешно создана"),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/Workout"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class WorkoutStorePaths {}

/**
 * @OA\Get(
 *     path="/api/admin/workouts/{id}",
 *     summary="Получить тренировку по ID",
 *     description="Возвращает детальную информацию о тренировке с упражнениями и разминками",
 *     operationId="getWorkoutById",
 *     tags={"Admin Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тренировки",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/Workout"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class WorkoutShowPaths {}

/**
 * @OA\Put(
 *     path="/api/admin/workouts/{id}",
 *     summary="Обновить тренировку",
 *     description="Обновляет существующую тренировку. Для обновления изображения используйте multipart/form-data",
 *     operationId="updateWorkout",
 *     tags={"Admin Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тренировки",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="phase_id", type="integer", nullable=true, example=1, description="ID фазы"),
 *                 @OA\Property(property="title", type="string", example="Утренняя зарядка", description="Название тренировки"),
 *                 @OA\Property(property="description", type="string", example="Комплекс упражнений для пробуждения", description="Описание тренировки"),
 *                 @OA\Property(property="duration_minutes", type="string", example=30, description="Длительность в минутах"),
 *                 @OA\Property(property="is_active", type="boolean", example=true, description="Активность тренировки"),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="Новый файл изображения (опционально)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тренировка успешно обновлена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тренировка успешно обновлена"),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/Workout"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class WorkoutUpdatePaths {}

/**
 * @OA\Delete(
 *     path="/api/admin/workouts/{id}",
 *     summary="Удалить тренировку",
 *     description="Удаляет тренировку, если она не была назначена пользователям",
 *     operationId="deleteWorkout",
 *     tags={"Admin Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тренировки",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Тренировка успешно удалена",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Тренировка успешно удалена")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Нельзя удалить тренировку, которая уже была назначена пользователям",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     )
 * )
 */
class WorkoutDestroyPaths {}

/**
 * @OA\Post(
 *     path="/api/admin/workouts/{id}/image",
 *     summary="Загрузить изображение для тренировки",
 *     description="Загружает новое изображение для существующей тренировки (старое изображение удаляется)",
 *     operationId="uploadWorkoutImage",
 *     tags={"Admin Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тренировки",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"image"},
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="Файл изображения (jpeg, png, jpg, gif, webp, макс. 5MB)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Изображение успешно загружено",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Изображение успешно загружено"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="image", type="string", example="workouts/workout_1_1234567890.jpg"),
 *                 @OA\Property(property="image_url", type="string", example="http://localhost/storage/workouts/workout_1_1234567890.jpg")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Тренировка не найдена",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class WorkoutUploadImagePaths {}

/**
 * @OA\Get(
 *     path="/api/admin/workouts/{id}/image",
 *     summary="Получить изображение тренировки",
 *     description="Возвращает файл изображения тренировки",
 *     operationId="getWorkoutImage",
 *     tags={"Admin Workouts"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID тренировки",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Изображение тренировки",
 *         @OA\MediaType(
 *             mediaType="image/*",
 *             @OA\Schema(type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Неавторизован",
 *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedResponse")
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Доступ запрещен (только для администраторов)",
 *         @OA\JsonContent(ref="#/components/schemas/ForbiddenResponse")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Изображение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/ImageNotFoundResponse")
 *     )
 * )
 */
class WorkoutGetImagePaths {}
