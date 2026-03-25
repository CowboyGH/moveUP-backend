<?php

namespace App\Http\Swagger\Paths\Admin;

/**
 * @OA\Get(
 *     path="/api/admin/exercises",
 *     summary="Получить список всех упражнений",
 *     description="Возвращает список всех упражнений с информацией об оборудовании и количеством тренировок. Поддерживает поиск и пагинацию",
 *     operationId="getExercisesList",
 *     tags={"Admin Exercises"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Поиск по названию, описанию или группе мышц",
 *         required=false,
 *         @OA\Schema(type="string", maxLength=100, example="жим")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Количество элементов на странице (1-100)",
 *         required=false,
 *         @OA\Schema(type="integer", default=10, minimum=1, maximum=100)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Номер страницы",
 *         required=false,
 *         @OA\Schema(type="integer", default=1, minimum=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Exercise")
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="last_page", type="integer", example=5),
 *                 @OA\Property(property="per_page", type="integer", example=10),
 *                 @OA\Property(property="total", type="integer", example=50),
 *                 @OA\Property(property="from", type="integer", example=1),
 *                 @OA\Property(property="to", type="integer", example=10)
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
class ExercisePaths {}

/**
 * @OA\Post(
 *     path="/api/admin/exercises",
 *     summary="Создать новое упражнение",
 *     description="Создает новое упражнение с изображением",
 *     operationId="createExercise",
 *     tags={"Admin Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  required={"equipment_id", "title", "description", "muscle_group", "image"},
 *                  @OA\Property(
 *                      property="equipment_id",
 *                      type="integer",
 *                      example=1,
 *                      description="ID оборудования (получить из GET /api/admin/equipments)"
 *                  ),
 *                  @OA\Property(property="title", type="string", example="Жим гантелей лежа", description="Название упражнения"),
 *                  @OA\Property(property="description", type="string", example="Базовое упражнение для развития грудных мышц", description="Описание упражнения"),
 *                  @OA\Property(property="muscle_group", type="string", example="Грудные", description="Группа мышц"),
 *                  @OA\Property(
 *                      property="image",
 *                      type="string",
 *                      format="binary",
 *                      description="Файл изображения (jpeg, png, jpg, gif, webp, макс. 5MB)"
 *                  )
 *              )
 *          )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Упражнение успешно создано",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Упражнение успешно создано"),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/Exercise"
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
class ExerciseStorePaths {}

/**
 * @OA\Get(
 *     path="/api/admin/exercises/{id}",
 *     summary="Получить упражнение по ID",
 *     description="Возвращает информацию о конкретном упражнении с тренировками, в которых оно используется",
 *     operationId="getExerciseById",
 *     tags={"Admin Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID упражнения",
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
 *                 ref="#/components/schemas/ExerciseWithWorkouts"
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
 *         description="Упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class ExerciseShowPaths {}

/**
 * @OA\Put(
 *     path="/api/admin/exercises/{id}",
 *     summary="Обновить упражнение",
 *     description="Обновляет существующее упражнение. Для обновления изображения используйте multipart/form-data",
 *     operationId="updateExercise",
 *     tags={"Admin Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="ID упражнения",
 *          @OA\Schema(type="integer", example=1)
 *      ),
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\MediaType(
 *              mediaType="multipart/form-data",
 *              @OA\Schema(
 *                  @OA\Property(
 *                      property="equipment_id",
 *                      type="integer",
 *                      example=1,
 *                      description="ID оборудования (получить из GET /api/admin/equipments)"
 *                  ),
 *                  @OA\Property(property="title", type="string", example="Жим гантелей лежа", description="Название упражнения"),
 *                  @OA\Property(property="description", type="string", example="Базовое упражнение для развития грудных мышц", description="Описание упражнения"),
 *                  @OA\Property(property="muscle_group", type="string", example="Грудные", description="Группа мышц"),
 *                  @OA\Property(
 *                      property="image",
 *                      type="string",
 *                      format="binary",
 *                      description="Новый файл изображения (опционально)"
 *                  )
 *              )
 *          )
 *      ),
 *     @OA\Response(
 *         response=200,
 *         description="Упражнение успешно обновлено",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Упражнение успешно обновлено"),
 *             @OA\Property(
 *                 property="data",
 *                 ref="#/components/schemas/Exercise"
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
 *         description="Упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class ExerciseUpdatePaths {}

/**
 * @OA\Delete(
 *     path="/api/admin/exercises/{id}",
 *     summary="Удалить упражнение",
 *     description="Удаляет упражнение, если оно не используется в тренировках и нет результатов тестов",
 *     operationId="deleteExercise",
 *     tags={"Admin Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID упражнения",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Упражнение успешно удалено",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Упражнение успешно удалено")
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
 *         description="Упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Нельзя удалить упражнение, которое используется в тренировках или есть результаты тестов",
 *         @OA\JsonContent(ref="#/components/schemas/ConflictResponse")
 *     )
 * )
 */
class ExerciseDestroyPaths {}

/**
 * @OA\Post(
 *     path="/api/admin/exercises/{id}/image",
 *     summary="Загрузить изображение для упражнения",
 *     description="Загружает новое изображение для существующего упражнения (старое изображение удаляется)",
 *     operationId="uploadExerciseImage",
 *     tags={"Admin Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID упражнения",
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
 *                 @OA\Property(property="image", type="string", example="exercises/exercise_1_1234567890.jpg"),
 *                 @OA\Property(property="image_url", type="string", example="http://localhost/storage/exercises/exercise_1_1234567890.jpg")
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
 *         description="Упражнение не найдено",
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
class ExerciseUploadImagePaths {}

/**
 * @OA\Get(
 *     path="/api/admin/exercises/{id}/image",
 *     summary="Получить изображение упражнения",
 *     description="Возвращает файл изображения упражнения",
 *     operationId="getExerciseImage",
 *     tags={"Admin Exercises"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID упражнения",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Изображение упражнения",
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
 *         @OA\JsonContent(ref="#/components/schemas/NotFoundResponse")
 *     )
 * )
 */
class ExerciseGetImagePaths {}
