<?php

namespace App\Http\Controllers;

use App\Models\DailyExpense;
use App\Models\DailyExpenseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DailyExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyExpense::orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_id', 'like', "%{$search}%")
                    ->orWhere('vendor_name', 'like', "%{$search}%")
                    ->orWhere('expense_category', 'like', "%{$search}%")
                    ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        $dailyExpenses = $query->paginate(15)->withQueryString();

        return view('transactions.daily-expenses.index', compact('dailyExpenses'));
    }

    public function create()
    {
        return view('transactions.daily-expenses.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $expenseId = $data['expense_id'] ?? ('EXP-' . strtoupper(Str::random(8)));

        $dailyExpense = new DailyExpense();
        $dailyExpense->fill([
            'expense_id' => $expenseId,
            'expense_date' => $data['expense_date'],
            'expense_category' => $data['expense_category'],
            'vendor_name' => $data['vendor_name'],
            'payment_method' => $data['payment_method'],
            'invoice_number' => $data['invoice_number'] ?? null,
            'amount' => $data['amount'] ?? 0,
            'gst_applied' => $data['gst_applied'] ?? null,
        ]);

        $totals = $this->calculateTotalsFromItems($data['items'] ?? []);
        $dailyExpense->total_expense_amount = $totals['total_expense_amount'];
        $dailyExpense->total_gst_amount = $totals['total_gst_amount'];
        
        // Grand Total = Total Expense Amount + GST Applied (if any) + Total GST Amount from items
        $gstAppliedAmount = $dailyExpense->gst_applied ? ($dailyExpense->total_expense_amount * $dailyExpense->gst_applied) / 100 : 0;
        $dailyExpense->grand_total = $dailyExpense->total_expense_amount + $gstAppliedAmount + $dailyExpense->total_gst_amount;

        $user = Auth::user();
        if ($user) {
            $dailyExpense->organization_id = $user->organization_id ?? null;
            $dailyExpense->branch_id = session('active_branch_id');
            $dailyExpense->created_by = $user->id;
        }

        $dailyExpense->save();

        foreach ($data['items'] as $item) {
            $itemTotals = $this->calculateItemTotals(
                $item['quantity'],
                $item['unit_price'],
                $item['gst_percentage'] ?? null
            );

            DailyExpenseItem::create([
                'daily_expense_id' => $dailyExpense->id,
                'item_description' => $item['item_description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $itemTotals['total_amount'],
                'gst_percentage' => $item['gst_percentage'] ?? null,
                'gst_amount' => $itemTotals['gst_amount'],
                'line_total' => $itemTotals['line_total'],
            ]);
        }

        return redirect()->route('daily-expenses.index')
            ->with('success', 'Daily Expense created successfully.');
    }

    public function show(DailyExpense $dailyExpense)
    {
        $dailyExpense->load('items');
        return view('transactions.daily-expenses.show', compact('dailyExpense'));
    }

    public function edit(DailyExpense $dailyExpense)
    {
        $dailyExpense->load('items');
        return view('transactions.daily-expenses.edit', compact('dailyExpense'));
    }

    public function update(Request $request, DailyExpense $dailyExpense)
    {
        $data = $this->validateRequest($request, $dailyExpense);

        $dailyExpense->expense_id = $data['expense_id'] ?? $dailyExpense->expense_id;
        $dailyExpense->expense_date = $data['expense_date'];
        $dailyExpense->expense_category = $data['expense_category'];
        $dailyExpense->vendor_name = $data['vendor_name'];
        $dailyExpense->payment_method = $data['payment_method'];
        $dailyExpense->invoice_number = $data['invoice_number'] ?? null;
        $dailyExpense->amount = $data['amount'] ?? 0;
        $dailyExpense->gst_applied = $data['gst_applied'] ?? null;

        $totals = $this->calculateTotalsFromItems($data['items'] ?? []);
        $dailyExpense->total_expense_amount = $totals['total_expense_amount'];
        $dailyExpense->total_gst_amount = $totals['total_gst_amount'];
        
        // Grand Total = Total Expense Amount + GST Applied (if any) + Total GST Amount from items
        $gstAppliedAmount = $dailyExpense->gst_applied ? ($dailyExpense->total_expense_amount * $dailyExpense->gst_applied) / 100 : 0;
        $dailyExpense->grand_total = $dailyExpense->total_expense_amount + $gstAppliedAmount + $dailyExpense->total_gst_amount;

        $dailyExpense->save();

        $dailyExpense->items()->delete();

        foreach ($data['items'] as $item) {
            $itemTotals = $this->calculateItemTotals(
                $item['quantity'],
                $item['unit_price'],
                $item['gst_percentage'] ?? null
            );

            DailyExpenseItem::create([
                'daily_expense_id' => $dailyExpense->id,
                'item_description' => $item['item_description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $itemTotals['total_amount'],
                'gst_percentage' => $item['gst_percentage'] ?? null,
                'gst_amount' => $itemTotals['gst_amount'],
                'line_total' => $itemTotals['line_total'],
            ]);
        }

        return redirect()->route('daily-expenses.index')
            ->with('success', 'Daily Expense updated successfully.');
    }

    public function destroy(DailyExpense $dailyExpense)
    {
        $dailyExpense->delete();

        return redirect()->route('daily-expenses.index')
            ->with('success', 'Daily Expense deleted successfully.');
    }

    protected function validateRequest(Request $request, ?DailyExpense $dailyExpense = null): array
    {
        $rules = [
            'expense_date' => ['required', 'date'],
            'expense_category' => ['required', 'string', 'max:191'],
            'vendor_name' => ['required', 'string', 'max:191'],
            'payment_method' => ['required', 'in:cash,credit,bank_transfer'],
            'invoice_number' => ['nullable', 'string', 'max:191'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'gst_applied' => ['nullable', 'numeric', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.item_description' => ['required', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.gst_percentage' => ['nullable', 'numeric', 'min:0'],
        ];

        if (!$dailyExpense) {
            $rules['expense_id'] = ['nullable', 'string', 'max:191', 'unique:daily_expenses,expense_id'];
        } else {
            $rules['expense_id'] = ['nullable', 'string', 'max:191', 'unique:daily_expenses,expense_id,' . $dailyExpense->id];
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
        $totalExpense = 0;
        $totalGst = 0;

        foreach ($items as $item) {
            $itemTotals = $this->calculateItemTotals(
                $item['quantity'],
                $item['unit_price'],
                $item['gst_percentage'] ?? null
            );
            $totalExpense += $itemTotals['total_amount'];
            $totalGst += $itemTotals['gst_amount'];
        }

        return [
            'total_expense_amount' => $totalExpense,
            'total_gst_amount' => $totalGst,
        ];
    }
}

