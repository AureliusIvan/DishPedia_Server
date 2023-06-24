<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'categories' => $categories,
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
                'name' => 'required|unique:categories',
                // Add more validation rules for your category fields
            ]);

            $category = Category::create([
                'name' => $request->name,
                // Assign other fields from the request to the category model
            ]);

            return response()->json([
                'category' => $category,
                'message' => 'Category created successfully.',
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

    public function show(Category $category)
    {
        return response()->json([
            'category' => $category,
        ], 200);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
            // Add more validation rules for your category fields
        ]);

        $category->update([
            'name' => $request->name,
            // Update other fields from the request in the category model
        ]);

        return response()->json([
            'category' => $category,
            'message' => 'Category updated successfully.',
        ], 200);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}