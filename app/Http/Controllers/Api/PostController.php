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


    public function myPosts(Request $request)
    {
        $posts = $request->user()->posts()
            ->with('user', 'category')
            ->withCount([
                'comments',
                'reactions as upvotes_count' => fn($q) => $q->where('reaction_type', 'upvote'),
                'reactions as downvotes_count' => fn($q) => $q->where('reaction_type', 'downvote')
            ])
            ->latest()
            ->simplePaginate(10);


        return response()->json($posts, 200);
    }

    public function index(Request $request)
    {

        $limit = $request->query('limit', 10);

        $posts = Post::with('user', 'category')
            ->withCount([
                'comments',
                'reactions as upvotes_count' => fn($q) => $q->where('reaction_type', 'upvote'),
                'reactions as downvotes_count' => fn($q) => $q->where('reaction_type', 'downvote')
            ])
            ->latest()
            ->simplePaginate($limit);

        // dd(collect($posts)->toArray());


        return response()->json($posts, 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'content' => ['required', 'string', 'min:3'],
            'category_id' => ['required', 'integer', 'exists:categories,id']
        ]);

        $user = $request->user();

        $post = $user->posts()->create($validatedData);

        $post->load('user')->loadCount('comments', 'reactions');




        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {

        $post->load(['user', 'category', 'comments.user', 'reactions.user']);

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
            'category_id' => ['required', 'integer', 'exists:categories,id']

        ]);

        $post->update($validatedData);
        $post->load(['user', 'category', 'comments.user', 'reactions.user']);

        return response()->json($post, 200);
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
