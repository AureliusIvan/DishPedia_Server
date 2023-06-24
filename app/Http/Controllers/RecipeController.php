<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class RecipeController extends Controller
{
    public function index()
    {
        try {
            $recipes = Recipe::all();
            
            return response()->json([
                'recipes' => $recipes,
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

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'ingredients' => 'required',
                'preparation_steps' => 'required',
                'image' => 'required',
            ]);

            $recipe = Recipe::create([
                'user_id' => auth()->user()->id,
                'title' => $request->title,
                'description' => $request->description,
                'ingredients' => $request->ingredients,
                'preparation_steps' => $request->preparation_steps,
                'image' => $request->image,
            ]);

            return response()->json([
                'recipe' => $recipe,
                'message' => 'Recipe created successfully.',
            ], 201);
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

    public function show(Recipe $recipe)
    {
        try {
            return response()->json([
                'recipe' => $recipe,
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


    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'ingredients' => 'required',
            'preparation_steps' => 'required',
            'image' => 'required',
        ]);

        $recipe->update([
            'title' => $request->title,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'preparation_steps' =>  $request->preparation_steps,
            'image' => $request->image,
        ]);

        // $recipe->save();

        return response()->json([
            'recipe' => $recipe,
            'message' => 'Recipe updated successfully.',
        ], 200);
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return response()->json([
            'message' => 'Recipe deleted successfully.',
        ], 200);
    }
}