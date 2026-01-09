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
    /**
     * Display a listing of computer devices.
     */
    public function index(Request $request)
    {
        $filterBrand = $request->get('brand');
        $sort = $request->get('sort', 'date_modified');

        $perPage = 18;
        $page = $request->integer('page', 1);
        $offset = ($page - 1) * $perPage;

        /** Base query */
        $query = Computers::query()
            ->when($filterBrand, function ($query, $filterBrand) {
                // Search by manufacturer or host_name
                $query->where('manufacturer', 'LIKE', '%' . $filterBrand . '%')
                    ->orWhere('host_name', 'LIKE', '%' . $filterBrand . '%');
            })
            ->with(['transactions', 'currentTransaction']); // Eager load relationships here

        /** Sorting */
        switch ($sort) {
            case 'name':
                $query->orderBy('manufacturer', 'asc') // Fixed: changed 'brand' to 'manufacturer'
                    ->orderBy('model', 'asc');
                break;

            case 'availability':
                $query->orderByRaw("
                CASE
                    WHEN status = 'In Storage' THEN 1
                    WHEN status = 'In Use' THEN 2
                    WHEN status = 'In Repair' THEN 3
                    WHEN status = 'Retired' THEN 4
                    ELSE 5
                END
            ");
                break;

            case 'date_modified':
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        /** Total count */
        $total = (clone $query)->count();

        /** SQL Server–safe pagination - Get IDs first */
        $ids = DB::table(DB::raw("(
        SELECT id,
               ROW_NUMBER() OVER (ORDER BY updated_at DESC) AS row_num
        FROM computers
        WHERE deleted_at IS NULL
    ) AS t"))
            ->whereBetween('row_num', [$offset + 1, $offset + $perPage])
            ->pluck('id');

        /** Fetch the actual models */
        $items = $query->whereIn('id', $ids)->get();

        /** Create paginator */
        $computersPagination = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return Inertia::render('AssetInventoryManagement/Computer', [
            'computers' => $computersPagination,
            'filters' => $request->only(['brand', 'sort']),
        ]);
    }
    /**
     * Show the form for creating a new computer.
     */
    public function create()
    {
        return Inertia::render('AssetInventoryManagement/AddComputer');
    }

    /**
     * Store a newly created computer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string',
            'model' => 'required|string',
            'serial_num' => 'required|string|unique:computers,serial_num',
            'processor' => 'required|string',
            'ram' => 'required|string',
            'storage' => 'required|string',
            'os' => 'nullable|string',
            'cashout' => 'required|boolean',
        ]);

        $validated['status'] = 'available';

        Computers::create($validated);

        return redirect()->back()->with('success', 'Computer registered successfully.');
    }

    /**
     * Issue a computer.
     */
    public function issue(Request $request, Computers $computer)
    {
        $validated = $request->validate([
            'issued_to' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date_issued' => 'required|date',
            'issued_by' => 'nullable|string|max:255',
            'issued_accessories' => 'nullable|string',
            'it_ack_issued' => 'required|boolean',
            'purch_ack_issued' => 'required|boolean',
        ]);

        $validated['serial_num'] = $computer->serial_num;

        ComputerTransaction::create($validated);

        $computer->update([
            'status' => 'issued',
        ]);

        return redirect()->back()
            ->with('success', 'The computer has been issued successfully to ' . $validated['issued_to']);
    }

    /**
     * Return a computer.
     */
    public function return(Request $request, Computers $computer)
    {
        $validated = $request->validate([
            'returned_to' => 'required|string|max:255',
            'returned_by' => 'required|string|max:255',
            'returnee_department' => 'required|string|max:255',
            'date_returned' => 'required|date',
            'returned_accessories' => 'nullable|string',
            'it_ack_returned' => 'required|boolean',
            'purch_ack_returned' => 'required|boolean',
            'remarks' => 'nullable|string|max:255',
        ]);

        $transaction = ComputerTransaction::where('serial_num', $computer->serial_num)
            ->whereNull('date_returned')
            ->latest()
            ->first();

        if ($transaction) {
            $computer->update(['status' => 'available']);
            $transaction->update($validated);

            return redirect()->back()->with('success', 'The computer has been returned successfully.');
        }

        return redirect()->back()
            ->withErrors(['error' => 'No active issuance found for this device.']);
    }

    /**
     * Display the specified computer.
     */
    public function show(Computers $computer)
    {
        $computer->load(['currentTransaction', 'transactions']);

        return Inertia::render('AssetInventoryManagement/ComputerDetails', [
            'computer' => $computer,
            'computer_transaction' => $computer->transactions()->latest()->first(),
        ]);
    }

    /**
     * Remove the specified computer.
     */
    public function destroy(Computers $computer)
    {
        try {
            $computer->delete();

            return redirect()
                ->route('computer.index')
                ->with('success', 'Asset record and all related history have been deleted');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete asset.');
        }
    }
}
