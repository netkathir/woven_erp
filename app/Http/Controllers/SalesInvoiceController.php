<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\CompanyInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesInvoice::with('customer')->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $salesInvoices = $query->paginate(15)->withQueryString();

        return view('transactions.sales-invoices.index', compact('salesInvoices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        return view('transactions.sales-invoices.create', compact('customers', 'products', 'companyInfo'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $invoiceNumber = $data['invoice_number'] ?? ('INV-' . strtoupper(Str::random(8)));

        $customer = Customer::find($data['customer_id']);
        $billingAddress = $customer ? $this->formatBillingAddress($customer) : null;
        $shippingAddress = $customer ? $this->formatShippingAddress($customer) : null;

        $salesInvoice = new SalesInvoice();
        $salesInvoice->fill([
            'invoice_number' => $invoiceNumber,
            'invoice_date' => $data['invoice_date'],
            'customer_id' => $data['customer_id'],
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
            'gst_percentage_overall' => $data['gst_percentage_overall'] ?? null,
            'gst_classification' => $this->determineGstClassification($data['customer_id']),
        ]);

        $totals = $this->calculateTotalsFromItems($data['items'] ?? []);
        $salesInvoice->total_sales_amount = $totals['total_sales_amount'];
        $salesInvoice->total_gst_amount = $totals['total_gst_amount'];
        $salesInvoice->grand_total = $totals['grand_total'];

        $user = Auth::user();
        if ($user) {
            $salesInvoice->organization_id = $user->organization_id ?? null;
            $salesInvoice->branch_id = session('active_branch_id');
            $salesInvoice->created_by = $user->id;
        }

        $salesInvoice->save();

        foreach ($data['items'] as $item) {
            $itemTotals = $this->calculateItemTotals(
                $item['quantity_sold'],
                $item['unit_price'],
                $item['gst_percentage'] ?? null
            );

            SalesInvoiceItem::create([
                'sales_invoice_id' => $salesInvoice->id,
                'product_id' => $item['product_id'],
                'quantity_sold' => $item['quantity_sold'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $itemTotals['total_amount'],
                'gst_percentage' => $item['gst_percentage'] ?? null,
                'gst_amount' => $itemTotals['gst_amount'],
                'line_total' => $itemTotals['line_total'],
            ]);
        }

        return redirect()->route('sales-invoices.index')
            ->with('success', 'Sales Invoice created successfully.');
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['customer', 'items.product']);
        return view('transactions.sales-invoices.show', compact('salesInvoice'));
    }

    public function edit(SalesInvoice $salesInvoice)
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        $salesInvoice->load('items');

        return view('transactions.sales-invoices.edit', compact('salesInvoice', 'customers', 'products', 'companyInfo'));
    }

    public function update(Request $request, SalesInvoice $salesInvoice)
    {
        $data = $this->validateRequest($request, $salesInvoice);

        $customer = Customer::find($data['customer_id']);
        $billingAddress = $customer ? $this->formatBillingAddress($customer) : null;
        $shippingAddress = $customer ? $this->formatShippingAddress($customer) : null;

        $salesInvoice->invoice_number = $data['invoice_number'] ?? $salesInvoice->invoice_number;
        $salesInvoice->invoice_date = $data['invoice_date'];
        $salesInvoice->customer_id = $data['customer_id'];
        $salesInvoice->billing_address = $billingAddress;
        $salesInvoice->shipping_address = $shippingAddress;
        $salesInvoice->gst_percentage_overall = $data['gst_percentage_overall'] ?? null;
        $salesInvoice->gst_classification = $this->determineGstClassification($data['customer_id']);

        $totals = $this->calculateTotalsFromItems($data['items'] ?? []);
        $salesInvoice->total_sales_amount = $totals['total_sales_amount'];
        $salesInvoice->total_gst_amount = $totals['total_gst_amount'];
        $salesInvoice->grand_total = $totals['grand_total'];

        $salesInvoice->save();

        $salesInvoice->items()->delete();

        foreach ($data['items'] as $item) {
            $itemTotals = $this->calculateItemTotals(
                $item['quantity_sold'],
                $item['unit_price'],
                $item['gst_percentage'] ?? null
            );

            SalesInvoiceItem::create([
                'sales_invoice_id' => $salesInvoice->id,
                'product_id' => $item['product_id'],
                'quantity_sold' => $item['quantity_sold'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $itemTotals['total_amount'],
                'gst_percentage' => $item['gst_percentage'] ?? null,
                'gst_amount' => $itemTotals['gst_amount'],
                'line_total' => $itemTotals['line_total'],
            ]);
        }

        return redirect()->route('sales-invoices.index')
            ->with('success', 'Sales Invoice updated successfully.');
    }

    public function destroy(SalesInvoice $salesInvoice)
    {
        $salesInvoice->delete();

        return redirect()->route('sales-invoices.index')
            ->with('success', 'Sales Invoice deleted successfully.');
    }

    protected function validateRequest(Request $request, ?SalesInvoice $salesInvoice = null): array
    {
        $rules = [
            'invoice_date' => ['required', 'date'],
            'customer_id' => ['required', 'exists:customers,id'],
            'gst_percentage_overall' => ['nullable', 'numeric', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity_sold' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.gst_percentage' => ['nullable', 'numeric', 'min:0'],
        ];

        if (!$salesInvoice) {
            $rules['invoice_number'] = ['nullable', 'string', 'max:191', 'unique:sales_invoices,invoice_number'];
        } else {
            $rules['invoice_number'] = ['nullable', 'string', 'max:191', 'unique:sales_invoices,invoice_number,' . $salesInvoice->id];
        }

        return $request->validate($rules);
    }

    protected function calculateItemTotals($quantity, $unitPrice, $gstPercentage): array
    {
        $quantity = (float) $quantity;
        $unitPrice = (float) $unitPrice;
        $gstPercentage = $gstPercentage !== null ? (float) $gstPercentage : 0.0;

        $totalAmount = $quantity * $unitPrice;
        $gstAmount = $gstPercentage > 0 ? ($totalAmount * $gstPercentage) / 100 : 0;
        $lineTotal = $totalAmount + $gstAmount;

        return [
            'total_amount' => $totalAmount,
            'gst_amount' => $gstAmount,
            'line_total' => $lineTotal,
        ];
    }

    protected function calculateTotalsFromItems(array $items): array
    {
        $totalSales = 0;
        $totalGst = 0;

        foreach ($items as $item) {
            $itemTotals = $this->calculateItemTotals(
                $item['quantity_sold'],
                $item['unit_price'],
                $item['gst_percentage'] ?? null
            );
            $totalSales += $itemTotals['total_amount'];
            $totalGst += $itemTotals['gst_amount'];
        }

        return [
            'total_sales_amount' => $totalSales,
            'total_gst_amount' => $totalGst,
            'grand_total' => $totalSales + $totalGst,
        ];
    }

    protected function determineGstClassification(int $customerId): ?string
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return null;
        }

        $activeBranchId = session('active_branch_id');
        $companyInfo = null;
        if ($activeBranchId) {
            $companyInfo = CompanyInformation::where('branch_id', $activeBranchId)->first();
        } else {
            $companyInfo = CompanyInformation::first();
        }

        if (!$companyInfo || !$companyInfo->state || !$customer->billing_state) {
            return null;
        }

        return strtolower($companyInfo->state) === strtolower($customer->billing_state)
            ? 'CGST_SGST'
            : 'IGST';
    }

    protected function formatBillingAddress(Customer $customer): string
    {
        $parts = [];
        if ($customer->billing_address_line_1) $parts[] = $customer->billing_address_line_1;
        if ($customer->billing_address_line_2) $parts[] = $customer->billing_address_line_2;
        if ($customer->billing_city) $parts[] = $customer->billing_city;
        if ($customer->billing_state) $parts[] = $customer->billing_state;
        if ($customer->billing_postal_code) $parts[] = $customer->billing_postal_code;
        if ($customer->billing_country) $parts[] = $customer->billing_country;
        return implode(', ', $parts);
    }

    protected function formatShippingAddress(Customer $customer): string
    {
        $parts = [];
        if ($customer->shipping_address_line_1) $parts[] = $customer->shipping_address_line_1;
        if ($customer->shipping_address_line_2) $parts[] = $customer->shipping_address_line_2;
        if ($customer->shipping_city) $parts[] = $customer->shipping_city;
        if ($customer->shipping_state) $parts[] = $customer->shipping_state;
        if ($customer->shipping_postal_code) $parts[] = $customer->shipping_postal_code;
        if ($customer->shipping_country) $parts[] = $customer->shipping_country;
        return implode(', ', $parts);
    }
}

