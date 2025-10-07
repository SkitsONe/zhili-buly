<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getAllWithPostCount();

            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($categories)
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Ошибка при получении категорий.');
        }
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->createCategory($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно создана!',
                'data' => new CategoryResource($category)
            ], 201);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Ошибка при создании категории.');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->findByIdWithPosts($id);

            if (!$category) {
                abort(404, 'Категория не найдена');
            }

            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category)
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Ошибка при получении категории');
        }
    }

    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        try {
            $category = $this->categoryService->updateCategory($id, $request->validated());

            if (!$category) {
                abort(404, 'Категория не найдена');
            }

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно обновлена!',
                'data' => new CategoryResource($category)
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(400, 'Ошибка при обновлении категории');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->categoryService->deleteCategory($id);

            if (!$result) {
                abort(404, 'Категория не найдена');
            }

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно удалена!'
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Ошибка при удалении категории');
        }
    }

    public function available(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getAvailableCategories();

            return response()->json([
                'success' => true,
                'data' => CategoryResource::collection($categories)
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Ошибка при получении категорий');
        }
    }
}
