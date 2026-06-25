<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $posts = Post::with('user')
            ->withCount([
                'comments',
                'reactions as upvotes_count' => fn($q) => $q->where('reaction_type', 'upvote'),
                'reactions as downvotes_count' => fn($q) => $q->where('reaction_type', 'downvote')
            ])
            ->latest()
            ->simplePaginate(10);

        // dd(collect($posts)->toArray());


        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $validatedData = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:3']
        ]);

        $user = $request->user();

        $post = $user->posts()->create($validatedData);

        $post->load('user')->loadCount('comments', 'reactions');




        return response()->json([
            'message' => "Post created successfully",
            'post' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {

        $post->load(['user', 'comments.user', 'reactions.user']);

        $post->loadCount([
            "reactions as upvotes_count" => fn($q) =>
            $q->where('reaction_type', 'upvote'),
            "reactions as downvotes_count" => fn($q) =>
            $q->where('reaction_type', 'downvote'),
        ]);

        // dd(collect($post)->toArray());



        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {

        $this->authorize('update', $post);

        $validatedData = $request->validate([
            'title' => ['string', 'min:3', 'max:255'],
            'content' => ['string', 'min:3'],
        ]);

        $post->update($validatedData);
        $post->load(['user', 'comments.user', 'reactions.user']);

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->noContent();
    }
}
