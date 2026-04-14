<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        [$firstName, $lastName] = $this->splitName($user->name);

        return view('profile.edit', [
            'user' => $user,
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $request->merge([
            'first_name' => trim((string) $request->input('first_name')),
            'last_name' => trim((string) $request->input('last_name')),
            'email' => trim((string) $request->input('email')),
            'username' => filled($request->input('username'))
                ? trim((string) $request->input('username'))
                : null,
        ]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'username' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
        ]);

        $user->fill([
            'name' => trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? '')),
            'email' => $validated['email'],
            'username' => $validated['username'] ?? null,
        ])->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', __('dashboard.profile_updated'));
    }

    private function splitName(?string $name): array
    {
        $trimmedName = trim((string) $name);

        if ($trimmedName === '') {
            return ['', ''];
        }

        $parts = preg_split('/\s+/u', $trimmedName) ?: [];
        $firstName = array_shift($parts) ?? '';
        $lastName = implode(' ', $parts);

        return [$firstName, $lastName];
    }
}
