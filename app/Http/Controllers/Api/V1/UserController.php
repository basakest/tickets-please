<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters): AnonymousResourceCollection
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse|UserResource
    {
        try {
            $this->isAble('store', User::class);
            $user = User::create($request->mappedAttributes());
            return UserResource::make($user);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to create this resource', 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): UserResource
    {
        if ($this->include('tickets')) {
            return UserResource::make($user->load('tickets'));
        }
        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(User $user, UpdateUserRequest $request): JsonResponse|UserResource
    {
        try {
            $this->isAble('update', User::class);
            $user->update($request->mappedAttributes());
            return UserResource::make($user);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this resource', 403);
        }
    }

    public function replace(User $user, ReplaceUserRequest $request): JsonResponse|UserResource
    {
        try {
            $this->isAble('replace', User::class);
            $user->update($request->mappedAttributes());
            return UserResource::make($user);
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to update this resource', 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            $this->isAble('delete', User::class);
            $user->delete();
            return $this->ok('User successfully deleted');
        } catch (AuthorizationException $e) {
            return $this->error('You are not authorized to delete this resource', 403);
        }
    }
}
