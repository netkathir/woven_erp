<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
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
        
        $query = Customer::query();
        
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
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        
        switch ($sortBy) {
            case 'customer_name':
                $query->orderBy('customer_name', $sortOrder);
                break;
            case 'code':
                $query->orderBy('code', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            default:
                $query->orderBy('id', $sortOrder);
                break;
        }
        
        $customers = $query->paginate(15)->withQueryString();
        
        return view('masters.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('masters.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:100',
            'shipping_address_line_1' => 'nullable|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:100',
            'payment_terms' => 'required|in:cash,credit,advance,partial,other',
            'gst_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ], [
            'customer_name.required' => 'Customer Name is required.',
            'customer_name.max' => 'Customer Name must not exceed 255 characters.',
            'contact_name.max' => 'Contact Name must not exceed 255 characters.',
            'phone_number.max' => 'Phone Number must not exceed 20 characters.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'billing_address_line_1.max' => 'Billing Address Line 1 must not exceed 255 characters.',
            'billing_address_line_2.max' => 'Billing Address Line 2 must not exceed 255 characters.',
            'billing_city.max' => 'Billing City must not exceed 100 characters.',
            'billing_state.max' => 'Billing State must not exceed 100 characters.',
            'billing_postal_code.max' => 'Billing Postal Code must not exceed 20 characters.',
            'billing_country.max' => 'Billing Country must not exceed 100 characters.',
            'shipping_address_line_1.max' => 'Shipping Address Line 1 must not exceed 255 characters.',
            'shipping_address_line_2.max' => 'Shipping Address Line 2 must not exceed 255 characters.',
            'shipping_city.max' => 'Shipping City must not exceed 100 characters.',
            'shipping_state.max' => 'Shipping State must not exceed 100 characters.',
            'shipping_postal_code.max' => 'Shipping Postal Code must not exceed 20 characters.',
            'shipping_country.max' => 'Shipping Country must not exceed 100 characters.',
            'payment_terms.required' => 'Payment Terms is required.',
            'payment_terms.in' => 'Payment Terms must be one of: Cash, Credit, Advance, Partial, or Other.',
            'gst_number.max' => 'GST Number must not exceed 50 characters.',
            'credit_limit.numeric' => 'Credit Limit must be a number.',
            'credit_limit.min' => 'Credit Limit must be at least 0.',
        ]);

        // Generate unique code
        $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $request->customer_name), 0, 10));
        
        // Ensure base code is not empty
        if (empty($baseCode)) {
            $baseCode = 'CUST' . strtoupper(substr(md5($request->customer_name), 0, 7));
        }
        
        $code = $baseCode;
        $counter = 1;
        $maxAttempts = 1000;
        
        // Check for existing code including soft-deleted records
        while (Customer::withTrashed()->where('code', $code)->exists() && $counter < $maxAttempts) {
            $code = $baseCode . '_' . $counter;
            $counter++;
        }
        
        // If still not unique after max attempts, add timestamp
        if (Customer::withTrashed()->where('code', $code)->exists()) {
            $code = $baseCode . '_' . time();
        }

        $user = auth()->user();
        
        try {
            $customer = Customer::create([
                'customer_name' => $request->customer_name,
                'code' => $code,
                'contact_name' => $request->contact_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'billing_address_line_1' => $request->billing_address_line_1,
                'billing_address_line_2' => $request->billing_address_line_2,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_postal_code' => $request->billing_postal_code,
                'billing_country' => $request->billing_country,
                'shipping_address_line_1' => $request->shipping_address_line_1,
                'shipping_address_line_2' => $request->shipping_address_line_2,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country' => $request->shipping_country,
                'payment_terms' => $request->payment_terms,
                'gst_number' => $request->gst_number,
                'credit_limit' => $request->credit_limit ?? 0,
                'notes' => $request->notes,
                'is_active' => $request->has('is_active') ? true : false,
                'organization_id' => $user->organization_id,
                'branch_id' => $user->branch_id,
                'created_by' => $user->id,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // If still duplicate, generate with timestamp
            if ($e->getCode() == 23000) {
                $code = $baseCode . '_' . time() . '_' . rand(1000, 9999);
                $customer = Customer::create([
                    'customer_name' => $request->customer_name,
                    'code' => $code,
                    'contact_name' => $request->contact_name,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email,
                    'billing_address_line_1' => $request->billing_address_line_1,
                    'billing_address_line_2' => $request->billing_address_line_2,
                    'billing_city' => $request->billing_city,
                    'billing_state' => $request->billing_state,
                    'billing_postal_code' => $request->billing_postal_code,
                    'billing_country' => $request->billing_country,
                    'shipping_address_line_1' => $request->shipping_address_line_1,
                    'shipping_address_line_2' => $request->shipping_address_line_2,
                    'shipping_city' => $request->shipping_city,
                    'shipping_state' => $request->shipping_state,
                    'shipping_postal_code' => $request->shipping_postal_code,
                    'shipping_country' => $request->shipping_country,
                    'payment_terms' => $request->payment_terms,
                    'gst_number' => $request->gst_number,
                    'credit_limit' => $request->credit_limit ?? 0,
                    'notes' => $request->notes,
                    'is_active' => $request->has('is_active') ? true : false,
                    'organization_id' => $user->organization_id,
                    'branch_id' => $user->branch_id,
                    'created_by' => $user->id,
                ]);
            } else {
                throw $e;
            }
        }

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): View
    {
        return view('masters.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        return view('masters.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:100',
            'shipping_address_line_1' => 'nullable|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:100',
            'payment_terms' => 'required|in:cash,credit,advance,partial,other',
            'gst_number' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ], [
            'customer_name.required' => 'Customer Name is required.',
            'customer_name.max' => 'Customer Name must not exceed 255 characters.',
            'contact_name.max' => 'Contact Name must not exceed 255 characters.',
            'phone_number.max' => 'Phone Number must not exceed 20 characters.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'billing_address_line_1.max' => 'Billing Address Line 1 must not exceed 255 characters.',
            'billing_address_line_2.max' => 'Billing Address Line 2 must not exceed 255 characters.',
            'billing_city.max' => 'Billing City must not exceed 100 characters.',
            'billing_state.max' => 'Billing State must not exceed 100 characters.',
            'billing_postal_code.max' => 'Billing Postal Code must not exceed 20 characters.',
            'billing_country.max' => 'Billing Country must not exceed 100 characters.',
            'shipping_address_line_1.max' => 'Shipping Address Line 1 must not exceed 255 characters.',
            'shipping_address_line_2.max' => 'Shipping Address Line 2 must not exceed 255 characters.',
            'shipping_city.max' => 'Shipping City must not exceed 100 characters.',
            'shipping_state.max' => 'Shipping State must not exceed 100 characters.',
            'shipping_postal_code.max' => 'Shipping Postal Code must not exceed 20 characters.',
            'shipping_country.max' => 'Shipping Country must not exceed 100 characters.',
            'payment_terms.required' => 'Payment Terms is required.',
            'payment_terms.in' => 'Payment Terms must be one of: Cash, Credit, Advance, Partial, or Other.',
            'gst_number.max' => 'GST Number must not exceed 50 characters.',
            'credit_limit.numeric' => 'Credit Limit must be a number.',
            'credit_limit.min' => 'Credit Limit must be at least 0.',
        ]);

        $customer->update([
            'customer_name' => $request->customer_name,
            'contact_name' => $request->contact_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'billing_address_line_1' => $request->billing_address_line_1,
            'billing_address_line_2' => $request->billing_address_line_2,
            'billing_city' => $request->billing_city,
            'billing_state' => $request->billing_state,
            'billing_postal_code' => $request->billing_postal_code,
            'billing_country' => $request->billing_country,
            'shipping_address_line_1' => $request->shipping_address_line_1,
            'shipping_address_line_2' => $request->shipping_address_line_2,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_postal_code' => $request->shipping_postal_code,
            'shipping_country' => $request->shipping_country,
            'payment_terms' => $request->payment_terms,
            'gst_number' => $request->gst_number,
            'credit_limit' => $request->credit_limit ?? 0,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
