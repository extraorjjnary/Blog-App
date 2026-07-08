<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
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
    public function update(Request $request, Comment $comment)
    {
        $user = $request->user();
        $guestName = $request->input('guest_name');

        if ($user) {
            $this->authorize('update', $comment);
        } else {
            if ($guestName !== $comment->guest_name) {
                return response()->json(['messsage' => 'Unauthorized'], 403);
            }
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:3']
        ]);


        $comment->update([
            'content' => $validated['content']
        ]);

        $comment->load('user');

        return response()->json($comment, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Comment $comment)
    {
        $user = $request->user();
        $guestName = $request->input('guest_name');

        if ($user) {
            $this->authorize('delete', $comment);
        } else {
            if ($guestName !== $comment->guest_name) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $comment->delete();

        return response()->noContent();
    }
}
