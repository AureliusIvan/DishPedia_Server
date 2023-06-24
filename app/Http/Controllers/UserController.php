<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\User;

class UserController extends Controller
{

    public function register(Request $request)
    {
        try {
            $fields = $request->validate([
                'name' => 'required|string',
                // unique to users table and email field
                'email' => 'required|string|unique:users,email',
                // confirmed to check if password and password_confirmation are same
                'password' => 'required|string|confirmed'
            ]);

            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password'])
            ]);

            $token = $user->createToken('myAppToken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = User::where('email', $credentials['email'])->first();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You are not authorized to access this resource.'], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }

    public function getUser(Request $request)
    {
        try {
            $user = $request->user();
            return response()->json(['user' => $user], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You are not authorized to access this resource.'], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }


    public function updateUser(Request $request)
    {
        try {
            $user = $request->user();
            
            // Update the user details
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            
            // Check if a new password is provided and update it
            $password = $request->input('password');
            if ($password) {
                $user->password = Hash::make($password);
            }

            // Save the updated user
            $user->save();

            // Return a success response
            return response()->json(['message' => 'User updated successfully'], 200);
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


    public function deleteUser(Request $request)
    {
        $user = $request->user();

        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}