<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorTicketsController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $author, TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection($author->tickets()->filter($filters)->paginate());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(User $author, Request $request): TicketResource
    {
        $model = [
            'title'       => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status'      => $request->input('data.attributes.status'),
        ];

        return new TicketResource($author->tickets()->create($model));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function replace(int $authorId, Ticket $ticket, ReplaceTicketRequest $request): JsonResponse|TicketResource
    {
        if ($authorId === $ticket->user_id) {
            $model = [
                'title'       => $request->input('data.attributes.title'),
                'description' => $request->input('data.attributes.description'),
                'status'      => $request->input('data.attributes.status'),
                'user_id'     => $request->input('data.relationships.author.data.id'),
            ];

            $ticket->update($model);

            return TicketResource::make($ticket);
        }
        // TODO: ticket doesn't belong to user
        return $this->error('use has no power', 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $authorId, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id === $authorId) {
            $ticket->delete();
            return $this->ok('Ticket successfully deleted');
        }
        return $this->error('Ticket not found', 404);
    }
}
