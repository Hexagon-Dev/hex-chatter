<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;

class UserController extends Controller
{
    public function me(): JsonResponse
    {
        $user = auth()->user();

        if (is_null($user)) {
            throw new UnauthorizedException();
        }

        return response()->json(UserResource::make($user));
    }

    public function index(string $search = null): JsonResponse
    {
        $users = User::query();

        if (!is_null($search)) {
            $users->where('name', 'like', "%$search%");
        }

        return response()->json(UserResource::collection($users->get()));
    }
}
