<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorsController extends ApiController
{
    /**
     * Get authors.
     *
     * Retrieves all users that created a ticket.
     *
     * @group Showing Authors
     */
    public function index(AuthorFilter $filters): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::filter($filters)->select('users.*')
                ->join('tickets', 'users.id', '=', 'tickets.user_id')
                ->distinct()
                ->paginate()
        );
    }

    /**
     * Get an author.
     *
     * Retrieves all users that created a ticket.
     *
     * @group Showing Authors
     */
    public function show(User $user): UserResource
    {
        if ($this->include('tickets')) {
            return UserResource::make($user->load('tickets'));
        }
        return UserResource::make($user);
    }
}
