@php
    $editing = isset($pettyCash);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="expense_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Expense ID</label>
        <input type="text" name="expense_id" id="expense_id"
               value="{{ old('expense_id', $editing ? $pettyCash->expense_id : '') }}"
               placeholder="Auto Generated if left empty"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('expense_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="date" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Date <span style="color:red">*</span></label>
        <input type="date" name="date" id="date" required
               value="{{ old('date', $editing ? optional($pettyCash->date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('date')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="expense_category" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Expense Category <span style="color:red">*</span></label>
        <select name="expense_category" id="expense_category" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Category --</option>
            <option value="Stationery" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Stationery' ? 'selected' : '' }}>Stationery</option>
            <option value="Office Supplies" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
            <option value="Transportation" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Transportation' ? 'selected' : '' }}>Transportation</option>
            <option value="Meals" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Meals' ? 'selected' : '' }}>Meals</option>
            <option value="Utilities" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Utilities' ? 'selected' : '' }}>Utilities</option>
            <option value="Maintenance" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="Marketing" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
            <option value="Other" {{ old('expense_category', $editing ? $pettyCash->expense_category : '') === 'Other' ? 'selected' : '' }}>Other</option>
        </select>
        @error('expense_category')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="description" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Description</label>
        <input type="text" name="description" id="description"
               value="{{ old('description', $editing ? $pettyCash->description : '') }}"
               placeholder="e.g., Printer toner, Taxi fare"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('description')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="amount" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Amount <span style="color:red">*</span></label>
        <input type="number" step="0.01" min="0" name="amount" id="amount" required
               value="{{ old('amount', $editing ? $pettyCash->amount : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('amount')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="payment_method" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Payment Method <span style="color:red">*</span></label>
        <select name="payment_method" id="payment_method" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="Cash" {{ old('payment_method', $editing ? $pettyCash->payment_method : 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
            <option value="Credit" {{ old('payment_method', $editing ? $pettyCash->payment_method : '') === 'Credit' ? 'selected' : '' }}>Credit</option>
            <option value="Debit" {{ old('payment_method', $editing ? $pettyCash->payment_method : '') === 'Debit' ? 'selected' : '' }}>Debit</option>
        </select>
        @error('payment_method')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="paid_to" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Paid To <span style="color:red">*</span></label>
        <input type="text" name="paid_to" id="paid_to" required
               value="{{ old('paid_to', $editing ? $pettyCash->paid_to : '') }}"
               placeholder="e.g., John Doe, XYZ Suppliers"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('paid_to')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="receipt" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Receipt</label>
        <input type="file" name="receipt" id="receipt" accept=".pdf,.jpg,.jpeg,.png"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('receipt')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
        @if($editing && $pettyCash->receipt_path)
            <div style="margin-top: 8px;">
                <a href="{{ asset('storage/' . $pettyCash->receipt_path) }}" target="_blank" style="color: #667eea; text-decoration: none;">
                    <i class="fas fa-file"></i> View Current Receipt
                </a>
            </div>
        @endif
        <small style="color: #666; font-size: 12px; display: block; margin-top: 4px;">Accepted formats: PDF, JPG, PNG (Max 5MB)</small>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <label for="remarks" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Remarks</label>
    <textarea name="remarks" id="remarks" rows="3"
              placeholder="Additional remarks or comments (optional)"
              style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; font-family: inherit;">{{ old('remarks', $editing ? $pettyCash->remarks : '') }}</textarea>
    @error('remarks')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

