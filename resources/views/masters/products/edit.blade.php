@extends('layouts.dashboard')

@section('title', 'Edit Product - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Product</h2>
        <a href="{{ route('products.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" id="productForm">
        @csrf
        @method('PUT')

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>
            
            <div style="margin-bottom: 20px;">
                <label for="product_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Product Name <span style="color: red;">*</span></label>
                <input type="text" name="product_name" id="product_name" value="{{ old('product_name', $product->product_name) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter product name">
                @error('product_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label for="unit_of_measure" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Unit of Measure <span style="color: red;">*</span></label>
                    <select name="unit_of_measure" id="unit_of_measure" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select unit</option>
                        <option value="KG" {{ old('unit_of_measure', $product->unit_of_measure) === 'KG' ? 'selected' : '' }}>KG (Kilogram)</option>
                        <option value="G" {{ old('unit_of_measure', $product->unit_of_measure) === 'G' ? 'selected' : '' }}>G (Gram)</option>
                        <option value="MT" {{ old('unit_of_measure', $product->unit_of_measure) === 'MT' ? 'selected' : '' }}>MT (Metric Ton)</option>
                        <option value="Nos" {{ old('unit_of_measure', $product->unit_of_measure) === 'Nos' ? 'selected' : '' }}>Nos (Numbers)</option>
                        <option value="L" {{ old('unit_of_measure', $product->unit_of_measure) === 'L' ? 'selected' : '' }}>L (Liters)</option>
                        <option value="ML" {{ old('unit_of_measure', $product->unit_of_measure) === 'ML' ? 'selected' : '' }}>ML (Milliliters)</option>
                        <option value="M" {{ old('unit_of_measure', $product->unit_of_measure) === 'M' ? 'selected' : '' }}>M (Meters)</option>
                        <option value="CM" {{ old('unit_of_measure', $product->unit_of_measure) === 'CM' ? 'selected' : '' }}>CM (Centimeters)</option>
                        <option value="FT" {{ old('unit_of_measure', $product->unit_of_measure) === 'FT' ? 'selected' : '' }}>FT (Feet)</option>
                        <option value="IN" {{ old('unit_of_measure', $product->unit_of_measure) === 'IN' ? 'selected' : '' }}>IN (Inches)</option>
                        <option value="PCS" {{ old('unit_of_measure', $product->unit_of_measure) === 'PCS' ? 'selected' : '' }}>PCS (Pieces)</option>
                        <option value="BOX" {{ old('unit_of_measure', $product->unit_of_measure) === 'BOX' ? 'selected' : '' }}>BOX (Boxes)</option>
                        <option value="PKT" {{ old('unit_of_measure', $product->unit_of_measure) === 'PKT' ? 'selected' : '' }}>PKT (Packets)</option>
                    </select>
                    @error('unit_of_measure')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="product_category" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Product Category <span style="color: red;">*</span></label>
                    <select name="product_category" id="product_category" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        <option value="">Select category</option>
                        <option value="Electronics" {{ old('product_category', $product->product_category) === 'Electronics' ? 'selected' : '' }}>Electronics</option>
                        <option value="Furniture" {{ old('product_category', $product->product_category) === 'Furniture' ? 'selected' : '' }}>Furniture</option>
                        <option value="Textiles" {{ old('product_category', $product->product_category) === 'Textiles' ? 'selected' : '' }}>Textiles</option>
                        <option value="Apparel" {{ old('product_category', $product->product_category) === 'Apparel' ? 'selected' : '' }}>Apparel</option>
                        <option value="Home & Kitchen" {{ old('product_category', $product->product_category) === 'Home & Kitchen' ? 'selected' : '' }}>Home & Kitchen</option>
                        <option value="Automotive" {{ old('product_category', $product->product_category) === 'Automotive' ? 'selected' : '' }}>Automotive</option>
                        <option value="Sports & Outdoors" {{ old('product_category', $product->product_category) === 'Sports & Outdoors' ? 'selected' : '' }}>Sports & Outdoors</option>
                        <option value="Toys & Games" {{ old('product_category', $product->product_category) === 'Toys & Games' ? 'selected' : '' }}>Toys & Games</option>
                        <option value="Books" {{ old('product_category', $product->product_category) === 'Books' ? 'selected' : '' }}>Books</option>
                        <option value="Health & Beauty" {{ old('product_category', $product->product_category) === 'Health & Beauty' ? 'selected' : '' }}>Health & Beauty</option>
                        <option value="Food & Beverages" {{ old('product_category', $product->product_category) === 'Food & Beverages' ? 'selected' : '' }}>Food & Beverages</option>
                        <option value="Industrial" {{ old('product_category', $product->product_category) === 'Industrial' ? 'selected' : '' }}>Industrial</option>
                        <option value="Other" {{ old('product_category', $product->product_category) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('product_category')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
                <textarea name="description" id="description" rows="3"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"
                    placeholder="Enter description (optional)">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Pricing & Stock Information</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label for="price_per_unit" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Price per Unit <span style="color: red;">*</span></label>
                    <input type="number" name="price_per_unit" id="price_per_unit" value="{{ old('price_per_unit', $product->price_per_unit) }}" step="0.01" min="0" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="0.00">
                    @error('price_per_unit')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="stock_quantity" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Stock Quantity <span style="color: red;">*</span></label>
                    <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" step="0.01" min="0" required
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="0.00">
                    @error('stock_quantity')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="gst_percentage" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST Percentage</label>
                    <input type="number" name="gst_percentage" id="gst_percentage" value="{{ old('gst_percentage', $product->gst_percentage) }}" step="0.01" min="0" max="100"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        placeholder="0.00">
                    @error('gst_percentage')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                    style="width: 18px; height: 18px; cursor: pointer;">
                <span style="color: #333; font-weight: 500;">Active</span>
            </label>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="resetForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Reset
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update
            </button>
            <a href="{{ route('products.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function resetForm() {
        document.getElementById('productForm').reset();
        // Restore original values
        document.getElementById('product_name').value = '{{ addslashes($product->product_name) }}';
        document.getElementById('unit_of_measure').value = '{{ addslashes($product->unit_of_measure) }}';
        document.getElementById('product_category').value = '{{ addslashes($product->product_category) }}';
        document.getElementById('price_per_unit').value = '{{ $product->price_per_unit }}';
        document.getElementById('stock_quantity').value = '{{ $product->stock_quantity }}';
        document.getElementById('gst_percentage').value = '{{ $product->gst_percentage }}';
        document.getElementById('description').value = '{{ addslashes($product->description ?? '') }}';
        document.querySelector('input[name="is_active"]').checked = {{ $product->is_active ? 'true' : 'false' }};
    }
</script>
@endpush
@endsection
