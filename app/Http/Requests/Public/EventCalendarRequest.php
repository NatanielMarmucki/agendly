<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Optional `?sessions=1,2,3` limits the export to the attendee's plan.
 * Only ids travel to the server – the plan itself is never stored.
 */
class EventCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sessions = $this->query('sessions');

        if (is_string($sessions)) {
            $this->merge([
                'sessions' => array_values(array_filter(explode(',', $sessions), fn (string $id): bool => $id !== '')),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sessions' => ['sometimes', 'array', 'max:500'],
            'sessions.*' => ['integer', 'min:1'],
        ];
    }

    /**
     * A calendar download has nowhere to redirect back to (and the public
     * routes have no session to flash errors into): answer 422 directly.
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }

    /**
     * @return list<int>|null
     */
    public function sessionIds(): ?array
    {
        if (! $this->has('sessions')) {
            return null;
        }

        /** @var list<int|string> $ids */
        $ids = $this->validated('sessions', []);

        return array_map(intval(...), $ids);
    }
}
