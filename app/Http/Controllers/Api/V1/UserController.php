<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    /**
     * Get all users
     *
     * @group      Managing Users
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=name
     * @queryParam filter[name] Filter by status name. Wildcards are supported. No-example
     * @queryParam filter[email] Filter by email. Wildcards are supported. No-example
     */
    public function index(AuthorFilter $filters): AnonymousResourceCollection
    {
        return UserResource::collection(User::filter($filters)->paginate());
    }

    /**
     * Create a user
     *
     * @group    Managing Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
     */
    public function store(StoreUserRequest $request): JsonResponse|UserResource
    {
        if ($this->isAble('store', User::class)) {
            $user = User::create($request->mappedAttributes());
            return UserResource::make($user);
        }
        return $this->notAuthorized('You are not authorized to create this resource');
    }

    /**
     * Display a user
     *
     * @group Managing Users
     */
    public function show(User $user): UserResource
    {
        if ($this->include('tickets')) {
            return UserResource::make($user->load('tickets'));
        }
        return UserResource::make($user);
    }

    /**
     * Update a user
     *
     * @group Managing Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
     */
    public function update(User $user, UpdateUserRequest $request): JsonResponse|UserResource
    {
        if ($this->isAble('update', User::class)) {
            $user->update($request->mappedAttributes());
            return UserResource::make($user);
        }
        return $this->notAuthorized('You are not authorized to update this resource');
    }

    /**
     * Replace a user
     *
     * @group Managing Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
     */
    public function replace(User $user, ReplaceUserRequest $request): JsonResponse|UserResource
    {
        if ($this->isAble('replace', User::class)) {
            $user->update($request->mappedAttributes());
            return UserResource::make($user);
        }
        return $this->notAuthorized('You are not authorized to update this resource');
    }

    /**
     * Delete a user
     *
     * @group Managing Users
     *
     * @response 200 {}
     */
    public function destroy(User $user): JsonResponse
    {
        if ($this->isAble('delete', User::class)) {
            $user->delete();
            return $this->ok('User successfully deleted');
        }
        return $this->notAuthorized('You are not authorized to delete this resource');
    }
}
