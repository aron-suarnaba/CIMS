<?php

namespace App\Http\Controllers;

use App\Models\Computers;
use App\Models\ComputerTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ComputersController extends Controller
{
    public function index(Request $request)
    {
        $filterBrand = $request->get('brand'); // UI filter label remains 'brand'
        $sort = $request->get('sort', 'date_modified');

        $perPage = 12;
        $page = $request->integer('page', 1);
        $offset = ($page - 1) * $perPage;

        $query = Computers::query()
            ->when($filterBrand, function ($query, $filterBrand) {
                // Fixed: Search against 'manufacturer' or 'host_name'
                $query->where('manufacturer', 'LIKE', '%' . $filterBrand . '%')
                      ->orWhere('host_name', 'LIKE', '%' . $filterBrand . '%');
            })
            ->with('transactions'); // Matches model function name

        /** Sorting - Fixed column names to match migration */
        switch ($sort) {
            case 'name':
                $query->orderBy('manufacturer', 'asc') // Changed from brand
                      ->orderBy('model', 'asc');
                break;

            case 'availability':
                $query->orderByRaw("
                    CASE
                        WHEN status = 'In Storage' THEN 1
                        WHEN status = 'In Use' THEN 2
                        WHEN status = 'In Repair' THEN 3
                        ELSE 4
                    END
                ");
                break;

            case 'date_modified':
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        $total = (clone $query)->count();

        // SQL Server–safe pagination
        $ids = DB::table(DB::raw("(
            SELECT id,
                   ROW_NUMBER() OVER (ORDER BY updated_at DESC) AS row_num
            FROM computers
            WHERE deleted_at IS NULL
        ) AS t"))
            ->whereBetween('row_num', [$offset + 1, $offset + $perPage])
            ->pluck('id');

        $computers = $query->whereIn('id', $ids)->get();

        $paginated = new LengthAwarePaginator(
            $computers, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('AssetInventoryManagement/Computer', [
            'computers' => $paginated,
            'filters' => $request->only(['brand', 'sort']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'manufacturer'   => 'required|string', // Changed from brand
            'model'          => 'required|string',
            'serial_number'  => 'required|string|unique:computers,serial_number', // Fixed name
            'cpu'            => 'required|string', // Changed from processor
            'ram_gb'         => 'required|integer', // Changed from ram
            'storage_gb'     => 'required|integer', // Changed from storage
            'os_version'     => 'nullable|string', // Changed from os
            'host_name'      => 'required|string|unique:computers,host_name',
        ]);

        $validated['status'] = 'In Storage';

        Computers::create($validated);

        return redirect()->back()->with('success', 'Computer registered successfully.');
    }

    public function issue(Request $request, Computers $computer)
    {
        $validated = $request->validate([
            'assigned_user' => 'required|string|max:255', // Changed from issued_to
            'department'    => 'required|string|max:255',
            'date_issued'   => 'required|date',
        ]);

        // Link via host_name as per your foreign key migration
        $validated['host_name'] = $computer->host_name;

        ComputerTransaction::create($validated);

        $computer->update(['status' => 'In Use']);

        return redirect()->back()->with('success', 'Asset issued to ' . $validated['assigned_user']);
    }

    public function return(Request $request, Computers $computer)
    {
        $validated = $request->validate([
            'returned_to'    => 'required|string|max:255',
            'pullout_date'   => 'required|date', // Changed from date_returned
            'pullout_reason' => 'required|string|max:255', // Changed from remarks
        ]);

        $transaction = ComputerTransaction::where('host_name', $computer->host_name)
            ->whereNull('pullout_date')
            ->latest()
            ->first();

        if ($transaction) {
            $computer->update(['status' => 'In Storage']);
            $transaction->update($validated);
            return redirect()->back()->with('success', 'Asset returned successfully.');
        }

        return redirect()->back()->withErrors(['error' => 'No active issuance found.']);
    }
}
