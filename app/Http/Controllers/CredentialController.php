<?php

namespace App\Http\Controllers;

use App\Models\Credential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class CredentialController extends Controller
{
    public function index(Request $request)
    {
        $query = Credential::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'date_modified');
        switch ($sort) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'username':
                $query->orderBy('username', 'asc');
                break;
            case 'date_modified':
            default:
                $query->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        return Inertia::render('AssetInventoryManagement/CredentialList', [
            'credentials' => $query->paginate(12)->withQueryString(),
            'filters' => $request->only(['search', 'sort']),
        ]);
    }

    public function create()
    {
        // creation happens via modal on index, so just redirect
        return redirect()->route('credentials.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        Credential::create($validated);

        return redirect()->route('credentials.index')->with('success', 'Credential saved successfully.');
    }

    public function show(Request $request, Credential $credential)
    {
        // Support JSON for modal fetch
        if ($request->wantsJson()) {
            return response()->json([
                'credential' => $credential,
            ]);
        }

        // We always hide the password in the initial view; show only when flashed
        $revealed = $request->session()->get('revealed_password');

        return Inertia::render('AssetInventoryManagement/CredentialDetails', [
            'credential' => $credential->makeHidden('password'),
            'revealed_password' => $revealed,
        ]);
    }

    public function reveal(Request $request, Credential $credential)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();
        $loginOk = $user && Hash::check($request->password, $user->password);

        if ($loginOk) {
            // decrypt stored password once user login password is confirmed
            $stored = $credential->getRawOriginal('password');
            try {
                $decrypted = Crypt::decryptString($stored);
            } catch (\Throwable $e) {
                $decrypted = null;
            }

            if ($decrypted === null) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'errors' => ['password' => ['Unable to decrypt credential password.']],
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->withErrors(['password' => 'Unable to decrypt credential password.']);
            }

            if ($request->wantsJson()) {
                return response()->json(['revealed_password' => $decrypted]);
            }
            // password correct -- redirect back with flashed value
            return redirect()
                ->route('credentials.show', $credential)
                ->with('revealed_password', $decrypted);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'errors' => ['password' => ['The login password you entered does not match.']],
            ], 422);
        }

        return redirect()
            ->back()
            ->withErrors(['password' => 'The login password you entered does not match.']);
    }

    public function update(Request $request, Credential $credential)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'url' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $credential->update($validated);

        return back()->with('success', 'Credential updated successfully.');
    }

    public function destroy(Credential $credential)
    {
        $credential->delete();
        return redirect()->route('credentials.index')->with('success', 'Credential deleted.');
    }
}
