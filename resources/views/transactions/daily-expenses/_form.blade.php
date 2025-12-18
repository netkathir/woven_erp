@php
    $editing = isset($dailyExpense);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="expense_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Expense ID</label>
        <input type="text" name="expense_id" id="expense_id"
               value="{{ old('expense_id', $editing ? $dailyExpense->expense_id : '') }}"
               placeholder="Auto Generated if left empty"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('expense_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="expense_date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Date <span style="color:red">*</span></label>
        <input type="date" name="expense_date" id="expense_date" required
               value="{{ old('expense_date', $editing ? optional($dailyExpense->expense_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('expense_date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="expense_category" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Expense Category <span style="color:red">*</span></label>
        <select name="expense_category" id="expense_category" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Category --</option>
            <option value="Office Supplies" {{ old('expense_category', $editing ? $dailyExpense->expense_category : '') === 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
            <option value="Travel" {{ old('expense_category', $editing ? $dailyExpense->expense_category : '') === 'Travel' ? 'selected' : '' }}>Travel</option>
            <option value="Utilities" {{ old('expense_category', $editing ? $dailyExpense->expense_category : '') === 'Utilities' ? 'selected' : '' }}>Utilities</option>
            <option value="Maintenance" {{ old('expense_category', $editing ? $dailyExpense->expense_category : '') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="Marketing" {{ old('expense_category', $editing ? $dailyExpense->expense_category : '') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
            <option value="Professional Services" {{ old('expense_category', $editing ? $dailyExpense->expense_category : '') === 'Professional Services' ? 'selected' : '' }}>Professional Services</option>
            <option value="Other" {{ old('expense_category', $editing ? $dailyExpense->expense_category : '') === 'Other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('expense_category')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="vendor_name" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Vendor Name <span style="color:red">*</span></label>
        <input type="text" name="vendor_name" id="vendor_name" required
               value="{{ old('vendor_name', $editing ? $dailyExpense->vendor_name : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('vendor_name')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="payment_method" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Payment Method <span style="color:red">*</span></label>
        <select name="payment_method" id="payment_method" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="cash" {{ old('payment_method', $editing ? $dailyExpense->payment_method : 'cash') === 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="credit" {{ old('payment_method', $editing ? $dailyExpense->payment_method : '') === 'credit' ? 'selected' : '' }}>Credit</option>
            <option value="bank_transfer" {{ old('payment_method', $editing ? $dailyExpense->payment_method : '') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
        </select>
        @error('payment_method')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="invoice_number" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Invoice Number</label>
        <input type="text" name="invoice_number" id="invoice_number"
               value="{{ old('invoice_number', $editing ? $dailyExpense->invoice_number : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('invoice_number')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="amount" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Amount</label>
        <input type="number" step="0.01" min="0" name="amount" id="amount"
               value="{{ old('amount', $editing ? $dailyExpense->amount : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('amount')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>
</div>

<h3 style="margin-bottom: 12px; font-size: 18px; color: #333;">Expense Items</h3>

<div style="overflow-x: auto; margin-bottom: 20px;">
    <table id="itemsTable" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 10px; text-align: left;">Item Description</th>
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
                $oldItems = old('items', $editing ? $dailyExpense->items->toArray() : [['item_description' => '', 'quantity' => '', 'unit_price' => '', 'gst_percentage' => '']]);
            @endphp
            @foreach($oldItems as $index => $item)
                <tr>
                    <td style="padding: 8px;">
                        <input type="text" name="items[{{ $index }}][item_description]" required
                               value="{{ $item['item_description'] ?? '' }}"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
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
                               value="{{ isset($item['total_amount']) ? number_format($item['total_amount'], 2) : '' }}"
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
                               value="{{ isset($item['gst_amount']) ? number_format($item['gst_amount'], 2) : '' }}"
                               style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd; text-align: right; background: #f5f5f5;">
                    </td>
                    <td style="padding: 8px;">
                        <input type="text" readonly
                               class="item-line-total"
                               value="{{ isset($item['line_total']) ? number_format($item['line_total'], 2) : '' }}"
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
            <span style="font-weight: 500; color: #374151;">Total Expense Amount:</span>
            <span id="total_expense_amount_view" style="font-weight: 600; color: #111827;">0.00</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="font-weight: 500; color: #374151;">GST Applied (%):</span>
            <input type="number" step="0.01" min="0" name="gst_applied" id="gst_applied"
                   value="{{ old('gst_applied', $editing ? $dailyExpense->gst_applied : '') }}"
                   style="width: 100px; padding: 4px 8px; border-radius: 4px; border: 1px solid #ddd; text-align: right;">
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

<input type="hidden" name="total_expense_amount" id="total_expense_amount">
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
        var totalExpense = 0;
        var totalGst = 0;

        rows.forEach(function (row) {
            totalExpense += parseNumber(row.querySelector('.item-total-amount').value);
            totalGst += parseNumber(row.querySelector('.item-gst-amount').value);
        });

        var gstAppliedPerc = parseNumber(document.getElementById('gst_applied').value);
        var gstAppliedAmount = gstAppliedPerc > 0 ? (totalExpense * gstAppliedPerc) / 100 : 0;
        var grandTotal = totalExpense + gstAppliedAmount + totalGst;

        document.getElementById('total_expense_amount_view').innerText = totalExpense.toFixed(2);
        document.getElementById('total_gst_amount_view').innerText = totalGst.toFixed(2);
        document.getElementById('grand_total_view').innerText = grandTotal.toFixed(2);

        document.getElementById('total_expense_amount').value = totalExpense.toFixed(2);
        document.getElementById('total_gst_amount').value = totalGst.toFixed(2);
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
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
                <input type="text" name="items[${index}][item_description]" required
                       style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
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

    document.getElementById('gst_applied').addEventListener('input', function() {
        recalcTotals();
    });

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

