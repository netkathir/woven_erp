@php
    $editing = isset($workOrder);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Work Order Number</label>
        <input type="text" value="{{ $editing ? $workOrder->work_order_number : 'Auto Generated' }}" disabled
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
    </div>

    <div>
        <label for="work_order_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Work Order Date <span style="color:red">*</span></label>
        <input type="date" name="work_order_date" id="work_order_date" required
               value="{{ old('work_order_date', $editing ? optional($workOrder->work_order_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('work_order_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="customer_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Customer Name</label>
        <select name="customer_id" id="customer_id"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Customer --</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}"
                    {{ old('customer_id', $editing ? $workOrder->customer_id : '') == $customer->id ? 'selected' : '' }}>
                    {{ $customer->customer_name }} ({{ $customer->code }})
                </option>
            @endforeach
        </select>
        @error('customer_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="product_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Product to be Manufactured</label>
        <select name="product_id" id="product_id"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Product --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}"
                    {{ old('product_id', $editing ? $workOrder->product_id : '') == $product->id ? 'selected' : '' }}>
                    {{ $product->product_name }} ({{ $product->code }})
                </option>
            @endforeach
        </select>
        @error('product_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="quantity_to_produce" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Quantity to be Produced <span style="color:red">*</span></label>
        <input type="number" step="0.001" min="0" name="quantity_to_produce" id="quantity_to_produce" required
               value="{{ old('quantity_to_produce', $editing ? $workOrder->quantity_to_produce : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('quantity_to_produce')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="work_order_type" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Work Order Type <span style="color:red">*</span></label>
        <select name="work_order_type" id="work_order_type" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="customer_order" {{ old('work_order_type', $editing ? $workOrder->work_order_type : 'customer_order') === 'customer_order' ? 'selected' : '' }}>Customer Order</option>
            <option value="internal" {{ old('work_order_type', $editing ? $workOrder->work_order_type : 'customer_order') === 'internal' ? 'selected' : '' }}>Internal</option>
        </select>
        @error('work_order_type')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

<h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Material Required & Consumption</h3>

<div style="overflow-x: auto; margin-bottom: 20px;">
    <table id="materialsTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 10px; text-align: left;">Material</th>
                <th style="padding: 10px; text-align: left;">UOM</th>
                <th style="padding: 10px; text-align: right;">Material Required</th>
                <th style="padding: 10px; text-align: right;">Consumption</th>
                <th style="padding: 10px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldMaterials = old('materials', $editing ? $workOrder->materials->toArray() : [['raw_material_id' => '', 'unit_of_measure' => '', 'material_required' => '', 'consumption' => '']]);
                $rawMaterialsJson = $rawMaterials->map(function($rm) {
                    return [
                        'id' => $rm->id,
                        'unit_of_measure' => $rm->unit_of_measure,
                    ];
                })->values()->toJson();
            @endphp
            @foreach($oldMaterials as $index => $material)
                <tr>
                    <td style="padding: 8px;">
                        <select name="materials[{{ $index }}][raw_material_id]" class="material-raw" required
                                style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                            <option value="">-- Select --</option>
                            @foreach($rawMaterials as $rm)
                                <option value="{{ $rm->id }}"
                                    {{ (int)($material['raw_material_id'] ?? 0) === $rm->id ? 'selected' : '' }}>
                                    {{ $rm->raw_material_name }} ({{ $rm->code }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" name="materials[{{ $index }}][unit_of_measure]"
                               value="{{ $material['unit_of_measure'] ?? '' }}"
                               class="material-uom"
                               readonly
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background:#f5f5f5;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.001" min="0" name="materials[{{ $index }}][material_required]"
                               value="{{ $material['material_required'] ?? '' }}"
                               class="material-required"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.001" min="0" name="materials[{{ $index }}][consumption]"
                               value="{{ $material['consumption'] ?? '' }}"
                               class="material-consumption"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px; text-align: center;">
                        <button type="button" onclick="removeMaterialRow(this)"
                                style="padding: 6px 10px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<button type="button" onclick="addMaterialRow()"
        style="padding: 10px 18px; background: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 10px;">
    <i class="fas fa-plus"></i> Add Material
</button>

<p style="font-size: 13px; color: #6b7280; margin-top: 4px;">
    Consumption is auto-filled equal to Material Required, but you can adjust it if actual usage is different.  
    Status will change to Completed when total consumption meets or exceeds the quantity to be produced.
</p>

@push('scripts')
<script>
    const workOrderRawMaterialsLookup = {!! $rawMaterialsJson !!};

    function woFindRawMaterial(id) {
        return workOrderRawMaterialsLookup.find(rm => rm.id === parseInt(id));
    }

    function attachMaterialRowEvents(row) {
        const materialSelect = row.querySelector('.material-raw');
        const uomInput = row.querySelector('.material-uom');
        const requiredInput = row.querySelector('.material-required');
        const consumptionInput = row.querySelector('.material-consumption');

        if (materialSelect) {
            materialSelect.addEventListener('change', function () {
                const rm = woFindRawMaterial(materialSelect.value);
                if (rm) {
                    uomInput.value = rm.unit_of_measure || '';
                } else {
                    uomInput.value = '';
                }
            });
        }

        if (requiredInput && consumptionInput) {
            requiredInput.addEventListener('input', function () {
                if (!consumptionInput.value) {
                    consumptionInput.value = requiredInput.value;
                }
            });
        }
    }

    function addMaterialRow() {
        const tbody = document.querySelector('#materialsTable tbody');
        const index = tbody.querySelectorAll('tr').length;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 8px;">
                <select name="materials[${index}][raw_material_id]" class="material-raw" required
                        style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="">-- Select --</option>
                    @foreach($rawMaterials as $rm)
                        <option value="{{ $rm->id }}">{{ $rm->raw_material_name }} ({{ $rm->code }})</option>
                    @endforeach
                </select>
            </td>
            <td style="padding: 8px;">
                <input type="text" name="materials[${index}][unit_of_measure]"
                       class="material-uom"
                       readonly
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; background:#f5f5f5;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.001" min="0" name="materials[${index}][material_required]"
                       class="material-required"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.001" min="0" name="materials[${index}][consumption]"
                       class="material-consumption"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px; text-align: center;">
                <button type="button" onclick="removeMaterialRow(this)"
                        style="padding: 6px 10px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        attachMaterialRowEvents(tr);
    }

    function removeMaterialRow(button) {
        const tbody = document.querySelector('#materialsTable tbody');
        if (tbody.querySelectorAll('tr').length <= 1) {
            alert('At least one material row is required.');
            return;
        }
        const row = button.closest('tr');
        if (row) {
            row.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('#materialsTable tbody tr');
        rows.forEach(function (row) {
            attachMaterialRowEvents(row);
        });
    });
</script>
@endpush


