<?php

namespace App\Http\Controllers;

use App\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite for a recipe.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
        ]);

        $user = $request->user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('recipe_id', $request->recipe_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Favorite removed.';
        } else {
            $favorite = Favorite::create([
                'user_id' => $user->id,
                'recipe_id' => $request->recipe_id,
            ]);
            $message = 'Favorite added.';
        }

        return response()->json([
            'favorite' => $favorite,
            'message' => $message,
        ], 200);
    }
}