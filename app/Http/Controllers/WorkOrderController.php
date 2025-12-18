<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderMaterial;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkOrder::with(['customer', 'product'])->orderByDesc('id');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('work_order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($qc) use ($search) {
                        $qc->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product', function ($qp) use ($search) {
                        $qp->where('product_name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $workOrders = $query->paginate(15)->withQueryString();

        return view('transactions.work-orders.index', compact('workOrders'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('raw_material_name')->get();

        return view('transactions.work-orders.create', compact('customers', 'products', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $workOrderNumber = 'WO-' . strtoupper(Str::random(8));

        $workOrder = new WorkOrder();
        $workOrder->fill([
            'work_order_number' => $workOrderNumber,
            'customer_id' => $data['customer_id'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'quantity_to_produce' => $data['quantity_to_produce'],
            'work_order_type' => $data['work_order_type'],
            'work_order_date' => $data['work_order_date'],
            'status' => WorkOrder::STATUS_OPEN,
        ]);

        $user = Auth::user();
        if ($user) {
            $workOrder->organization_id = $user->organization_id ?? null;
            $workOrder->branch_id = session('active_branch_id');
            $workOrder->created_by = $user->id;
        }

        $workOrder->save();

        $this->saveMaterials($workOrder, $data['materials']);

        return redirect()->route('work-orders.index')
            ->with('success', 'Work Order created successfully.');
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['customer', 'product', 'materials.rawMaterial']);
        return view('transactions.work-orders.show', compact('workOrder'));
    }

    public function edit(WorkOrder $workOrder)
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('product_name')->get();
        $rawMaterials = RawMaterial::where('is_active', true)->orderBy('raw_material_name')->get();

        $workOrder->load('materials');

        return view('transactions.work-orders.edit', compact('workOrder', 'customers', 'products', 'rawMaterials'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $data = $this->validateRequest($request);

        $workOrder->customer_id = $data['customer_id'] ?? null;
        $workOrder->product_id = $data['product_id'] ?? null;
        $workOrder->quantity_to_produce = $data['quantity_to_produce'];
        $workOrder->work_order_type = $data['work_order_type'];
        $workOrder->work_order_date = $data['work_order_date'];

        // Auto update status: completed if total consumption >= quantity_to_produce (simple rule)
        $totalConsumption = $this->calculateTotalConsumption($data['materials']);
        if ($totalConsumption >= (float) $workOrder->quantity_to_produce && $workOrder->quantity_to_produce > 0) {
            $workOrder->status = WorkOrder::STATUS_COMPLETED;
        } else {
            $workOrder->status = WorkOrder::STATUS_OPEN;
        }

        $workOrder->save();

        $workOrder->materials()->delete();
        $this->saveMaterials($workOrder, $data['materials']);

        return redirect()->route('work-orders.index')
            ->with('success', 'Work Order updated successfully.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->delete();

        return redirect()->route('work-orders.index')
            ->with('success', 'Work Order deleted successfully.');
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'quantity_to_produce' => ['required', 'numeric', 'min:0.0001'],
            'work_order_type' => ['required', 'in:customer_order,internal'],
            'work_order_date' => ['required', 'date'],

            'materials' => ['required', 'array', 'min:1'],
            'materials.*.raw_material_id' => ['required', 'exists:raw_materials,id'],
            'materials.*.material_required' => ['required', 'numeric', 'min:0.0001'],
            'materials.*.consumption' => ['nullable', 'numeric', 'min:0'],
            'materials.*.unit_of_measure' => ['required', 'string', 'max:191'],
        ]);
    }

    protected function saveMaterials(WorkOrder $workOrder, array $materials): void
    {
        foreach ($materials as $material) {
            $required = (float) $material['material_required'];
            $consumption = array_key_exists('consumption', $material) && $material['consumption'] !== null
                ? (float) $material['consumption']
                : $required; // auto-calculated = required by default

            WorkOrderMaterial::create([
                'work_order_id' => $workOrder->id,
                'raw_material_id' => $material['raw_material_id'],
                'material_required' => $required,
                'consumption' => $consumption,
                'unit_of_measure' => $material['unit_of_measure'],
            ]);
        }
    }

    protected function calculateTotalConsumption(array $materials): float
    {
        $total = 0;
        foreach ($materials as $material) {
            $consumption = array_key_exists('consumption', $material) && $material['consumption'] !== null
                ? (float) $material['consumption']
                : (float) $material['material_required'];
            $total += $consumption;
        }
        return $total;
    }
}


