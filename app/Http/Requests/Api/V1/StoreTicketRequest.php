<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isTicketController = $this->routeIs('tickets.store');
        // 从 data.relationships.author.data.id 获取创建 ticket 的用户 ID, 允许管理员为其他用户创建 ticket
        $authorIdAttr = $isTicketController ? 'data.relationships.author.data.id' : 'author';
        $user = Auth::user();

        $rules = [
            'data'                        => ['required', 'array'],
            'data.attributes'             => ['required', 'array'],
            'data.attributes.title'       => ['required', 'string'],
            'data.attributes.description' => ['required', 'string'],
            'data.attributes.status'      => ['required', 'string', 'in:A,C,H,X'],
        ];

        if ($isTicketController) {
            $rules['data.relationships'] = ['required', 'array'];
            $rules['data.relationships.author'] = ['required', 'array'];
            $rules['data.relationships.author.data'] = ['required', 'array'];
        }

        // Validate that the data.relationships.author.data.id(integer) equals $user->id
        // eg: The data.relationships.author.data.id field must be 2.
        $rules[$authorIdAttr] = ['required', 'integer', 'exists:users,id', 'size:' . $user->id];

        if ($user->tokenCan(Abilities::CreateTicket)) {
            array_pop($rules[$authorIdAttr]);
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')->id,
            ]);
        }
    }

    public function bodyParameters(): array
    {
        $documentation = [
            'data.attributes.title'       => [
                'description' => "The ticket's title (method)",
                'example'     => 'No-example',
            ],
            'data.attributes.description' => [
                'description' => "The ticket's description",
                'example'     => 'No-example',
            ],
            'data.attributes.status'      => [
                'description' => "The ticket's status",
                'example'     => 'No-example',
            ],
        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The author assigned to the ticket.',
                'example'     => 'No-example',
            ];
        } else {
            $documentation['author'] = [
                'description' => 'The author assigned to the ticket.',
                'example'     => 'No-example',
            ];
        }

        return $documentation;
    }
}
