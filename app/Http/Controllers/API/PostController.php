<?php

namespace App\Http\Controllers\API;

use App\Dto\PostDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Policies\PostPolicy;
use App\Services\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService,
        private PostPolicy $postPolicy,
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
            abort(400, 'Error when receiving the post');
        }
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        try {
            $postData = PostDto::fromRequest($request->validated(), $request->user()->id);
            $post = $this->postService->createPost($postData);
            return response()->json(
                new PostResource($post),
                201
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(400, 'Error in create post');
        }
    }

    public function show(int $postId): JsonResponse
    {
        try {
            $post = $this->postService->findByIdWithRelations($postId);

            abort_if(!$post, 404);

            return response()->json(
                new PostResource($post)
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(400, 'Error when receiving the post');
        }
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        try {
            abort_if(!$this->postPolicy->update($request->user(), $post), 403);

            $postData = PostDto::fromRequest($request->validated(), $post->user_id);

            $updatedPost = $this->postService->updatePost($post, $postData);

            return response()->json(
                new PostResource($updatedPost)
            );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(400, 'Error in update post');
        }
    }

    public function destroy(Post $post): JsonResponse
    {
        try {
            abort_if(!$this->postPolicy->delete(request()->user(), $post), 403);

            $result = $this->postService->deletePost($post);

            abort_if(!$result, 404);

            return response()->json(null, 204);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(400, 'Error in delete post');
        }
    }
}
