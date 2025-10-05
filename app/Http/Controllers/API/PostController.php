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
use Illuminate\Support\Facades\Log;

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
            Log::error($e->getMessage());
            abort(500, 'Ошибка при получении статей');
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
            Log::error($e->getMessage());
            abort(500, 'Ошибка при создании статьи');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $post = $this->postService->findByIdWithRelations($id);

            if (!$post) {
                abort(404, 'Статья не найдена');
            }

            return response()->json([
                'success' => true,
                'data' => new PostResource($post)
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Ошибка при получении статьи');
        }
    }

    public function update(UpdatePostRequest $request, $id): JsonResponse
    {
        try {
            if (!$this->postService->checkOwnership($id, $request->user()->id)) {
                abort(403, 'У вас нет прав для редактирования этой статьи');
            }

            $updatedPost = $this->postService->updatePost($id, $request->validated());

            if (!$updatedPost) {
                abort(404, 'Статья не найдена');
            }

            return response()->json([
                'success' => true,
                'message' => 'Статья успешно обновлена!',
                'data' => new PostResource($updatedPost->load(['user', 'category']))
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Ошибка при обновлении статьи');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $post = $this->postService->findById($id);

            if (!$post) {
                abort(404, 'Статья не найдена');
            }

            if (!$this->postService->checkOwnership($id, request()->user()->id)) {
                abort(403, 'У вас нет прав для удаления этой статьи');
            }

            $result = $this->postService->deletePost($id);

            if (!$result) {
                abort(404, 'Статья не найдена');
            }

            return response()->json([
                'success' => true,
                'message' => 'Статья успешно удалена!'
            ], 200);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Ошибка при удалении статьи');
        }
    }
}
