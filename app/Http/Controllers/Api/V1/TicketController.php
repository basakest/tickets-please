<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse as JsonResponseAlias;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            $user = User::query()->findOrFail($request->input('data.relationships.author.data.id'));
        } catch (ModelNotFoundException $e) {
            return $this->ok('User not found', [
                'error' => 'The provided user id does not exists',
            ]);
        }

        $model = [
            'title'       => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status'      => $request->input('data.attributes.status'),
        ];

        return TicketResource::make($user->tickets()->create($model));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): TicketResource
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }
        return TicketResource::make($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        //
    }

    public function replace(Ticket $ticket, ReplaceTicketRequest $request): TicketResource
    {
        $model = [
            'title'       => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status'      => $request->input('data.attributes.status'),
            'user_id'     => $request->input('data.relationships.author.data.id'),
        ];

        $ticket->update($model);

        return TicketResource::make($ticket);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): JsonResponseAlias
    {
        // use route model binding will expose some back-end info
        $ticket->delete();
        return $this->ok('Ticket successfully deleted');
    }
}
