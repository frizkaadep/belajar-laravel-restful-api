<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        // Validate the request data
        $data = $request->validated();

        if (User::where('username', $data['username'])->count() == 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'username' => ['Username already exists. Please choose another one.']
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']); // Hash the password
        $user->save(); // Save the user to the database

        return( new UserResource($user))->response()
            ->setStatusCode(201); // HTTP status code 201 Created
    }

    public function login(Request $request): JsonResponse
    {
        // Validate the request data
        $data = $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:100',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => ['username or password is incorrect.']
                ]
            ], 400));
        }

        $user->token = Str::uuid()->toString();
        $user->save(); // Save the token to the user

        // Here you would typically generate a token for the user
        // For simplicity, we will just return the user data
        return( new UserResource($user))->response()
            ->setStatusCode(200);
    }

    public function get(Request $request): JsonResponse
    {
        $user = Auth::user();
        // return new UserResource($user);
        return (new UserResource($user))->response()
            ->setStatusCode(200); // HTTP status code 200 OK
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();
        $user = Auth::user();

        if (!$user instanceof \App\Models\User) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => ['Authenticated user not found or invalid.']
                ]
            ], 404));
        }

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']); // Hash the new password
        }

        $user->save();
        return new UserResource($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        // dd(get_class($user)); // Harusnya "App\Models\User"
        if ($user instanceof \App\Models\User) {
            $user->token = null; // Clear the token
            $user->save(); // Save the changes to the user
        } else {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => ['Authenticated user not found or invalid.']
                ]
            ], 404));
        }

        return response()->json(['data' => true])->setStatusCode(200); // HTTP status code 200 OK
    }
}
