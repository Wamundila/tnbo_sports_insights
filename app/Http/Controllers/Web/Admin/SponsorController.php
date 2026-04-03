<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sponsor::query()->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('admin.sponsors.index', $this->viewData($query->paginate(15)->withQueryString(), $request));
    }

    public function store(Request $request): RedirectResponse
    {
        Sponsor::query()->create($this->validatedData($request));

        return redirect()
            ->route('admin.sponsors.index')
            ->with('status', 'Sponsor created.');
    }

    public function edit(Request $request, Sponsor $sponsor): View
    {
        $query = Sponsor::query()->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('admin.sponsors.index', $this->viewData(
            $query->paginate(15)->withQueryString(),
            $request,
            $sponsor
        ));
    }

    public function update(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $sponsor->update($this->validatedData($request, $sponsor));

        return redirect()
            ->route('admin.sponsors.edit', $sponsor)
            ->with('status', 'Sponsor updated.');
    }

    private function validatedData(Request $request, ?Sponsor $sponsor = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('sponsors', 'code')->ignore($sponsor)],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:40'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function viewData($sponsors, Request $request, ?Sponsor $editing = null): array
    {
        return [
            'sponsors' => $sponsors,
            'filters' => $request->only('status'),
            'editing' => $editing,
        ];
    }
}
