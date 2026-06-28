<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Reaction;

class ReactionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Post $post)
    {

        // Validate first the incoming request

        $validated = $request->validate([
            'guest_identifier' => ['nullable', 'string'],
            'reaction_type' => ['required', 'in:upvote,downvote']
        ]);

        $user = $request->user();
        $guestId = $validated['guest_identifier'] ?? null;

        // Require either auth user or guest identifier

        if (!$user && !$guestId) {
            return response()->json(['message' => 'Unauthorized or missing guest token'], 401);
        }

        // Get existing reaction if it exists

        $existingReaction = Reaction::where('post_id', $post->id)
            ->when($user, fn($query) => $query->where('user_id', $user->id))
            ->when(!$user && $guestId, fn($query) => $query->where('guest_identifier', $guestId))
            ->first();


        // upsert logic

        $currentReaction = null;

        if ($existingReaction) {
            // if find the same reaction, toggle off
            if ($validated['reaction_type'] === $existingReaction->reaction_type) {
                $existingReaction->delete();
                $currentReaction = null;
            } else {
                // otherwise update only the reaction type
                $existingReaction->update([
                    'reaction_type' => $validated['reaction_type']
                ]);

                $currentReaction = $validated['reaction_type']; // ← switched reaction
            }
        } else {
            // if there is no existing reaction yet, create a brand new reaction associate with: post, current authuser/guest

            $post->reactions()->create([
                'user_id' => $user?->id,
                'guest_identifier' => $guestId,
                'reaction_type' => $validated['reaction_type']
            ]);

            $currentReaction = $validated['reaction_type']; // ← brand new reaction
        }

        $post->loadCount([
            "reactions as upvotes_count" =>
            fn($q) =>
            $q->where('reaction_type', 'upvote'),
            "reactions as downvotes_count" =>
            fn($q) =>
            $q->where('reaction_type', 'downvote'),
        ]);


        return response()->json([
            'upvotes_count' => $post->upvotes_count,
            'downvotes_count' => $post->downvotes_count,
            'user_reaction' => $currentReaction
        ], 200);
    }
}
