@php
    $editing = isset($production);
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <div>
        <label for="work_order_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Work Order Number</label>
        <select name="work_order_id" id="work_order_id"
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Work Order --</option>
            @foreach($workOrders as $wo)
                <option value="{{ $wo->id }}"
                    {{ old('work_order_id', $editing ? $production->work_order_id : optional($selectedWorkOrder ?? null)->id) == $wo->id ? 'selected' : '' }}>
                    {{ $wo->work_order_number }}
                </option>
            @endforeach
        </select>
        @error('work_order_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="product_id" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Product Name <span style="color:red">*</span></label>
        <select name="product_id" id="product_id" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
            <option value="">-- Select Product --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}"
                    {{ old('product_id', $editing ? $production->product_id : optional($selectedWorkOrder ?? null)->product_id) == $product->id ? 'selected' : '' }}>
                    {{ $product->product_name }} ({{ $product->code }})
                </option>
            @endforeach
        </select>
        @error('product_id')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="produced_quantity" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Produced Quantity <span style="color:red">*</span></label>
        <input type="number" step="0.001" min="0" name="produced_quantity" id="produced_quantity" required
               value="{{ old('produced_quantity', $editing ? $production->produced_quantity : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('produced_quantity')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="weight_per_unit" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Weight of 1 Bag/Unit <span style="color:red">*</span></label>
        <input type="number" step="0.001" min="0" name="weight_per_unit" id="weight_per_unit" required
               value="{{ old('weight_per_unit', $editing ? $production->weight_per_unit : '') }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
        @error('weight_per_unit')
            <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Total Weight Produced</label>
        <input type="text" id="total_weight_view" disabled
               value="{{ $editing ? number_format($production->total_weight, 3) : '' }}"
               style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd; background: #f5f5f5;">
    </div>
</div>

<div style="margin-bottom: 20px;">
    <label for="remarks" style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Remarks</label>
    <textarea name="remarks" id="remarks" rows="3"
              style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">{{ old('remarks', $editing ? $production->remarks : '') }}</textarea>
    @error('remarks')
        <div style="color: red; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
    @enderror
</div>

<input type="hidden" name="total_weight" id="total_weight">

@push('scripts')
<script>
    function parseNumber(value) {
        var n = parseFloat(value);
        return isNaN(n) ? 0 : n;
    }

    function recalcTotalWeight() {
        var qty = parseNumber(document.getElementById('produced_quantity').value);
        var wpu = parseNumber(document.getElementById('weight_per_unit').value);
        var total = qty * wpu;
        if (!isNaN(total) && total > 0) {
            document.getElementById('total_weight_view').value = total.toFixed(3);
            document.getElementById('total_weight').value = total.toFixed(3);
        } else {
            document.getElementById('total_weight_view').value = '';
            document.getElementById('total_weight').value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var qtyInput = document.getElementById('produced_quantity');
        var wpuInput = document.getElementById('weight_per_unit');

        if (qtyInput) qtyInput.addEventListener('input', recalcTotalWeight);
        if (wpuInput) wpuInput.addEventListener('input', recalcTotalWeight);

        recalcTotalWeight();
    });
</script>
@endpush


