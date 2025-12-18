<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\RawMaterial;
use App\Models\Product;
use App\Models\MaterialInward;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                    ->orWhere('source_document_number', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('transaction_type')) {
            $query->where('transaction_type', $type);
        }

        $stockTransactions = $query->paginate(15)->withQueryString();

        return view('transactions.stock-transactions.index', compact('stockTransactions'));
    }

    public function create()
    {
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('raw_material_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        $materialInwards = MaterialInward::orderByDesc('id')->get();
        $salesInvoices = SalesInvoice::orderByDesc('id')->get();

        return view('transactions.stock-transactions.create', compact('rawMaterials', 'products', 'materialInwards', 'salesInvoices'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $transactionNumber = $data['transaction_number'] ?? ('ST-' . strtoupper(Str::random(8)));

        // Get item details
        $item = null;
        $unitOfMeasure = '';
        if ($data['item_type'] === 'raw_material') {
            $item = RawMaterial::find($data['item_id']);
            $unitOfMeasure = $item ? $item->unit_of_measure : '';
        } else {
            $item = Product::find($data['item_id']);
            $unitOfMeasure = $item ? $item->unit_of_measure : '';
        }

        // Validate stock availability for stock out
        if ($data['transaction_type'] === 'stock_out') {
            $availableStock = $this->getAvailableStock($data['item_type'], $data['item_id']);
            if ($availableStock < $data['quantity']) {
                $itemName = $data['item_type'] === 'raw_material' 
                    ? ($item ? $item->raw_material_name : 'Item')
                    : ($item ? $item->product_name : 'Item');
                return back()
                    ->withInput()
                    ->withErrors(['quantity' => "Insufficient stock available for {$itemName}. Available stock: " . number_format($availableStock, 3) . ". You cannot stock out more than available stock."]);
            }
        }

        // Get source document number
        $sourceDocumentNumber = null;
        if ($data['source_document_type'] && $data['source_document_id']) {
            if ($data['source_document_type'] === 'material_inward') {
                $sourceDoc = MaterialInward::find($data['source_document_id']);
                $sourceDocumentNumber = $sourceDoc ? $sourceDoc->inward_number : null;
            } elseif ($data['source_document_type'] === 'sales_invoice') {
                $sourceDoc = SalesInvoice::find($data['source_document_id']);
                $sourceDocumentNumber = $sourceDoc ? $sourceDoc->invoice_number : null;
            }
        }

        $stockTransaction = new StockTransaction();
        $stockTransaction->fill([
            'transaction_number' => $transactionNumber,
            'transaction_date' => $data['transaction_date'],
            'transaction_type' => $data['transaction_type'],
            'item_type' => $data['item_type'],
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
            'unit_of_measure' => $unitOfMeasure,
            'source_document_type' => $data['source_document_type'] ?? null,
            'source_document_id' => $data['source_document_id'] ?? null,
            'source_document_number' => $sourceDocumentNumber,
        ]);

        $user = Auth::user();
        if ($user) {
            $stockTransaction->organization_id = $user->organization_id ?? null;
            $stockTransaction->branch_id = session('active_branch_id');
            $stockTransaction->created_by = $user->id;
        }

        DB::transaction(function () use ($stockTransaction, $data) {
            $stockTransaction->save();

            // Update stock quantity
            if ($data['transaction_type'] === 'stock_in') {
                if ($data['item_type'] === 'raw_material') {
                    RawMaterial::where('id', $data['item_id'])->increment('quantity_available', $data['quantity']);
                } else {
                    Product::where('id', $data['item_id'])->increment('stock_quantity', $data['quantity']);
                }
            } else { // stock_out
                if ($data['item_type'] === 'raw_material') {
                    RawMaterial::where('id', $data['item_id'])->decrement('quantity_available', $data['quantity']);
                } else {
                    Product::where('id', $data['item_id'])->decrement('stock_quantity', $data['quantity']);
                }
            }
        });

        return redirect()->route('stock-transactions.index')
            ->with('success', 'Stock Transaction created successfully.');
    }

    public function show(StockTransaction $stockTransaction)
    {
        return view('transactions.stock-transactions.show', compact('stockTransaction'));
    }

    public function edit(StockTransaction $stockTransaction)
    {
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('raw_material_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        $materialInwards = MaterialInward::orderByDesc('id')->get();
        $salesInvoices = SalesInvoice::orderByDesc('id')->get();

        return view('transactions.stock-transactions.edit', compact('stockTransaction', 'rawMaterials', 'products', 'materialInwards', 'salesInvoices'));
    }

    public function update(Request $request, StockTransaction $stockTransaction)
    {
        $data = $this->validateRequest($request, $stockTransaction);

        // Get item details
        $item = null;
        $unitOfMeasure = '';
        if ($data['item_type'] === 'raw_material') {
            $item = RawMaterial::find($data['item_id']);
            $unitOfMeasure = $item ? $item->unit_of_measure : '';
        } else {
            $item = Product::find($data['item_id']);
            $unitOfMeasure = $item ? $item->unit_of_measure : '';
        }

        $oldQuantity = $stockTransaction->quantity;
        $oldItemType = $stockTransaction->item_type;
        $oldItemId = $stockTransaction->item_id;
        $oldTransactionType = $stockTransaction->transaction_type;

        // Validate stock availability for stock out
        if ($data['transaction_type'] === 'stock_out') {
            // Calculate available stock after reversing old transaction
            $availableStock = $this->getAvailableStock($data['item_type'], $data['item_id']);
            
            // If old transaction was stock_out on same item, add back the old quantity
            if ($oldTransactionType === 'stock_out' && $oldItemType === $data['item_type'] && $oldItemId == $data['item_id']) {
                $availableStock += $oldQuantity;
            }
            // If old transaction was stock_in on same item, subtract the old quantity
            elseif ($oldTransactionType === 'stock_in' && $oldItemType === $data['item_type'] && $oldItemId == $data['item_id']) {
                $availableStock -= $oldQuantity;
            }
            
            if ($availableStock < $data['quantity']) {
                $itemName = $data['item_type'] === 'raw_material' 
                    ? ($item ? $item->raw_material_name : 'Item')
                    : ($item ? $item->product_name : 'Item');
                return back()
                    ->withInput()
                    ->withErrors(['quantity' => "Insufficient stock available for {$itemName}. Available stock: " . number_format($availableStock, 3) . ". You cannot stock out more than available stock."]);
            }
        }

        // Get source document number
        $sourceDocumentNumber = null;
        if ($data['source_document_type'] && $data['source_document_id']) {
            if ($data['source_document_type'] === 'material_inward') {
                $sourceDoc = MaterialInward::find($data['source_document_id']);
                $sourceDocumentNumber = $sourceDoc ? $sourceDoc->inward_number : null;
            } elseif ($data['source_document_type'] === 'sales_invoice') {
                $sourceDoc = SalesInvoice::find($data['source_document_id']);
                $sourceDocumentNumber = $sourceDoc ? $sourceDoc->invoice_number : null;
            }
        }

        $stockTransaction->transaction_number = $data['transaction_number'] ?? $stockTransaction->transaction_number;
        $stockTransaction->transaction_date = $data['transaction_date'];
        $stockTransaction->transaction_type = $data['transaction_type'];
        $stockTransaction->item_type = $data['item_type'];
        $stockTransaction->item_id = $data['item_id'];
        $stockTransaction->quantity = $data['quantity'];
        $stockTransaction->unit_of_measure = $unitOfMeasure;
        $stockTransaction->source_document_type = $data['source_document_type'] ?? null;
        $stockTransaction->source_document_id = $data['source_document_id'] ?? null;
        $stockTransaction->source_document_number = $sourceDocumentNumber;

        DB::transaction(function () use ($stockTransaction, $data, $oldQuantity, $oldItemType, $oldItemId, $oldTransactionType) {
            // Reverse old transaction
            if ($oldTransactionType === 'stock_in') {
                if ($oldItemType === 'raw_material') {
                    RawMaterial::where('id', $oldItemId)->decrement('quantity_available', $oldQuantity);
                } else {
                    Product::where('id', $oldItemId)->decrement('stock_quantity', $oldQuantity);
                }
            } else {
                if ($oldItemType === 'raw_material') {
                    RawMaterial::where('id', $oldItemId)->increment('quantity_available', $oldQuantity);
                } else {
                    Product::where('id', $oldItemId)->increment('stock_quantity', $oldQuantity);
                }
            }

            $stockTransaction->save();

            // Apply new transaction
            if ($data['transaction_type'] === 'stock_in') {
                if ($data['item_type'] === 'raw_material') {
                    RawMaterial::where('id', $data['item_id'])->increment('quantity_available', $data['quantity']);
                } else {
                    Product::where('id', $data['item_id'])->increment('stock_quantity', $data['quantity']);
                }
            } else {
                if ($data['item_type'] === 'raw_material') {
                    RawMaterial::where('id', $data['item_id'])->decrement('quantity_available', $data['quantity']);
                } else {
                    Product::where('id', $data['item_id'])->decrement('stock_quantity', $data['quantity']);
                }
            }
        });

        return redirect()->route('stock-transactions.index')
            ->with('success', 'Stock Transaction updated successfully.');
    }

    public function destroy(StockTransaction $stockTransaction)
    {
        DB::transaction(function () use ($stockTransaction) {
            // Reverse the transaction
            if ($stockTransaction->transaction_type === 'stock_in') {
                if ($stockTransaction->item_type === 'raw_material') {
                    RawMaterial::where('id', $stockTransaction->item_id)->decrement('quantity_available', $stockTransaction->quantity);
                } else {
                    Product::where('id', $stockTransaction->item_id)->decrement('stock_quantity', $stockTransaction->quantity);
                }
            } else {
                if ($stockTransaction->item_type === 'raw_material') {
                    RawMaterial::where('id', $stockTransaction->item_id)->increment('quantity_available', $stockTransaction->quantity);
                } else {
                    Product::where('id', $stockTransaction->item_id)->increment('stock_quantity', $stockTransaction->quantity);
                }
            }

            $stockTransaction->delete();
        });

        return redirect()->route('stock-transactions.index')
            ->with('success', 'Stock Transaction deleted successfully.');
    }

    protected function validateRequest(Request $request, ?StockTransaction $stockTransaction = null): array
    {
        $rules = [
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:stock_in,stock_out'],
            'item_type' => ['required', 'in:raw_material,product'],
            'item_id' => ['required', 'integer'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'source_document_type' => ['nullable', 'in:material_inward,sales_invoice'],
            'source_document_id' => ['nullable', 'integer'],
        ];

        if (!$stockTransaction) {
            $rules['transaction_number'] = ['nullable', 'string', 'max:191', 'unique:stock_transactions,transaction_number'];
        } else {
            $rules['transaction_number'] = ['nullable', 'string', 'max:191', 'unique:stock_transactions,transaction_number,' . $stockTransaction->id];
        }

        return $request->validate($rules);
    }

    protected function getAvailableStock(string $itemType, int $itemId): float
    {
        if ($itemType === 'raw_material') {
            $item = RawMaterial::find($itemId);
            return $item ? (float) $item->quantity_available : 0;
        } else {
            $item = Product::find($itemId);
            return $item ? (float) $item->stock_quantity : 0;
        }
    }
}

