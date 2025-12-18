# Laravel Woven ERP - Form Requirements with Dependencies and Data Sources

This document outlines all forms that need to be implemented in the Woven ERP system, following Laravel best practices with CRUD operations, table creation, and proper folder structures.

---

## Table of Contents
1. [Master Data Forms](#master-data-forms)
2. [Product Management Forms](#product-management-forms)
3. [Customer & Supplier Forms](#customer--supplier-forms)
4. [Transaction Forms](#transaction-forms)
5. [HR Management Forms](#hr-management-forms)
6. [Production Management Forms](#production-management-forms)
7. [Purchase Management Forms](#purchase-management-forms)
8. [Reports & Analytics](#reports--analytics)

---

## Master Data Forms

### 1. Units Form

**Purpose**: Manage measurement units (kg, meters, pieces, etc.)

**Database Migration**:
```php
Schema::create('units', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // e.g., "Kilogram", "Meter"
    $table->string('code')->unique(); // e.g., "KG", "M"
    $table->string('symbol')->nullable(); // e.g., "kg", "m"
    $table->enum('type', ['weight', 'length', 'volume', 'count', 'other'])->default('other');
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Unit.php`
- Relationships: `organization()`, `branch()`, `creator()`
- Fillable: `name`, `code`, `symbol`, `type`, `is_active`, `organization_id`, `branch_id`, `created_by`

**Controller**: `app/Http/Controllers/UnitController.php`
- Methods: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`
- Validation: name (required, unique), code (required, unique), type (required, in:weight,length,volume,count,other)

**Views**: `resources/views/masters/units/`
- `index.blade.php` - List all units with pagination, search, sort
- `create.blade.php` - Create form
- `edit.blade.php` - Edit form
- `show.blade.php` - View details

**Routes**: 
```php
Route::resource('units', UnitController::class);
```

**Dependencies**: 
- Organizations table
- Branches table
- Users table

**Data Sources**:
- Direct user input
- No foreign key dependencies

---

### 2. Tax Form

**Purpose**: Manage tax rates and tax configurations

**Database Migration**:
```php
Schema::create('taxes', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // e.g., "GST 18%", "VAT 5%"
    $table->string('code')->unique();
    $table->decimal('rate', 5, 2); // e.g., 18.00 for 18%
    $table->enum('type', ['gst', 'vat', 'cgst', 'sgst', 'igst', 'other'])->default('gst');
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Tax.php`
- Relationships: `organization()`, `branch()`, `creator()`
- Fillable: `name`, `code`, `rate`, `type`, `description`, `is_active`, `organization_id`, `branch_id`, `created_by`

**Controller**: `app/Http/Controllers/TaxController.php`
- Methods: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`
- Validation: name (required), code (required, unique), rate (required, numeric, min:0, max:100), type (required)

**Views**: `resources/views/masters/taxes/`
- `index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`

**Routes**: 
```php
Route::resource('taxes', TaxController::class);
```

**Dependencies**: 
- Organizations table
- Branches table
- Users table

---

### 3. Discounts Form

**Purpose**: Manage discount rates and discount configurations

**Database Migration**:
```php
Schema::create('discounts', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // e.g., "Bulk Discount 10%", "Seasonal Discount"
    $table->string('code')->unique();
    $table->enum('type', ['percentage', 'fixed'])->default('percentage');
    $table->decimal('value', 10, 2); // Percentage or fixed amount
    $table->decimal('min_purchase_amount', 10, 2)->nullable();
    $table->date('valid_from')->nullable();
    $table->date('valid_to')->nullable();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Discount.php`
- Relationships: `organization()`, `branch()`, `creator()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/DiscountController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique), type (required), value (required, numeric, min:0)

**Views**: `resources/views/masters/discounts/`
- All CRUD views

**Routes**: 
```php
Route::resource('discounts', DiscountController::class);
```

**Dependencies**: 
- Organizations, Branches, Users tables

---

## Product Management Forms

### 4. Raw Material Categories Form

**Purpose**: Categorize raw materials (e.g., Yarn, Dye, Chemicals)

**Database Migration**:
```php
Schema::create('raw_material_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/RawMaterialCategory.php`
- Relationships: `organization()`, `branch()`, `creator()`, `subCategories()`, `rawMaterials()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/RawMaterialCategoryController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique)

**Views**: `resources/views/masters/raw-material-categories/`
- All CRUD views

**Routes**: 
```php
Route::resource('raw-material-categories', RawMaterialCategoryController::class);
```

**Dependencies**: 
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Used by: Raw Material Sub Categories, Raw Materials

---

### 5. Raw Material Sub Categories Form

**Purpose**: Sub-categorize raw materials within categories

**Database Migration**:
```php
Schema::create('raw_material_sub_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->unsignedBigInteger('raw_material_category_id');
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('raw_material_category_id')->references('id')->on('raw_material_categories');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/RawMaterialSubCategory.php`
- Relationships: `category()`, `organization()`, `branch()`, `creator()`, `rawMaterials()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/RawMaterialSubCategoryController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique), raw_material_category_id (required, exists)

**Views**: `resources/views/masters/raw-material-sub-categories/`
- All CRUD views (with category dropdown in create/edit)

**Routes**: 
```php
Route::resource('raw-material-sub-categories', RawMaterialSubCategoryController::class);
```

**Dependencies**: 
- Raw Material Categories table
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Raw Material Categories (dropdown)

---

### 6. Raw Materials Form

**Purpose**: Manage raw materials inventory

**Database Migration**:
```php
Schema::create('raw_materials', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->unsignedBigInteger('raw_material_category_id');
    $table->unsignedBigInteger('raw_material_sub_category_id')->nullable();
    $table->unsignedBigInteger('unit_id'); // Unit of measurement
    $table->text('description')->nullable();
    $table->string('specification')->nullable();
    $table->decimal('min_stock_level', 10, 2)->default(0);
    $table->decimal('max_stock_level', 10, 2)->nullable();
    $table->decimal('reorder_level', 10, 2)->default(0);
    $table->decimal('current_stock', 10, 2)->default(0);
    $table->decimal('cost_price', 10, 2)->default(0);
    $table->string('supplier_name')->nullable();
    $table->string('supplier_code')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('raw_material_category_id')->references('id')->on('raw_material_categories');
    $table->foreign('raw_material_sub_category_id')->references('id')->on('raw_material_sub_categories');
    $table->foreign('unit_id')->references('id')->on('units');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/RawMaterial.php`
- Relationships: `category()`, `subCategory()`, `unit()`, `organization()`, `branch()`, `creator()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/RawMaterialController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique), raw_material_category_id (required), unit_id (required)

**Views**: `resources/views/masters/raw-materials/`
- All CRUD views (with category, sub-category, unit dropdowns)

**Routes**: 
```php
Route::resource('raw-materials', RawMaterialController::class);
```

**Dependencies**: 
- Raw Material Categories table
- Raw Material Sub Categories table
- Units table
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Raw Material Categories (dropdown)
- Raw Material Sub Categories (dropdown, filtered by category)
- Units (dropdown)

---

### 7. Product Categories Form

**Purpose**: Categorize finished products

**Database Migration**:
```php
Schema::create('product_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/ProductCategory.php`
- Relationships: `organization()`, `branch()`, `creator()`, `products()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/ProductCategoryController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique)

**Views**: `resources/views/masters/product-categories/`
- All CRUD views

**Routes**: 
```php
Route::resource('product-categories', ProductCategoryController::class);
```

**Dependencies**: 
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Used by: Products table

---

### 8. Products Form

**Purpose**: Manage finished products inventory

**Database Migration**:
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->string('sku')->unique()->nullable();
    $table->unsignedBigInteger('product_category_id');
    $table->unsignedBigInteger('unit_id');
    $table->text('description')->nullable();
    $table->text('specification')->nullable();
    $table->decimal('selling_price', 10, 2)->default(0);
    $table->decimal('cost_price', 10, 2)->default(0);
    $table->decimal('min_stock_level', 10, 2)->default(0);
    $table->decimal('max_stock_level', 10, 2)->nullable();
    $table->decimal('reorder_level', 10, 2)->default(0);
    $table->decimal('current_stock', 10, 2)->default(0);
    $table->string('image')->nullable();
    $table->json('images')->nullable(); // Multiple images
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('product_category_id')->references('id')->on('product_categories');
    $table->foreign('unit_id')->references('id')->on('units');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Product.php`
- Relationships: `category()`, `unit()`, `organization()`, `branch()`, `creator()`, `bomProcesses()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/ProductController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique), sku (nullable, unique), product_category_id (required), unit_id (required)

**Views**: `resources/views/masters/products/`
- All CRUD views (with category, unit dropdowns, image upload)

**Routes**: 
```php
Route::resource('products', ProductController::class);
```

**Dependencies**: 
- Product Categories table
- Units table
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Product Categories (dropdown)
- Units (dropdown)
- Image uploads

---

## Customer & Supplier Forms

### 9. Customers Form

**Purpose**: Manage customer master data

**Database Migration**:
```php
Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->string('company_name')->nullable();
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->string('mobile')->nullable();
    $table->text('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('country')->nullable();
    $table->string('pincode')->nullable();
    $table->string('gstin')->nullable();
    $table->string('pan')->nullable();
    $table->enum('customer_type', ['retail', 'wholesale', 'corporate', 'other'])->default('retail');
    $table->decimal('credit_limit', 10, 2)->default(0);
    $table->integer('credit_days')->default(0);
    $table->text('notes')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Customer.php`
- Relationships: `organization()`, `branch()`, `creator()`, `billingAddresses()`, `quotations()`, `proformaInvoices()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/CustomerController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique), email (nullable, email), gstin (nullable, size:15)

**Views**: `resources/views/masters/customers/`
- All CRUD views

**Routes**: 
```php
Route::resource('customers', CustomerController::class);
```

**Dependencies**: 
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Used by: Billing Addresses, Quotations, Proforma Invoices

---

### 10. Suppliers Form

**Purpose**: Manage supplier master data

**Database Migration**:
```php
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->string('company_name')->nullable();
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->string('mobile')->nullable();
    $table->text('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('country')->nullable();
    $table->string('pincode')->nullable();
    $table->string('gstin')->nullable();
    $table->string('pan')->nullable();
    $table->enum('supplier_type', ['manufacturer', 'trader', 'distributor', 'other'])->default('trader');
    $table->text('notes')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Supplier.php`
- Relationships: `organization()`, `branch()`, `creator()`, `purchaseIndents()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/SupplierController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique), email (nullable, email)

**Views**: `resources/views/masters/suppliers/`
- All CRUD views

**Routes**: 
```php
Route::resource('suppliers', SupplierController::class);
```

**Dependencies**: 
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Used by: Purchase Indents

---

### 11. Billing Addresses Form

**Purpose**: Manage multiple billing addresses for customers

**Database Migration**:
```php
Schema::create('billing_addresses', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('customer_id');
    $table->string('address_name'); // e.g., "Head Office", "Branch Office"
    $table->string('contact_person')->nullable();
    $table->string('email')->nullable();
    $table->string('phone')->nullable();
    $table->string('mobile')->nullable();
    $table->text('address');
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('country')->nullable();
    $table->string('pincode')->nullable();
    $table->string('gstin')->nullable();
    $table->boolean('is_default')->default(false);
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('customer_id')->references('id')->on('customers');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/BillingAddress.php`
- Relationships: `customer()`, `organization()`, `branch()`, `creator()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/BillingAddressController.php`
- Methods: Full CRUD
- Validation: customer_id (required, exists), address_name (required), address (required)

**Views**: `resources/views/masters/billing-addresses/`
- All CRUD views (with customer dropdown)

**Routes**: 
```php
Route::resource('billing-addresses', BillingAddressController::class);
```

**Dependencies**: 
- Customers table
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Customers (dropdown)

---

## Transaction Forms

### 12. Quotations Form

**Purpose**: Create and manage customer quotations

**Database Migration**:
```php
Schema::create('quotations', function (Blueprint $table) {
    $table->id();
    $table->string('quotation_number')->unique();
    $table->date('quotation_date');
    $table->date('valid_until')->nullable();
    $table->unsignedBigInteger('customer_id');
    $table->unsignedBigInteger('billing_address_id')->nullable();
    $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
    $table->decimal('subtotal', 10, 2)->default(0);
    $table->decimal('tax_amount', 10, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('total_amount', 10, 2)->default(0);
    $table->text('terms_conditions')->nullable();
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('customer_id')->references('id')->on('customers');
    $table->foreign('billing_address_id')->references('id')->on('billing_addresses');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});

Schema::create('quotation_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('quotation_id');
    $table->unsignedBigInteger('product_id');
    $table->string('description')->nullable();
    $table->decimal('quantity', 10, 2);
    $table->decimal('unit_price', 10, 2);
    $table->decimal('discount_percentage', 5, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('tax_percentage', 5, 2)->default(0);
    $table->decimal('tax_amount', 10, 2)->default(0);
    $table->decimal('total_amount', 10, 2);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    
    $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
    $table->foreign('product_id')->references('id')->on('products');
});
```

**Model**: `app/Models/Quotation.php`
- Relationships: `customer()`, `billingAddress()`, `organization()`, `branch()`, `creator()`, `items()`
- Fillable: All fields

**Model**: `app/Models/QuotationItem.php`
- Relationships: `quotation()`, `product()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/QuotationController.php`
- Methods: Full CRUD
- Validation: quotation_date (required, date), customer_id (required, exists), items (required, array, min:1)

**Views**: `resources/views/transactions/quotations/`
- All CRUD views (with customer, product dropdowns, dynamic item rows)

**Routes**: 
```php
Route::resource('quotations', QuotationController::class);
Route::post('quotations/{quotation}/send', [QuotationController::class, 'send'])->name('quotations.send');
Route::post('quotations/{quotation}/convert-to-proforma', [QuotationController::class, 'convertToProforma'])->name('quotations.convert-to-proforma');
```

**Dependencies**: 
- Customers table
- Billing Addresses table
- Products table
- Organizations, Branches, Users tables

**Data Sources**:
- Customers (dropdown)
- Billing Addresses (dropdown, filtered by customer)
- Products (dropdown)
- Auto-generated quotation number

---

### 13. Proforma Invoices Form

**Purpose**: Create and manage proforma invoices

**Database Migration**:
```php
Schema::create('proforma_invoices', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number')->unique();
    $table->date('invoice_date');
    $table->unsignedBigInteger('customer_id');
    $table->unsignedBigInteger('billing_address_id')->nullable();
    $table->unsignedBigInteger('quotation_id')->nullable(); // If converted from quotation
    $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');
    $table->decimal('subtotal', 10, 2)->default(0);
    $table->decimal('tax_amount', 10, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('total_amount', 10, 2)->default(0);
    $table->text('terms_conditions')->nullable();
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('customer_id')->references('id')->on('customers');
    $table->foreign('billing_address_id')->references('id')->on('billing_addresses');
    $table->foreign('quotation_id')->references('id')->on('quotations');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});

Schema::create('proforma_invoice_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('proforma_invoice_id');
    $table->unsignedBigInteger('product_id');
    $table->string('description')->nullable();
    $table->decimal('quantity', 10, 2);
    $table->decimal('unit_price', 10, 2);
    $table->decimal('discount_percentage', 5, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);
    $table->decimal('tax_percentage', 5, 2)->default(0);
    $table->decimal('tax_amount', 10, 2)->default(0);
    $table->decimal('total_amount', 10, 2);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    
    $table->foreign('proforma_invoice_id')->references('id')->on('proforma_invoices')->onDelete('cascade');
    $table->foreign('product_id')->references('id')->on('products');
});
```

**Model**: `app/Models/ProformaInvoice.php`
- Relationships: `customer()`, `billingAddress()`, `quotation()`, `organization()`, `branch()`, `creator()`, `items()`
- Fillable: All fields

**Model**: `app/Models/ProformaInvoiceItem.php`
- Relationships: `proformaInvoice()`, `product()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/ProformaInvoiceController.php`
- Methods: Full CRUD
- Validation: invoice_date (required, date), customer_id (required, exists), items (required, array, min:1)

**Views**: `resources/views/transactions/proforma-invoices/`
- All CRUD views (with customer, product dropdowns, dynamic item rows)

**Routes**: 
```php
Route::resource('proforma-invoices', ProformaInvoiceController::class);
Route::post('proforma-invoices/{proformaInvoice}/send', [ProformaInvoiceController::class, 'send'])->name('proforma-invoices.send');
Route::get('proforma-invoices/{proformaInvoice}/print', [ProformaInvoiceController::class, 'print'])->name('proforma-invoices.print');
```

**Dependencies**: 
- Customers table
- Billing Addresses table
- Products table
- Quotations table (optional)
- Organizations, Branches, Users tables

**Data Sources**:
- Customers (dropdown)
- Billing Addresses (dropdown, filtered by customer)
- Products (dropdown)
- Quotations (dropdown, for conversion)
- Auto-generated invoice number

---

### 14. Tenders Form

**Purpose**: Manage tender documents and submissions

**Database Migration**:
```php
Schema::create('tenders', function (Blueprint $table) {
    $table->id();
    $table->string('tender_number')->unique();
    $table->string('title');
    $table->text('description')->nullable();
    $table->date('tender_date');
    $table->date('submission_deadline');
    $table->date('opening_date')->nullable();
    $table->decimal('tender_value', 10, 2)->nullable();
    $table->enum('status', ['draft', 'published', 'submitted', 'opened', 'awarded', 'rejected', 'cancelled'])->default('draft');
    $table->string('tender_document')->nullable();
    $table->text('terms_conditions')->nullable();
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Tender.php`
- Relationships: `organization()`, `branch()`, `creator()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/TenderController.php`
- Methods: Full CRUD
- Validation: tender_number (required, unique), title (required), tender_date (required, date), submission_deadline (required, date, after:tender_date)

**Views**: `resources/views/transactions/tenders/`
- All CRUD views (with document upload)

**Routes**: 
```php
Route::resource('tenders', TenderController::class);
Route::post('tenders/{tender}/submit', [TenderController::class, 'submit'])->name('tenders.submit');
```

**Dependencies**: 
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Document uploads
- Auto-generated tender number

---

### 15. Customer Complaints Form

**Purpose**: Track and manage customer complaints

**Database Migration**:
```php
Schema::create('customer_complaints', function (Blueprint $table) {
    $table->id();
    $table->string('complaint_number')->unique();
    $table->date('complaint_date');
    $table->unsignedBigInteger('customer_id');
    $table->unsignedBigInteger('product_id')->nullable();
    $table->unsignedBigInteger('quotation_id')->nullable();
    $table->unsignedBigInteger('proforma_invoice_id')->nullable();
    $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
    $table->enum('status', ['open', 'in_progress', 'resolved', 'closed', 'cancelled'])->default('open');
    $table->text('complaint_description');
    $table->text('resolution')->nullable();
    $table->date('resolved_date')->nullable();
    $table->unsignedBigInteger('assigned_to')->nullable();
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('customer_id')->references('id')->on('customers');
    $table->foreign('product_id')->references('id')->on('products');
    $table->foreign('quotation_id')->references('id')->on('quotations');
    $table->foreign('proforma_invoice_id')->references('id')->on('proforma_invoices');
    $table->foreign('assigned_to')->references('id')->on('users');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/CustomerComplaint.php`
- Relationships: `customer()`, `product()`, `quotation()`, `proformaInvoice()`, `assignedTo()`, `organization()`, `branch()`, `creator()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/CustomerComplaintController.php`
- Methods: Full CRUD
- Validation: complaint_date (required, date), customer_id (required, exists), complaint_description (required)

**Views**: `resources/views/transactions/customer-complaints/`
- All CRUD views (with customer, product, quotation, proforma invoice dropdowns)

**Routes**: 
```php
Route::resource('customer-complaints', CustomerComplaintController::class);
Route::post('customer-complaints/{complaint}/resolve', [CustomerComplaintController::class, 'resolve'])->name('customer-complaints.resolve');
```

**Dependencies**: 
- Customers table
- Products table (optional)
- Quotations table (optional)
- Proforma Invoices table (optional)
- Users table (for assignment)
- Organizations, Branches tables

**Data Sources**:
- Customers (dropdown)
- Products (dropdown)
- Quotations (dropdown, filtered by customer)
- Proforma Invoices (dropdown, filtered by customer)
- Users (dropdown, for assignment)
- Auto-generated complaint number

---

## HR Management Forms

### 16. Departments Form

**Purpose**: Manage organizational departments

**Database Migration**:
```php
Schema::create('departments', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->text('description')->nullable();
    $table->unsignedBigInteger('head_of_department')->nullable(); // User ID
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('head_of_department')->references('id')->on('users');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Department.php`
- Relationships: `headOfDepartment()`, `organization()`, `branch()`, `creator()`, `employees()`, `designations()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/DepartmentController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique)

**Views**: `resources/views/masters/departments/`
- All CRUD views (with head of department user dropdown)

**Routes**: 
```php
Route::resource('departments', DepartmentController::class);
```

**Dependencies**: 
- Users table
- Organizations, Branches tables

**Data Sources**:
- Direct user input
- Users (dropdown, for head of department)
- Used by: Designations, Employees

---

### 17. Designations Form

**Purpose**: Manage job designations/titles

**Database Migration**:
```php
Schema::create('designations', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->unsignedBigInteger('department_id')->nullable();
    $table->text('description')->nullable();
    $table->decimal('min_salary', 10, 2)->nullable();
    $table->decimal('max_salary', 10, 2)->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('department_id')->references('id')->on('departments');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Designation.php`
- Relationships: `department()`, `organization()`, `branch()`, `creator()`, `employees()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/DesignationController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique), department_id (nullable, exists)

**Views**: `resources/views/masters/designations/`
- All CRUD views (with department dropdown)

**Routes**: 
```php
Route::resource('designations', DesignationController::class);
```

**Dependencies**: 
- Departments table
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Departments (dropdown)
- Used by: Employees

---

### 18. Production Departments Form

**Purpose**: Manage production-specific departments

**Database Migration**:
```php
Schema::create('production_departments', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/ProductionDepartment.php`
- Relationships: `organization()`, `branch()`, `creator()`, `processes()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/ProductionDepartmentController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique)

**Views**: `resources/views/masters/production-departments/`
- All CRUD views

**Routes**: 
```php
Route::resource('production-departments', ProductionDepartmentController::class);
```

**Dependencies**: 
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Used by: Processes

---

### 19. Employees Form

**Purpose**: Manage employee master data

**Database Migration**:
```php
Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->string('employee_code')->unique();
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email')->unique()->nullable();
    $table->string('phone')->nullable();
    $table->string('mobile')->nullable();
    $table->date('date_of_birth')->nullable();
    $table->enum('gender', ['male', 'female', 'other'])->nullable();
    $table->text('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('country')->nullable();
    $table->string('pincode')->nullable();
    $table->unsignedBigInteger('department_id')->nullable();
    $table->unsignedBigInteger('designation_id')->nullable();
    $table->date('joining_date')->nullable();
    $table->decimal('salary', 10, 2)->nullable();
    $table->string('pan')->nullable();
    $table->string('aadhar')->nullable();
    $table->string('bank_account_number')->nullable();
    $table->string('bank_ifsc')->nullable();
    $table->string('bank_name')->nullable();
    $table->enum('employment_type', ['permanent', 'contract', 'temporary', 'intern'])->default('permanent');
    $table->enum('status', ['active', 'inactive', 'terminated', 'resigned'])->default('active');
    $table->string('photo')->nullable();
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('department_id')->references('id')->on('departments');
    $table->foreign('designation_id')->references('id')->on('designations');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Employee.php`
- Relationships: `department()`, `designation()`, `organization()`, `branch()`, `creator()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/EmployeeController.php`
- Methods: Full CRUD
- Validation: employee_code (required, unique), first_name (required), email (nullable, email, unique), department_id (nullable, exists), designation_id (nullable, exists)

**Views**: `resources/views/masters/employees/`
- All CRUD views (with department, designation dropdowns, photo upload)

**Routes**: 
```php
Route::resource('employees', EmployeeController::class);
```

**Dependencies**: 
- Departments table
- Designations table
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Departments (dropdown)
- Designations (dropdown, filtered by department)
- Photo uploads

---

## Production Management Forms

### 20. Processes Form

**Purpose**: Manage production processes

**Database Migration**:
```php
Schema::create('processes', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->unsignedBigInteger('production_department_id')->nullable();
    $table->text('description')->nullable();
    $table->decimal('standard_time', 10, 2)->nullable(); // in hours
    $table->decimal('cost_per_unit', 10, 2)->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('production_department_id')->references('id')->on('production_departments');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
});
```

**Model**: `app/Models/Process.php`
- Relationships: `productionDepartment()`, `organization()`, `branch()`, `creator()`, `bomProcesses()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/ProcessController.php`
- Methods: Full CRUD
- Validation: name (required), code (required, unique), production_department_id (nullable, exists)

**Views**: `resources/views/masters/processes/`
- All CRUD views (with production department dropdown)

**Routes**: 
```php
Route::resource('processes', ProcessController::class);
```

**Dependencies**: 
- Production Departments table
- Organizations, Branches, Users tables

**Data Sources**:
- Direct user input
- Production Departments (dropdown)
- Used by: BOM Processes

---

### 21. BOM Processes Form

**Purpose**: Manage Bill of Materials (BOM) with processes for products

**Database Migration**:
```php
Schema::create('bom_processes', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id');
    $table->unsignedBigInteger('process_id');
    $table->unsignedBigInteger('raw_material_id')->nullable();
    $table->decimal('quantity', 10, 2)->default(0);
    $table->decimal('wastage_percentage', 5, 2)->default(0);
    $table->decimal('process_time', 10, 2)->nullable(); // in hours
    $table->decimal('cost', 10, 2)->default(0);
    $table->integer('sequence_order')->default(0);
    $table->text('notes')->nullable();
    $table->boolean('is_active')->default(true);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('product_id')->references('id')->on('products');
    $table->foreign('process_id')->references('id')->on('processes');
    $table->foreign('raw_material_id')->references('id')->on('raw_materials');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
    
    $table->unique(['product_id', 'process_id', 'raw_material_id'], 'unique_bom_process');
});
```

**Model**: `app/Models/BomProcess.php`
- Relationships: `product()`, `process()`, `rawMaterial()`, `organization()`, `branch()`, `creator()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/BomProcessController.php`
- Methods: Full CRUD
- Validation: product_id (required, exists), process_id (required, exists), quantity (required, numeric, min:0)

**Views**: `resources/views/masters/bom-processes/`
- All CRUD views (with product, process, raw material dropdowns)

**Routes**: 
```php
Route::resource('bom-processes', BomProcessController::class);
Route::get('products/{product}/bom', [BomProcessController::class, 'showByProduct'])->name('products.bom');
```

**Dependencies**: 
- Products table
- Processes table
- Raw Materials table
- Organizations, Branches, Users tables

**Data Sources**:
- Products (dropdown)
- Processes (dropdown)
- Raw Materials (dropdown)
- Direct user input for quantities and costs

---

## Purchase Management Forms

### 22. Purchase Indents Form

**Purpose**: Manage purchase indent/requisition documents

**Database Migration**:
```php
Schema::create('purchase_indents', function (Blueprint $table) {
    $table->id();
    $table->string('indent_number')->unique();
    $table->date('indent_date');
    $table->date('required_date')->nullable();
    $table->unsignedBigInteger('supplier_id')->nullable();
    $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'ordered', 'received', 'cancelled'])->default('draft');
    $table->text('purpose')->nullable();
    $table->text('notes')->nullable();
    $table->decimal('total_amount', 10, 2)->default(0);
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('approved_by')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('supplier_id')->references('id')->on('suppliers');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
    $table->foreign('approved_by')->references('id')->on('users');
});

Schema::create('purchase_indent_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('purchase_indent_id');
    $table->unsignedBigInteger('raw_material_id');
    $table->string('description')->nullable();
    $table->decimal('quantity', 10, 2);
    $table->decimal('unit_price', 10, 2)->default(0);
    $table->decimal('total_amount', 10, 2);
    $table->text('specification')->nullable();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    
    $table->foreign('purchase_indent_id')->references('id')->on('purchase_indents')->onDelete('cascade');
    $table->foreign('raw_material_id')->references('id')->on('raw_materials');
});
```

**Model**: `app/Models/PurchaseIndent.php`
- Relationships: `supplier()`, `organization()`, `branch()`, `creator()`, `approvedBy()`, `items()`
- Fillable: All fields
- Uses: `HasApprovalStatus` trait

**Model**: `app/Models/PurchaseIndentItem.php`
- Relationships: `purchaseIndent()`, `rawMaterial()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/PurchaseIndentController.php`
- Methods: Full CRUD
- Validation: indent_date (required, date), items (required, array, min:1)
- Additional methods: `approve()`, `reject()`

**Views**: `resources/views/transactions/purchase-indents/`
- All CRUD views (with supplier, raw material dropdowns, dynamic item rows)

**Routes**: 
```php
Route::resource('purchase-indents', PurchaseIndentController::class);
Route::post('purchase-indents/{purchaseIndent}/approve', [PurchaseIndentController::class, 'approve'])->name('purchase-indents.approve');
Route::post('purchase-indents/{purchaseIndent}/reject', [PurchaseIndentController::class, 'reject'])->name('purchase-indents.reject');
```

**Dependencies**: 
- Suppliers table
- Raw Materials table
- Organizations, Branches, Users tables
- HasApprovalStatus trait

**Data Sources**:
- Suppliers (dropdown)
- Raw Materials (dropdown)
- Direct user input
- Auto-generated indent number

---

### 23. Subcontractor Evaluations Form

**Purpose**: Evaluate and manage subcontractor performance

**Database Migration**:
```php
Schema::create('subcontractor_evaluations', function (Blueprint $table) {
    $table->id();
    $table->string('evaluation_number')->unique();
    $table->date('evaluation_date');
    $table->unsignedBigInteger('supplier_id'); // Subcontractor is a type of supplier
    $table->enum('evaluation_type', ['quality', 'delivery', 'price', 'overall'])->default('overall');
    $table->integer('rating')->default(0); // 1-5 or 1-10 scale
    $table->text('strengths')->nullable();
    $table->text('weaknesses')->nullable();
    $table->text('recommendations')->nullable();
    $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
    $table->text('notes')->nullable();
    $table->unsignedBigInteger('organization_id')->nullable();
    $table->unsignedBigInteger('branch_id')->nullable();
    $table->unsignedBigInteger('created_by')->nullable();
    $table->unsignedBigInteger('evaluated_by')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->foreign('supplier_id')->references('id')->on('suppliers');
    $table->foreign('organization_id')->references('id')->on('organizations');
    $table->foreign('branch_id')->references('id')->on('branches');
    $table->foreign('created_by')->references('id')->on('users');
    $table->foreign('evaluated_by')->references('id')->on('users');
});
```

**Model**: `app/Models/SubcontractorEvaluation.php`
- Relationships: `supplier()`, `organization()`, `branch()`, `creator()`, `evaluatedBy()`
- Fillable: All fields

**Controller**: `app/Http/Controllers/SubcontractorEvaluationController.php`
- Methods: Full CRUD
- Validation: evaluation_date (required, date), supplier_id (required, exists), rating (required, integer, min:1, max:10)

**Views**: `resources/views/transactions/subcontractor-evaluations/`
- All CRUD views (with supplier dropdown)

**Routes**: 
```php
Route::resource('subcontractor-evaluations', SubcontractorEvaluationController::class);
```

**Dependencies**: 
- Suppliers table
- Organizations, Branches, Users tables

**Data Sources**:
- Suppliers (dropdown)
- Direct user input
- Auto-generated evaluation number

---

## Reports & Analytics

### 24. Reports Form

**Purpose**: Generate various reports (this is more of a module than a CRUD form)

**Note**: Reports typically don't have a traditional CRUD structure. Instead, they have:
- Report configuration tables (optional)
- Report generation controllers
- Report view templates

**Common Reports to Implement**:
1. Sales Reports (Quotations, Proforma Invoices)
2. Purchase Reports (Purchase Indents)
3. Inventory Reports (Products, Raw Materials)
4. Customer Reports
5. Supplier Reports
6. Production Reports
7. Financial Reports

**Implementation Approach**:
- Create `ReportController` with methods for each report type
- Use query builders to generate data
- Export to PDF/Excel using Laravel packages (dompdf, maatwebsite/excel)
- Create view templates for each report

**Routes**: 
```php
Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/purchase', [ReportController::class, 'purchase'])->name('reports.purchase');
    Route::get('/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    // ... more report routes
});
```

**Dependencies**: 
- All transaction and master data tables
- PDF/Excel export packages

---

## Implementation Checklist

For each form, implement in this order:

1. ✅ **Migration**: Create database migration file
2. ✅ **Model**: Create Eloquent model with relationships
3. ✅ **Controller**: Create controller with CRUD methods
4. ✅ **Request Validation**: Create Form Request classes (optional but recommended)
5. ✅ **Routes**: Add resource routes
6. ✅ **Views**: Create Blade templates (index, create, edit, show)
7. ✅ **Seeder**: Update MenuFormSeeder to add form entry
8. ✅ **Permissions**: Already seeded in ModuleActionPermissionSeeder
9. ✅ **Testing**: Test all CRUD operations

---

## Folder Structure Summary

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── UnitController.php
│   │   ├── TaxController.php
│   │   ├── DiscountController.php
│   │   ├── RawMaterialCategoryController.php
│   │   ├── RawMaterialSubCategoryController.php
│   │   ├── RawMaterialController.php
│   │   ├── ProductCategoryController.php
│   │   ├── ProductController.php
│   │   ├── CustomerController.php
│   │   ├── SupplierController.php
│   │   ├── BillingAddressController.php
│   │   ├── QuotationController.php
│   │   ├── ProformaInvoiceController.php
│   │   ├── TenderController.php
│   │   ├── CustomerComplaintController.php
│   │   ├── DepartmentController.php
│   │   ├── DesignationController.php
│   │   ├── ProductionDepartmentController.php
│   │   ├── EmployeeController.php
│   │   ├── ProcessController.php
│   │   ├── BomProcessController.php
│   │   ├── PurchaseIndentController.php
│   │   ├── SubcontractorEvaluationController.php
│   │   └── ReportController.php
│   └── Requests/ (Optional - for Form Request validation)
│       ├── StoreUnitRequest.php
│       ├── UpdateUnitRequest.php
│       └── ... (one pair per form)
│
├── Models/
│   ├── Unit.php
│   ├── Tax.php
│   ├── Discount.php
│   ├── RawMaterialCategory.php
│   ├── RawMaterialSubCategory.php
│   ├── RawMaterial.php
│   ├── ProductCategory.php
│   ├── Product.php
│   ├── Customer.php
│   ├── Supplier.php
│   ├── BillingAddress.php
│   ├── Quotation.php
│   ├── QuotationItem.php
│   ├── ProformaInvoice.php
│   ├── ProformaInvoiceItem.php
│   ├── Tender.php
│   ├── CustomerComplaint.php
│   ├── Department.php
│   ├── Designation.php
│   ├── ProductionDepartment.php
│   ├── Employee.php
│   ├── Process.php
│   ├── BomProcess.php
│   ├── PurchaseIndent.php
│   ├── PurchaseIndentItem.php
│   └── SubcontractorEvaluation.php

resources/
└── views/
    ├── masters/
    │   ├── units/
    │   ├── taxes/
    │   ├── discounts/
    │   ├── raw-material-categories/
    │   ├── raw-material-sub-categories/
    │   ├── raw-materials/
    │   ├── product-categories/
    │   ├── products/
    │   ├── customers/
    │   ├── suppliers/
    │   ├── billing-addresses/
    │   ├── departments/
    │   ├── designations/
    │   ├── production-departments/
    │   ├── employees/
    │   ├── processes/
    │   └── bom-processes/
    └── transactions/
        ├── quotations/
        ├── proforma-invoices/
        ├── tenders/
        ├── customer-complaints/
        ├── purchase-indents/
        └── subcontractor-evaluations/

database/
└── migrations/
    ├── YYYY_MM_DD_HHMMSS_create_units_table.php
    ├── YYYY_MM_DD_HHMMSS_create_taxes_table.php
    ├── YYYY_MM_DD_HHMMSS_create_discounts_table.php
    ├── YYYY_MM_DD_HHMMSS_create_raw_material_categories_table.php
    ├── YYYY_MM_DD_HHMMSS_create_raw_material_sub_categories_table.php
    ├── YYYY_MM_DD_HHMMSS_create_raw_materials_table.php
    ├── YYYY_MM_DD_HHMMSS_create_product_categories_table.php
    ├── YYYY_MM_DD_HHMMSS_create_products_table.php
    ├── YYYY_MM_DD_HHMMSS_create_customers_table.php
    ├── YYYY_MM_DD_HHMMSS_create_suppliers_table.php
    ├── YYYY_MM_DD_HHMMSS_create_billing_addresses_table.php
    ├── YYYY_MM_DD_HHMMSS_create_quotations_table.php
    ├── YYYY_MM_DD_HHMMSS_create_quotation_items_table.php
    ├── YYYY_MM_DD_HHMMSS_create_proforma_invoices_table.php
    ├── YYYY_MM_DD_HHMMSS_create_proforma_invoice_items_table.php
    ├── YYYY_MM_DD_HHMMSS_create_tenders_table.php
    ├── YYYY_MM_DD_HHMMSS_create_customer_complaints_table.php
    ├── YYYY_MM_DD_HHMMSS_create_departments_table.php
    ├── YYYY_MM_DD_HHMMSS_create_designations_table.php
    ├── YYYY_MM_DD_HHMMSS_create_production_departments_table.php
    ├── YYYY_MM_DD_HHMMSS_create_employees_table.php
    ├── YYYY_MM_DD_HHMMSS_create_processes_table.php
    ├── YYYY_MM_DD_HHMMSS_create_bom_processes_table.php
    ├── YYYY_MM_DD_HHMMSS_create_purchase_indents_table.php
    ├── YYYY_MM_DD_HHMMSS_create_purchase_indent_items_table.php
    └── YYYY_MM_DD_HHMMSS_create_subcontractor_evaluations_table.php
```

---

## Notes

1. **Soft Deletes**: All master data tables should use soft deletes for data integrity
2. **Organization & Branch**: Most tables include organization_id and branch_id for multi-tenancy
3. **Audit Trail**: Consider adding created_by, updated_by, deleted_by fields for audit purposes
4. **Status Fields**: Transaction tables should have status fields for workflow management
5. **Unique Constraints**: Code fields should be unique per organization/branch if needed
6. **Indexes**: Add indexes on frequently queried fields (organization_id, branch_id, status, dates)
7. **Validation**: Implement comprehensive validation in controllers or Form Request classes
8. **Authorization**: Add middleware/authorization checks based on user roles and permissions
9. **File Uploads**: Use Laravel's storage system for file uploads (images, documents)
10. **Number Generation**: Implement auto-numbering for transaction documents (quotations, invoices, etc.)

---

## Next Steps

1. Start with Master Data forms (Units, Tax, Discounts) as they have no dependencies
2. Then implement Product Management forms in order (Categories → Sub Categories → Raw Materials/Products)
3. Implement Customer & Supplier forms
4. Then Transaction forms (they depend on masters)
5. Finally, HR and Production Management forms
6. Reports can be implemented last as they depend on all transaction data

