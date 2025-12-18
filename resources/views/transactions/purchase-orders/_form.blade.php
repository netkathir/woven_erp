@php
    $editing = isset($purchaseOrder);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Purchase Order Number</label>
        <input type="text" class="form-control" value="{{ $editing ? $purchaseOrder->po_number : 'Auto Generated' }}" disabled
            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
    </div>

    <div>
        <label for="supplier_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Supplier Name <span style="color:red">*</span></label>
        <select name="supplier_id" id="supplier_id" required
            style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Supplier --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}"
                    {{ old('supplier_id', $editing ? $purchaseOrder->supplier_id : '') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->supplier_name }} ({{ $supplier->code }})
                </option>
            @endforeach
        </select>
        @error('supplier_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="purchase_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Purchase Date <span style="color:red">*</span></label>
        <input type="date" name="purchase_date" id="purchase_date" required
               value="{{ old('purchase_date', $editing ? optional($purchaseOrder->purchase_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('purchase_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="delivery_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Delivery Date</label>
        <input type="date" name="delivery_date" id="delivery_date"
               value="{{ old('delivery_date', $editing && $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('Y-m-d') : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('delivery_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="gst_percentage_overall" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">GST Percentage (Overall)</label>
        <input type="number" step="0.01" name="gst_percentage_overall" id="gst_percentage_overall"
               value="{{ old('gst_percentage_overall', $editing ? $purchaseOrder->gst_percentage_overall : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('gst_percentage_overall')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">GST Classification</label>
        <input type="text" id="gst_classification_view" disabled
               value="@if($editing && $purchaseOrder->gst_classification) {{ $purchaseOrder->gst_classification === 'CGST_SGST' ? 'CGST + SGST' : 'IGST' }} @endif"
               placeholder="Auto based on supplier and company state"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
    </div>
</div>

<h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Raw Materials</h3>

<div style="overflow-x: auto; margin-bottom: 20px;">
    <table id="itemsTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 10px; text-align: left;">Raw Material</th>
                <th style="padding: 10px; text-align: right;">Quantity</th>
                <th style="padding: 10px; text-align: right;">Unit Price</th>
                <th style="padding: 10px; text-align: right;">Total Amount</th>
                <th style="padding: 10px; text-align: right;">GST %</th>
                <th style="padding: 10px; text-align: right;">GST Amount</th>
                <th style="padding: 10px; text-align: right;">Line Total</th>
                <th style="padding: 10px; text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldItems = old('items', $editing ? $purchaseOrder->items->toArray() : [['raw_material_id' => '', 'quantity' => '', 'unit_price' => '', 'gst_percentage' => '']]);
            @endphp
            @foreach($oldItems as $index => $item)
                <tr>
                    <td style="padding: 8px;">
                        <select name="items[{{ $index }}][raw_material_id]" required
                                style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                            <option value="">-- Select --</option>
                            @foreach($rawMaterials as $rm)
                                <option value="{{ $rm->id }}"
                                    {{ (int)($item['raw_material_id'] ?? $item['raw_material_id'] ?? 0) === $rm->id ? 'selected' : '' }}>
                                    {{ $rm->raw_material_name }} ({{ $rm->code }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.001" min="0" name="items[{{ $index }}][quantity]"
                               value="{{ $item['quantity'] ?? '' }}"
                               class="item-quantity"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]"
                               value="{{ $item['unit_price'] ?? '' }}"
                               class="item-unit-price"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" readonly
                               class="item-total-amount"
                               value=""
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][gst_percentage]"
                               value="{{ $item['gst_percentage'] ?? '' }}"
                               class="item-gst-percentage"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" readonly
                               class="item-gst-amount"
                               value=""
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" readonly
                               class="item-line-total"
                               value=""
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
                    </td>
                    <td style="padding: 8px; text-align: center;">
                        <button type="button" class="btn-remove-row" onclick="removeItemRow(this)"
                                style="padding: 6px 10px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<button type="button" onclick="addItemRow()"
        style="padding: 10px 18px; background: #28a745; color: #fff; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;">
    <i class="fas fa-plus"></i> Add Row
</button>

<div style="display: flex; justify-content: flex-end; margin-top: 20px;">
    <div style="max-width: 360px; width: 100%; background: #f9fafb; padding: 16px 18px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: 500; color: #374151;">Total Raw Material Amount:</span>
            <span id="total_raw_material_amount_view" style="font-weight: 600; color: #111827;">0.00</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: 500; color: #374151;">Total GST Amount:</span>
            <span id="total_gst_amount_view" style="font-weight: 600; color: #111827;">0.00</span>
        </div>
        <div style="height: 1px; background: #e5e7eb; margin: 8px 0 10px;"></div>
        <div style="display: flex; justify-content: space-between;">
            <span style="font-weight: 700; color: #111827;">Grand Total:</span>
            <span id="grand_total_view" style="font-weight: 700; color: #111827;">0.00</span>
        </div>
    </div>
</div>

<input type="hidden" name="total_raw_material_amount" id="total_raw_material_amount">
<input type="hidden" name="total_gst_amount" id="total_gst_amount">
<input type="hidden" name="grand_total" id="grand_total">

@push('scripts')
<script>
    function parseNumber(value) {
        var n = parseFloat(value);
        return isNaN(n) ? 0 : n;
    }

    function recalcRow(row) {
        var qty = parseNumber(row.querySelector('.item-quantity').value);
        var price = parseNumber(row.querySelector('.item-unit-price').value);
        var gstPerc = parseNumber(row.querySelector('.item-gst-percentage').value);

        var total = qty * price;
        var gstAmt = gstPerc > 0 ? (total * gstPerc) / 100 : 0;
        var lineTotal = total + gstAmt;

        row.querySelector('.item-total-amount').value = total.toFixed(2);
        row.querySelector('.item-gst-amount').value = gstAmt.toFixed(2);
        row.querySelector('.item-line-total').value = lineTotal.toFixed(2);
    }

    function recalcTotals() {
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        var totalRaw = 0;
        var totalGst = 0;

        rows.forEach(function (row) {
            totalRaw += parseNumber(row.querySelector('.item-total-amount').value);
            totalGst += parseNumber(row.querySelector('.item-gst-amount').value);
        });

        document.getElementById('total_raw_material_amount_view').innerText = totalRaw.toFixed(2);
        document.getElementById('total_gst_amount_view').innerText = totalGst.toFixed(2);
        document.getElementById('grand_total_view').innerText = (totalRaw + totalGst).toFixed(2);

        document.getElementById('total_raw_material_amount').value = totalRaw.toFixed(2);
        document.getElementById('total_gst_amount').value = totalGst.toFixed(2);
        document.getElementById('grand_total').value = (totalRaw + totalGst).toFixed(2);
    }

    function attachRowEvents(row) {
        ['item-quantity', 'item-unit-price', 'item-gst-percentage'].forEach(function (cls) {
            var input = row.querySelector('.' + cls);
            if (input) {
                input.addEventListener('input', function () {
                    recalcRow(row);
                    recalcTotals();
                });
            }
        });
    }

    function addItemRow() {
        var tbody = document.querySelector('#itemsTable tbody');
        var index = tbody.querySelectorAll('tr').length;

        var tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="padding: 8px;">
                <select name="items[${index}][raw_material_id]" required
                        style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                    <option value="">-- Select --</option>
                    @foreach($rawMaterials as $rm)
                        <option value="{{ $rm->id }}">{{ $rm->raw_material_name }} ({{ $rm->code }})</option>
                    @endforeach
                </select>
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.001" min="0" name="items[${index}][quantity]"
                       class="item-quantity"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][unit_price]"
                       class="item-unit-price"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" readonly
                       class="item-total-amount"
                       value=""
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
            </td>
            <td style="padding: 8px;">
                <input type="number" step="0.01" min="0" name="items[${index}][gst_percentage]"
                       class="item-gst-percentage"
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right;">
            </td>
            <td style="padding: 8px;">
                <input type="text" readonly
                       class="item-gst-amount"
                       value=""
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
            </td>
            <td style="padding: 8px;">
                <input type="text" readonly
                       class="item-line-total"
                       value=""
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
            </td>
            <td style="padding: 8px; text-align: center;">
                <button type="button" class="btn-remove-row" onclick="removeItemRow(this)"
                        style="padding: 6px 10px; background: #dc3545; color: #fff; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        attachRowEvents(tr);
    }

    function removeItemRow(button) {
        var tbody = document.querySelector('#itemsTable tbody');
        if (tbody.querySelectorAll('tr').length <= 1) {
            alert('At least one item is required.');
            return;
        }
        var row = button.closest('tr');
        if (row) {
            row.remove();
        }
        recalcTotals();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var rows = document.querySelectorAll('#itemsTable tbody tr');
        rows.forEach(function (row) {
            attachRowEvents(row);
            recalcRow(row);
        });
        recalcTotals();
    });
</script>
@endpush


