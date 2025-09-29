<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        $users = User::skip($offset)->take($limit)->get();
        $total = User::count();

        return response()->json([
            'users' => $users,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users',
                'name' => 'required|string|max:255',
                'avatar' => 'nullable|url',
                'role' => 'nullable|in:admin,moderator,user,guest',
            ]);

            $user = User::create([
                'email' => $validated['email'],
                'name' => $validated['name'],
                'avatar' => $validated['avatar'] ?? null,
                'role' => $validated['role'] ?? 'user',
                'is_active' => true,
                'password' => bcrypt('password'), // Default password
            ]);

            return response()->json($user, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation failed',
                'errors' => collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'message' => $messages[0]
                    ];
                })->values()
            ], 422);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => 'User not found'
            ], 404);
        }

        try {
            $validated = $request->validate([
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'name' => 'sometimes|string|max:255',
                'avatar' => 'nullable|url',
                'role' => 'nullable|in:admin,moderator,user,guest',
                'is_active' => 'sometimes|boolean',
            ]);

            $user->update($validated);

            return response()->json($user);
        } catch (ValidationException $e) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation failed',
                'errors' => collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'message' => $messages[0]
                    ];
                })->values()
            ], 422);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json(null, 204);
    }
}
