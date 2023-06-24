<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Create a new comment.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'content' => 'required',
        ]);

        $user = $request->user();

        $comment = Comment::create([
            'user_id' => $user->id,
            'recipe_id' => $request->recipe_id,
            'content' => $request->content,
        ]);

        return response()->json([
            'comment' => $comment,
            'message' => 'Comment created successfully.',
        ], 201);
    }

    /**
     * Update the specified comment.
     *
     * @param  Request  $request
     * @param  Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'comment' => $comment,
            'message' => 'Comment updated successfully.',
        ], 200);
    }

    /**
     * Delete the specified comment.
     *
     * @param  Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ], 200);
    }
}