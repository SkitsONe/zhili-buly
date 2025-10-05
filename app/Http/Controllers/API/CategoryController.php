<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении категорий',
                'error' => $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании категории',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $category = $this->categoryService->findByIdWithPosts($id);

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена',
                    'available_categories' => $this->categoryService->getAvailableCategories()
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении категории',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        try {
            $category = $this->categoryService->updateCategory($id, $request->validated());

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно обновлена!',
                'data' => new CategoryResource($category)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении категории',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->categoryService->deleteCategory($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Категория не найдена'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Категория успешно удалена!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении категории',
                'error' => $e->getMessage()
            ], 500);
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении категорий',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
