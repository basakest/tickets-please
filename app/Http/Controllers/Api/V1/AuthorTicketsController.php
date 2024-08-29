<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\V1\TicketPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorTicketsController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(User $author, TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection($author->tickets()->filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(User $author, StoreTicketRequest $request): TicketResource|JsonResponse
    {
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource($author->tickets()->create($request->mappedAttributes([
                'author' => 'user_id',
            ])));
        }
        return $this->error('You are not authorized to create ticket', 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $authorId, Ticket $ticket, UpdateTicketRequest $request): JsonResponse|TicketResource
    {
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        }
        return $this->error('You are not authorized to update this resource', 403);
    }

    public function replace(int $authorId, Ticket $ticket, ReplaceTicketRequest $request): JsonResponse|TicketResource
    {
        if ($this->isAble('replace', $ticket)) {
            if ($authorId === $ticket->user_id) {
                $ticket->update($request->mappedAttributes());

                return TicketResource::make($ticket);
            }
            // TODO: ticket doesn't belong to user
            return $this->error('use has no power', 403);
        }
        return $this->error('You are not authorized to edit this resource', 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $authorId, Ticket $ticket): JsonResponse
    {
        if ($this->isAble('delete', $ticket)) {
            $ticket->delete();
            return $this->ok('Ticket successfully deleted');
        }
        return $this->error('You are not authorized to delete this resource', 403);
    }
}
