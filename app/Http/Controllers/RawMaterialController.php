<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RawMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        $query = RawMaterial::with('supplier');
        
        // Filter by organization/branch if needed
        if ($user->organization_id) {
            $query->where('organization_id', $user->organization_id);
        }
        if ($user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('raw_material_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('unit_of_measure', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        
        switch ($sortBy) {
            case 'raw_material_name':
                $query->orderBy('raw_material_name', $sortOrder);
                break;
            case 'code':
                $query->orderBy('code', $sortOrder);
                break;
            case 'quantity_available':
                $query->orderBy('quantity_available', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            default:
                $query->orderBy('id', $sortOrder);
                break;
        }
        
        $rawMaterials = $query->paginate(15)->withQueryString();
        
        return view('masters.raw-materials.index', compact('rawMaterials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('supplier_name')->get();
        return view('masters.raw-materials.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'raw_material_name' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'quantity_available' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price_per_unit' => 'required|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ], [
            'raw_material_name.required' => 'Raw Material Name is required.',
            'raw_material_name.max' => 'Raw Material Name must not exceed 255 characters.',
            'unit_of_measure.required' => 'Unit of Measure is required.',
            'unit_of_measure.max' => 'Unit of Measure must not exceed 50 characters.',
            'quantity_available.required' => 'Quantity Available is required.',
            'quantity_available.numeric' => 'Quantity Available must be a number.',
            'quantity_available.min' => 'Quantity Available must be at least 0.',
            'reorder_level.required' => 'Reorder Level is required.',
            'reorder_level.numeric' => 'Reorder Level must be a number.',
            'reorder_level.min' => 'Reorder Level must be at least 0.',
            'supplier_id.exists' => 'Selected Supplier does not exist.',
            'price_per_unit.required' => 'Price per Unit is required.',
            'price_per_unit.numeric' => 'Price per Unit must be a number.',
            'price_per_unit.min' => 'Price per Unit must be at least 0.',
            'gst_percentage.numeric' => 'GST Percentage must be a number.',
            'gst_percentage.min' => 'GST Percentage must be at least 0.',
            'gst_percentage.max' => 'GST Percentage must not exceed 100.',
        ]);

        // Generate unique code
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->raw_material_name), 0, 10));
        
        // Ensure base code is not empty
        if (empty($baseCode)) {
            $baseCode = 'RM' . strtoupper(substr(md5($request->raw_material_name), 0, 7));
        }
        
        $code = $baseCode;
        $counter = 1;
        $maxAttempts = 1000;
        
        // Check for existing code including soft-deleted records
        while (RawMaterial::withTrashed()->where('code', $code)->exists() && $counter < $maxAttempts) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }
        
        // If still not unique after max attempts, add timestamp
        if (RawMaterial::withTrashed()->where('code', $code)->exists()) {
            $code = $baseCode . '_' . time();
        }

        $user = auth()->user();
        
        try {
            $rawMaterial = RawMaterial::create([
                'raw_material_name' => $request->raw_material_name,
                'code' => $code,
                'unit_of_measure' => $request->unit_of_measure,
                'quantity_available' => $request->quantity_available,
                'reorder_level' => $request->reorder_level,
                'supplier_id' => $request->supplier_id,
                'price_per_unit' => $request->price_per_unit,
                'gst_percentage' => $request->gst_percentage ?? 0,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? true : false,
                'organization_id' => $user->organization_id,
                'branch_id' => $user->branch_id,
                'created_by' => $user->id,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // If still duplicate, generate with timestamp
            if ($e->getCode() == 23000) {
                $code = $baseCode . '_' . time() . '_' . rand(1000, 9999);
                $rawMaterial = RawMaterial::create([
                    'raw_material_name' => $request->raw_material_name,
                    'code' => $code,
                    'unit_of_measure' => $request->unit_of_measure,
                    'quantity_available' => $request->quantity_available,
                    'reorder_level' => $request->reorder_level,
                    'supplier_id' => $request->supplier_id,
                    'price_per_unit' => $request->price_per_unit,
                    'gst_percentage' => $request->gst_percentage ?? 0,
                    'description' => $request->description,
                    'is_active' => $request->has('is_active') ? true : false,
                    'organization_id' => $user->organization_id,
                    'branch_id' => $user->branch_id,
                    'created_by' => $user->id,
                ]);
            } else {
                throw $e;
            }
        }

        return redirect()->route('raw-materials.index')
            ->with('success', 'Raw Material created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RawMaterial $rawMaterial): View
    {
        $rawMaterial->load('supplier');
        return view('masters.raw-materials.show', compact('rawMaterial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RawMaterial $rawMaterial): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('supplier_name')->get();
        return view('masters.raw-materials.edit', compact('rawMaterial', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $request->validate([
            'raw_material_name' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'quantity_available' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price_per_unit' => 'required|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ], [
            'raw_material_name.required' => 'Raw Material Name is required.',
            'raw_material_name.max' => 'Raw Material Name must not exceed 255 characters.',
            'unit_of_measure.required' => 'Unit of Measure is required.',
            'unit_of_measure.max' => 'Unit of Measure must not exceed 50 characters.',
            'quantity_available.required' => 'Quantity Available is required.',
            'quantity_available.numeric' => 'Quantity Available must be a number.',
            'quantity_available.min' => 'Quantity Available must be at least 0.',
            'reorder_level.required' => 'Reorder Level is required.',
            'reorder_level.numeric' => 'Reorder Level must be a number.',
            'reorder_level.min' => 'Reorder Level must be at least 0.',
            'supplier_id.exists' => 'Selected Supplier does not exist.',
            'price_per_unit.required' => 'Price per Unit is required.',
            'price_per_unit.numeric' => 'Price per Unit must be a number.',
            'price_per_unit.min' => 'Price per Unit must be at least 0.',
            'gst_percentage.numeric' => 'GST Percentage must be a number.',
            'gst_percentage.min' => 'GST Percentage must be at least 0.',
            'gst_percentage.max' => 'GST Percentage must not exceed 100.',
        ]);

        $rawMaterial->update([
            'raw_material_name' => $request->raw_material_name,
            'unit_of_measure' => $request->unit_of_measure,
            'quantity_available' => $request->quantity_available,
            'reorder_level' => $request->reorder_level,
            'supplier_id' => $request->supplier_id,
            'price_per_unit' => $request->price_per_unit,
            'gst_percentage' => $request->gst_percentage ?? 0,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Raw Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RawMaterial $rawMaterial): RedirectResponse
    {
        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')
            ->with('success', 'Raw Material deleted successfully.');
    }
}
