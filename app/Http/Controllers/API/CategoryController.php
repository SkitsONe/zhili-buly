<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
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
                'data' => CategoryResource::collection($categories)
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error when getting the category.');
        }
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = $this->categoryService->createCategory($request->validated());

            return response()->json([
                'data' => new CategoryResource($category)
            ], 201);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error create category.');
        }
    }

    public function show(int $categoryId): JsonResponse
    {
        try {
            $category = $this->categoryService->findByIdWithPosts($categoryId);

            abort_if(!$category, 404);

            return response()->json([
                'data' => new CategoryResource($category)
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error when getting the category');
        }
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $result = $this->categoryService->updateCategory($category, $request->validated());

            abort_if(!$result, 404);

            return response()->json([
                'data' => new CategoryResource($category)
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(400, 'Error in update category');
        }
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            $result = $this->categoryService->deleteCategory($category);

            abort_if(!$result, 404);

            return response()->json([
                'success' => true,
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error in delete category');
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
            abort(500, 'Error when getting the category');
        }
    }
}
