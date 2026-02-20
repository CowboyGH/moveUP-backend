<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\ErrorResponse;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('testings')->get();
        return ApiResponse::data($categories);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());
        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'testings_count' => 0,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
        return ApiResponse::success('Категория успешно создана',$data, 201);
    }

    public function show(int $id): JsonResponse
    {
        $category = Category::with('testings')->find($id);
        if (!$category) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Категория не найдена',
                404
            );
        }
        return ApiResponse::data($category);
    }
    //НУЖНО ПОДУМАТЬ ПО ПОВОДУ ЭТОЙ ФУНКЦИИ, ЕСТЬ ЛИ СМЫСЛ ЕЕ ОСТАВЛЯТЬ
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Категория не найдена',
                404
            );
        }
        $category->update($request->validated());
        return ApiResponse::success('Категория успешно обновлена',$category);
    }
    public function destroy(int $id): JsonResponse
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error(
                ErrorResponse::NOT_FOUND,
                'Категория не найдена',
                404
            );
        }
        if($category->testings()->exists()){
            return ApiResponse::error(
                ErrorResponse::CONFLICT,
                'Нельзя удалить категорию, к которой привязаны тесты',
                422
            );
        }
        $category->delete();
        return ApiResponse::success('Категория успешно удалена');
    }
}
