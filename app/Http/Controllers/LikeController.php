<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class LikeController extends Controller
{
    public function toggleLike(Request $request)
    {
        try {
            $request->validate([
                'recipe_id' => 'required|exists:recipes,id',
            ]);

            $user = $request->user();

            $like = Like::where('user_id', $user->id)
                ->where('recipe_id', $request->recipe_id)
                ->first();
            if ($like) {
                $like->delete();
                $message = 'Like removed.';
            } else {
                $like = Like::create([
                    'user_id' => $user->id,
                    'recipe_id' => $request->recipe_id,
                ]);
                $message = 'Like added.';
            }

            return response()->json([
                'like' => $like,
                'message' => $message,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You are not authorized to access this resource.'], 403);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong. Please try again.',
                $e->getMessage()
            ], 500);
        }
    }
}