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
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): JsonResponse|TicketResource
    {
        if ($this->isAble('store', Ticket::class)) {
            $user = Auth::user();
            // 这里是不是没有必要根据请求参数来获取对应的用户
            // try {
            //     $user = User::query()->findOrFail($request->input('data.relationships.author.data.id'));
            // } catch (ModelNotFoundException $e) {
            //     return $this->ok('User not found', [
            //         'error' => 'The provided user id does not exists',
            //     ]);
            // }
            return TicketResource::make($user->tickets()->create($request->mappedAttributes()));
        }
        return $this->error('You are not authorized to create ticket', 403);
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
    public function update(Ticket $ticket, UpdateTicketRequest $request): TicketResource|JsonResponse
    {
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        }
        return $this->error('You are not authorized to update that resource', 403);
    }

    public function replace(Ticket $ticket, ReplaceTicketRequest $request): TicketResource|JsonResponse
    {
        if ($this->isAble('replace', $ticket)) {
            $ticket->update($request->mappedAttributes());
            return TicketResource::make($ticket);
        }
        return $this->error('You are not authorized to replace that resource', 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        if ($this->isAble('delete', $ticket)) {
            // use route model binding will expose some back-end info
            $ticket->delete();
            return $this->ok('Ticket successfully deleted');
        }
        return $this->error('You are not authorized to delete this resource', 403);
    }
}
