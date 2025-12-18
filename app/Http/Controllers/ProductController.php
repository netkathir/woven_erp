<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
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
        
        $query = Product::query();
        
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
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('product_category', 'like', "%{$search}%")
                  ->orWhere('unit_of_measure', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        
        switch ($sortBy) {
            case 'product_name':
                $query->orderBy('product_name', $sortOrder);
                break;
            case 'code':
                $query->orderBy('code', $sortOrder);
                break;
            case 'product_category':
                $query->orderBy('product_category', $sortOrder);
                break;
            case 'stock_quantity':
                $query->orderBy('stock_quantity', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            default:
                $query->orderBy('id', $sortOrder);
                break;
        }
        
        $products = $query->paginate(15)->withQueryString();
        
        return view('masters.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('masters.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'product_category' => 'required|string|max:100',
            'price_per_unit' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ], [
            'product_name.required' => 'Product Name is required.',
            'product_name.max' => 'Product Name must not exceed 255 characters.',
            'unit_of_measure.required' => 'Unit of Measure is required.',
            'unit_of_measure.max' => 'Unit of Measure must not exceed 50 characters.',
            'product_category.required' => 'Product Category is required.',
            'product_category.max' => 'Product Category must not exceed 100 characters.',
            'price_per_unit.required' => 'Price per Unit is required.',
            'price_per_unit.numeric' => 'Price per Unit must be a number.',
            'price_per_unit.min' => 'Price per Unit must be at least 0.',
            'stock_quantity.required' => 'Stock Quantity is required.',
            'stock_quantity.numeric' => 'Stock Quantity must be a number.',
            'stock_quantity.min' => 'Stock Quantity must be at least 0.',
            'gst_percentage.numeric' => 'GST Percentage must be a number.',
            'gst_percentage.min' => 'GST Percentage must be at least 0.',
            'gst_percentage.max' => 'GST Percentage must not exceed 100.',
        ]);

        // Generate unique code
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->product_name), 0, 10));
        
        // Ensure base code is not empty
        if (empty($baseCode)) {
            $baseCode = 'PRD' . strtoupper(substr(md5($request->product_name), 0, 7));
        }
        
        $code = $baseCode;
        $counter = 1;
        $maxAttempts = 1000;
        
        // Check for existing code including soft-deleted records
        while (Product::withTrashed()->where('code', $code)->exists() && $counter < $maxAttempts) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }
        
        // If still not unique after max attempts, add timestamp
        if (Product::withTrashed()->where('code', $code)->exists()) {
            $code = $baseCode . '_' . time();
        }

        $user = auth()->user();
        
        try {
            $product = Product::create([
                'product_name' => $request->product_name,
                'code' => $code,
                'unit_of_measure' => $request->unit_of_measure,
                'product_category' => $request->product_category,
                'price_per_unit' => $request->price_per_unit,
                'stock_quantity' => $request->stock_quantity,
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
                $product = Product::create([
                    'product_name' => $request->product_name,
                    'code' => $code,
                    'unit_of_measure' => $request->unit_of_measure,
                    'product_category' => $request->product_category,
                    'price_per_unit' => $request->price_per_unit,
                    'stock_quantity' => $request->stock_quantity,
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

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        return view('masters.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        return view('masters.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'unit_of_measure' => 'required|string|max:50',
            'product_category' => 'required|string|max:100',
            'price_per_unit' => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ], [
            'product_name.required' => 'Product Name is required.',
            'product_name.max' => 'Product Name must not exceed 255 characters.',
            'unit_of_measure.required' => 'Unit of Measure is required.',
            'unit_of_measure.max' => 'Unit of Measure must not exceed 50 characters.',
            'product_category.required' => 'Product Category is required.',
            'product_category.max' => 'Product Category must not exceed 100 characters.',
            'price_per_unit.required' => 'Price per Unit is required.',
            'price_per_unit.numeric' => 'Price per Unit must be a number.',
            'price_per_unit.min' => 'Price per Unit must be at least 0.',
            'stock_quantity.required' => 'Stock Quantity is required.',
            'stock_quantity.numeric' => 'Stock Quantity must be a number.',
            'stock_quantity.min' => 'Stock Quantity must be at least 0.',
            'gst_percentage.numeric' => 'GST Percentage must be a number.',
            'gst_percentage.min' => 'GST Percentage must be at least 0.',
            'gst_percentage.max' => 'GST Percentage must not exceed 100.',
        ]);

        $product->update([
            'product_name' => $request->product_name,
            'unit_of_measure' => $request->unit_of_measure,
            'product_category' => $request->product_category,
            'price_per_unit' => $request->price_per_unit,
            'stock_quantity' => $request->stock_quantity,
            'gst_percentage' => $request->gst_percentage ?? 0,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
