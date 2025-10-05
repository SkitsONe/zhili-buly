<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $posts = $this->postService->getAllWithFilters($request->all());

            return response()->json([
                'success' => true,
                'data' => PostResource::collection($posts),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении статей',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $post = $this->postService->createPost($request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Статья успешно создана!',
                'data' => new PostResource($post->load(['user', 'category']))
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании статьи',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $post = $this->postService->findByIdWithRelations($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Статья не найдена'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new PostResource($post)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении статьи',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdatePostRequest $request, $id): JsonResponse
    {
        try {
            if (!$this->postService->checkOwnership($id, $request->user()->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для редактирования этой статьи'
                ], 403);
            }

            $updatedPost = $this->postService->updatePost($id, $request->validated());

            if (!$updatedPost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Статья не найдена'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Статья успешно обновлена!',
                'data' => new PostResource($updatedPost->load(['user', 'category']))
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении статьи',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $post = $this->postService->findById($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Статья не найдена'
                ], 404);
            }

            if (!$this->postService->checkOwnership($id, request()->user()->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав для удаления этой статьи'
                ], 403);
            }

            $result = $this->postService->deletePost($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Статья не найдена'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Статья успешно удалена!'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении статьи',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
