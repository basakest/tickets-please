<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class TicketController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    /**
     * Get all tickets
     *
     * @group      Managing Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A, C, H, X. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *fix*
     */
    public function index(TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Create a ticket
     *
     * Creates a new ticket record. Users can only create tickets for themselves. Managers can create tickets for any user.
     *
     * @group Managing Tickets
     *
     * @response {"data":{"type":"ticket","id":107,"attributes":{"title":"asdfasdfasdfasdfasdfsadf","description":"test ticket","status":"A","createdAt":"2024-03-26T04:40:48.000000Z","updatedAt":"2024-03-26T04:40:48.000000Z"},"relationships":{"author":{"data":{"type":"user","id":1},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/1"}}},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/tickets\/107"}}}
     */
    public function store(StoreTicketRequest $request): JsonResponse|TicketResource
    {
        if ($this->isAble('store', Ticket::class)) {
            $user = Auth::user();
            return TicketResource::make($user->tickets()->create($request->mappedAttributes()));
        }
        return $this->notAuthorized('You are not authorized to create ticket');
    }

    /**
     * Show a specific ticket.
     *
     * Display an individual ticket.
     *
     * @group Managing Tickets
     */
    public function show(Ticket $ticket): TicketResource
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }
        return TicketResource::make($ticket);
    }

    /**
     * Update Ticket
     *
     * Update the specified ticket in storage.
     *
     * @group Managing Tickets
     */
    public function update(Ticket $ticket, UpdateTicketRequest $request): TicketResource|JsonResponse
    {
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        }
        return $this->notAuthorized('You are not authorized to update that resource');
    }

    /**
     * Replace Ticket
     *
     * Replace the specified ticket in storage.
     *
     * @group Managing Tickets
     *
     */
    public function replace(Ticket $ticket, ReplaceTicketRequest $request): TicketResource|JsonResponse
    {
        if ($this->isAble('replace', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        }
        return $this->notAuthorized('You are not authorized to replace that resource');
    }

    /**
     * Delete ticket.
     *
     * Remove the specified resource from storage.
     *
     * @group Managing Tickets
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        if ($this->isAble('delete', $ticket)) {
            // use route model binding will expose some back-end info
            $ticket->delete();
            return $this->ok('Ticket successfully deleted');
        }
        return $this->notAuthorized('You are not authorized to delete this resource');
    }
}
