<?php

namespace App\Http\Controllers;

use App\Models\SoftwareLicense;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SoftwareLicenseController extends Controller
{
    public function index(Request $request)
    {
        $query = SoftwareLicense::query();

        if ($search = $request->get('search')) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('software_name', 'like', "%{$search}%")
                    ->orWhere('vendor', 'like', "%{$search}%")
                    ->orWhere('license_type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'date_modified');
        switch ($sort) {
            case 'name':
                $query->orderBy('software_name', 'asc');
                break;
            case 'licenses':
                $query->orderBy('total_licenses', 'desc')->orderBy('id', 'desc');
                break;
            case 'date_modified':
            default:
                $query->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        return Inertia::render('AssetInventoryManagement/SoftwareLicenseList', [
            'licenses' => $query->paginate(12)->withQueryString(),
            'filters' => $request->only(['search', 'sort']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'software_name' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'license_type' => 'required|string|max:255',
            'total_licenses' => 'required|integer|min:1',
            'used_licenses' => 'required|integer|min:0',
            'license_key' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'status' => 'required|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        if ($validated['used_licenses'] > $validated['total_licenses']) {
            return back()->withErrors([
                'used_licenses' => 'Used licenses cannot be greater than total licenses.',
            ]);
        }

        SoftwareLicense::create($validated);

        return redirect()->route('software-license.index')->with('success', 'Software license added successfully.');
    }

    public function show(SoftwareLicense $softwareLicense)
    {
        return Inertia::render('AssetInventoryManagement/SoftwareLicenseDetails', [
            'license' => $softwareLicense,
        ]);
    }

    public function update(Request $request, SoftwareLicense $softwareLicense)
    {
        $validated = $request->validate([
            'software_name' => 'required|string|max:255',
            'vendor' => 'nullable|string|max:255',
            'license_type' => 'required|string|max:255',
            'total_licenses' => 'required|integer|min:1',
            'used_licenses' => 'required|integer|min:0',
            'license_key' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'status' => 'required|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        if ($validated['used_licenses'] > $validated['total_licenses']) {
            return back()->withErrors([
                'used_licenses' => 'Used licenses cannot be greater than total licenses.',
            ]);
        }

        $softwareLicense->update($validated);

        return back()->with('success', 'Software license updated successfully.');
    }

    public function destroy(SoftwareLicense $softwareLicense)
    {
        $softwareLicense->delete();

        return redirect()->route('software-license.index')->with('success', 'Software license deleted successfully.');
    }
}
