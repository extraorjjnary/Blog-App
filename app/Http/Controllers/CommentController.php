<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'guest_name' => ['nullable', 'string'],
            'content' => ['required', 'string', 'min:3']
        ]);

        $user = $request->user();
        $guestName = $validated['guest_name'] ?? null;

        $comment = $post->comments()->create([
            'user_id' => $user?->id,
            'guest_name' => $guestName,
            'content' => $validated['content']
        ]);

        $comment->load('user');

        return response()->json($comment, 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
