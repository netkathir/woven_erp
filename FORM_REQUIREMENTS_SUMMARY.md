# Laravel Woven ERP - Form Requirements Summary (One by One)

This document lists all forms that need to be implemented, one by one, with their essential requirements.

---

## FORM 1: Units

**Purpose**: Manage measurement units (kg, meters, pieces, etc.)

**Table**: `units`
- Fields: id, name, code (unique), symbol, type (enum), is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_units_table.php`
- Model: `app/Models/Unit.php`
- Controller: `app/Http/Controllers/UnitController.php`
- Views: `resources/views/masters/units/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`
- Route: `Route::resource('units', UnitController::class);`

**Dependencies**: Organizations, Branches, Users tables

---

## FORM 2: Tax

**Purpose**: Manage tax rates and configurations

**Table**: `taxes`
- Fields: id, name, code (unique), rate (decimal), type (enum: gst, vat, cgst, sgst, igst, other), description, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_taxes_table.php`
- Model: `app/Models/Tax.php`
- Controller: `app/Http/Controllers/TaxController.php`
- Views: `resources/views/masters/taxes/` (index, create, edit, show)
- Route: `Route::resource('taxes', TaxController::class);`

**Dependencies**: Organizations, Branches, Users tables

---

## FORM 3: Discounts

**Purpose**: Manage discount rates and configurations

**Table**: `discounts`
- Fields: id, name, code (unique), type (enum: percentage, fixed), value (decimal), min_purchase_amount, valid_from, valid_to, description, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_discounts_table.php`
- Model: `app/Models/Discount.php`
- Controller: `app/Http/Controllers/DiscountController.php`
- Views: `resources/views/masters/discounts/` (index, create, edit, show)
- Route: `Route::resource('discounts', DiscountController::class);`

**Dependencies**: Organizations, Branches, Users tables

---

## FORM 4: Raw Material Categories

**Purpose**: Categorize raw materials (e.g., Yarn, Dye, Chemicals)

**Table**: `raw_material_categories`
- Fields: id, name, code (unique), description, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_raw_material_categories_table.php`
- Model: `app/Models/RawMaterialCategory.php`
- Controller: `app/Http/Controllers/RawMaterialCategoryController.php`
- Views: `resources/views/masters/raw-material-categories/` (index, create, edit, show)
- Route: `Route::resource('raw-material-categories', RawMaterialCategoryController::class);`

**Dependencies**: Organizations, Branches, Users tables

**Used By**: Raw Material Sub Categories, Raw Materials

---

## FORM 5: Raw Material Sub Categories

**Purpose**: Sub-categorize raw materials within categories

**Table**: `raw_material_sub_categories`
- Fields: id, name, code (unique), raw_material_category_id (FK), description, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_raw_material_sub_categories_table.php`
- Model: `app/Models/RawMaterialSubCategory.php`
- Controller: `app/Http/Controllers/RawMaterialSubCategoryController.php`
- Views: `resources/views/masters/raw-material-sub-categories/` (index, create, edit, show)
- Route: `Route::resource('raw-material-sub-categories', RawMaterialSubCategoryController::class);`

**Dependencies**: Raw Material Categories, Organizations, Branches, Users tables

**Used By**: Raw Materials

---

## FORM 6: Raw Materials

**Purpose**: Manage raw materials inventory

**Table**: `raw_materials`
- Fields: id, name, code (unique), raw_material_category_id (FK), raw_material_sub_category_id (FK, nullable), unit_id (FK), description, specification, min_stock_level, max_stock_level, reorder_level, current_stock, cost_price, supplier_name, supplier_code, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_raw_materials_table.php`
- Model: `app/Models/RawMaterial.php`
- Controller: `app/Http/Controllers/RawMaterialController.php`
- Views: `resources/views/masters/raw-materials/` (index, create, edit, show)
- Route: `Route::resource('raw-materials', RawMaterialController::class);`

**Dependencies**: Raw Material Categories, Raw Material Sub Categories, Units, Organizations, Branches, Users tables

**Used By**: BOM Processes, Purchase Indents

---

## FORM 7: Product Categories

**Purpose**: Categorize finished products

**Table**: `product_categories`
- Fields: id, name, code (unique), description, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_product_categories_table.php`
- Model: `app/Models/ProductCategory.php`
- Controller: `app/Http/Controllers/ProductCategoryController.php`
- Views: `resources/views/masters/product-categories/` (index, create, edit, show)
- Route: `Route::resource('product-categories', ProductCategoryController::class);`

**Dependencies**: Organizations, Branches, Users tables

**Used By**: Products

---

## FORM 8: Products

**Purpose**: Manage finished products inventory

**Table**: `products`
- Fields: id, name, code (unique), sku (unique, nullable), product_category_id (FK), unit_id (FK), description, specification, selling_price, cost_price, min_stock_level, max_stock_level, reorder_level, current_stock, image, images (JSON), is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_products_table.php`
- Model: `app/Models/Product.php`
- Controller: `app/Http/Controllers/ProductController.php`
- Views: `resources/views/masters/products/` (index, create, edit, show)
- Route: `Route::resource('products', ProductController::class);`

**Dependencies**: Product Categories, Units, Organizations, Branches, Users tables

**Used By**: Quotations, Proforma Invoices, BOM Processes, Customer Complaints

---

## FORM 9: Customers

**Purpose**: Manage customer master data

**Table**: `customers`
- Fields: id, name, code (unique), company_name, email, phone, mobile, address, city, state, country, pincode, gstin, pan, customer_type (enum: retail, wholesale, corporate, other), credit_limit, credit_days, notes, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_customers_table.php`
- Model: `app/Models/Customer.php`
- Controller: `app/Http/Controllers/CustomerController.php`
- Views: `resources/views/masters/customers/` (index, create, edit, show)
- Route: `Route::resource('customers', CustomerController::class);`

**Dependencies**: Organizations, Branches, Users tables

**Used By**: Billing Addresses, Quotations, Proforma Invoices, Customer Complaints

---

## FORM 10: Suppliers

**Purpose**: Manage supplier master data

**Table**: `suppliers`
- Fields: id, name, code (unique), company_name, email, phone, mobile, address, city, state, country, pincode, gstin, pan, supplier_type (enum: manufacturer, trader, distributor, other), notes, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_suppliers_table.php`
- Model: `app/Models/Supplier.php`
- Controller: `app/Http/Controllers/SupplierController.php`
- Views: `resources/views/masters/suppliers/` (index, create, edit, show)
- Route: `Route::resource('suppliers', SupplierController::class);`

**Dependencies**: Organizations, Branches, Users tables

**Used By**: Purchase Indents, Subcontractor Evaluations

---

## FORM 11: Billing Addresses

**Purpose**: Manage multiple billing addresses for customers

**Table**: `billing_addresses`
- Fields: id, customer_id (FK), address_name, contact_person, email, phone, mobile, address, city, state, country, pincode, gstin, is_default, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_billing_addresses_table.php`
- Model: `app/Models/BillingAddress.php`
- Controller: `app/Http/Controllers/BillingAddressController.php`
- Views: `resources/views/masters/billing-addresses/` (index, create, edit, show)
- Route: `Route::resource('billing-addresses', BillingAddressController::class);`

**Dependencies**: Customers, Organizations, Branches, Users tables

**Used By**: Quotations, Proforma Invoices

---

## FORM 12: Quotations

**Purpose**: Create and manage customer quotations

**Tables**: 
- `quotations`: id, quotation_number (unique), quotation_date, valid_until, customer_id (FK), billing_address_id (FK, nullable), status (enum: draft, sent, accepted, rejected, expired), subtotal, tax_amount, discount_amount, total_amount, terms_conditions, notes, organization_id, branch_id, created_by, timestamps, soft_deletes
- `quotation_items`: id, quotation_id (FK), product_id (FK), description, quantity, unit_price, discount_percentage, discount_amount, tax_percentage, tax_amount, total_amount, sort_order, timestamps

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_quotations_table.php`
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_quotation_items_table.php`
- Model: `app/Models/Quotation.php`
- Model: `app/Models/QuotationItem.php`
- Controller: `app/Http/Controllers/QuotationController.php`
- Views: `resources/views/transactions/quotations/` (index, create, edit, show)
- Route: `Route::resource('quotations', QuotationController::class);`
- Additional Routes: `quotations.send`, `quotations.convert-to-proforma`

**Dependencies**: Customers, Billing Addresses, Products, Organizations, Branches, Users tables

---

## FORM 13: Proforma Invoices

**Purpose**: Create and manage proforma invoices

**Tables**: 
- `proforma_invoices`: id, invoice_number (unique), invoice_date, customer_id (FK), billing_address_id (FK, nullable), quotation_id (FK, nullable), status (enum: draft, sent, paid, cancelled), subtotal, tax_amount, discount_amount, total_amount, terms_conditions, notes, organization_id, branch_id, created_by, timestamps, soft_deletes
- `proforma_invoice_items`: id, proforma_invoice_id (FK), product_id (FK), description, quantity, unit_price, discount_percentage, discount_amount, tax_percentage, tax_amount, total_amount, sort_order, timestamps

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_proforma_invoices_table.php`
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_proforma_invoice_items_table.php`
- Model: `app/Models/ProformaInvoice.php`
- Model: `app/Models/ProformaInvoiceItem.php`
- Controller: `app/Http/Controllers/ProformaInvoiceController.php`
- Views: `resources/views/transactions/proforma-invoices/` (index, create, edit, show)
- Route: `Route::resource('proforma-invoices', ProformaInvoiceController::class);`
- Additional Routes: `proforma-invoices.send`, `proforma-invoices.print`

**Dependencies**: Customers, Billing Addresses, Products, Quotations (optional), Organizations, Branches, Users tables

---

## FORM 14: Tenders

**Purpose**: Manage tender documents and submissions

**Table**: `tenders`
- Fields: id, tender_number (unique), title, description, tender_date, submission_deadline, opening_date, tender_value, status (enum: draft, published, submitted, opened, awarded, rejected, cancelled), tender_document, terms_conditions, notes, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_tenders_table.php`
- Model: `app/Models/Tender.php`
- Controller: `app/Http/Controllers/TenderController.php`
- Views: `resources/views/transactions/tenders/` (index, create, edit, show)
- Route: `Route::resource('tenders', TenderController::class);`
- Additional Route: `tenders.submit`

**Dependencies**: Organizations, Branches, Users tables

---

## FORM 15: Customer Complaints

**Purpose**: Track and manage customer complaints

**Table**: `customer_complaints`
- Fields: id, complaint_number (unique), complaint_date, customer_id (FK), product_id (FK, nullable), quotation_id (FK, nullable), proforma_invoice_id (FK, nullable), priority (enum: low, medium, high, critical), status (enum: open, in_progress, resolved, closed, cancelled), complaint_description, resolution, resolved_date, assigned_to (FK, nullable), notes, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_customer_complaints_table.php`
- Model: `app/Models/CustomerComplaint.php`
- Controller: `app/Http/Controllers/CustomerComplaintController.php`
- Views: `resources/views/transactions/customer-complaints/` (index, create, edit, show)
- Route: `Route::resource('customer-complaints', CustomerComplaintController::class);`
- Additional Route: `customer-complaints.resolve`

**Dependencies**: Customers, Products (optional), Quotations (optional), Proforma Invoices (optional), Users (for assignment), Organizations, Branches tables

---

## FORM 16: Departments

**Purpose**: Manage organizational departments

**Table**: `departments`
- Fields: id, name, code (unique), description, head_of_department (FK, nullable), is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_departments_table.php`
- Model: `app/Models/Department.php`
- Controller: `app/Http/Controllers/DepartmentController.php`
- Views: `resources/views/masters/departments/` (index, create, edit, show)
- Route: `Route::resource('departments', DepartmentController::class);`

**Dependencies**: Users, Organizations, Branches tables

**Used By**: Designations, Employees

---

## FORM 17: Designations

**Purpose**: Manage job designations/titles

**Table**: `designations`
- Fields: id, name, code (unique), department_id (FK, nullable), description, min_salary, max_salary, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_designations_table.php`
- Model: `app/Models/Designation.php`
- Controller: `app/Http/Controllers/DesignationController.php`
- Views: `resources/views/masters/designations/` (index, create, edit, show)
- Route: `Route::resource('designations', DesignationController::class);`

**Dependencies**: Departments, Organizations, Branches, Users tables

**Used By**: Employees

---

## FORM 18: Production Departments

**Purpose**: Manage production-specific departments

**Table**: `production_departments`
- Fields: id, name, code (unique), description, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_production_departments_table.php`
- Model: `app/Models/ProductionDepartment.php`
- Controller: `app/Http/Controllers/ProductionDepartmentController.php`
- Views: `resources/views/masters/production-departments/` (index, create, edit, show)
- Route: `Route::resource('production-departments', ProductionDepartmentController::class);`

**Dependencies**: Organizations, Branches, Users tables

**Used By**: Processes

---

## FORM 19: Employees

**Purpose**: Manage employee master data

**Table**: `employees`
- Fields: id, employee_code (unique), first_name, last_name, email (unique, nullable), phone, mobile, date_of_birth, gender (enum), address, city, state, country, pincode, department_id (FK, nullable), designation_id (FK, nullable), joining_date, salary, pan, aadhar, bank_account_number, bank_ifsc, bank_name, employment_type (enum: permanent, contract, temporary, intern), status (enum: active, inactive, terminated, resigned), photo, notes, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_employees_table.php`
- Model: `app/Models/Employee.php`
- Controller: `app/Http/Controllers/EmployeeController.php`
- Views: `resources/views/masters/employees/` (index, create, edit, show)
- Route: `Route::resource('employees', EmployeeController::class);`

**Dependencies**: Departments, Designations, Organizations, Branches, Users tables

---

## FORM 20: Processes

**Purpose**: Manage production processes

**Table**: `processes`
- Fields: id, name, code (unique), production_department_id (FK, nullable), description, standard_time, cost_per_unit, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_processes_table.php`
- Model: `app/Models/Process.php`
- Controller: `app/Http/Controllers/ProcessController.php`
- Views: `resources/views/masters/processes/` (index, create, edit, show)
- Route: `Route::resource('processes', ProcessController::class);`

**Dependencies**: Production Departments, Organizations, Branches, Users tables

**Used By**: BOM Processes

---

## FORM 21: BOM Processes

**Purpose**: Manage Bill of Materials (BOM) with processes for products

**Table**: `bom_processes`
- Fields: id, product_id (FK), process_id (FK), raw_material_id (FK, nullable), quantity, wastage_percentage, process_time, cost, sequence_order, notes, is_active, organization_id, branch_id, created_by, timestamps, soft_deletes
- Unique Constraint: (product_id, process_id, raw_material_id)

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_bom_processes_table.php`
- Model: `app/Models/BomProcess.php`
- Controller: `app/Http/Controllers/BomProcessController.php`
- Views: `resources/views/masters/bom-processes/` (index, create, edit, show)
- Route: `Route::resource('bom-processes', BomProcessController::class);`
- Additional Route: `products.bom` (to view BOM for a product)

**Dependencies**: Products, Processes, Raw Materials, Organizations, Branches, Users tables

---

## FORM 22: Purchase Indents

**Purpose**: Manage purchase indent/requisition documents

**Tables**: 
- `purchase_indents`: id, indent_number (unique), indent_date, required_date, supplier_id (FK, nullable), status (enum: draft, pending_approval, approved, rejected, ordered, received, cancelled), purpose, notes, total_amount, organization_id, branch_id, created_by, approved_by (FK, nullable), approved_at, timestamps, soft_deletes
- `purchase_indent_items`: id, purchase_indent_id (FK), raw_material_id (FK), description, quantity, unit_price, total_amount, specification, sort_order, timestamps

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_purchase_indents_table.php`
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_purchase_indent_items_table.php`
- Model: `app/Models/PurchaseIndent.php` (uses HasApprovalStatus trait)
- Model: `app/Models/PurchaseIndentItem.php`
- Controller: `app/Http/Controllers/PurchaseIndentController.php`
- Views: `resources/views/transactions/purchase-indents/` (index, create, edit, show)
- Route: `Route::resource('purchase-indents', PurchaseIndentController::class);`
- Additional Routes: `purchase-indents.approve`, `purchase-indents.reject`

**Dependencies**: Suppliers, Raw Materials, Organizations, Branches, Users tables, HasApprovalStatus trait

---

## FORM 23: Subcontractor Evaluations

**Purpose**: Evaluate and manage subcontractor performance

**Table**: `subcontractor_evaluations`
- Fields: id, evaluation_number (unique), evaluation_date, supplier_id (FK), evaluation_type (enum: quality, delivery, price, overall), rating (1-10), strengths, weaknesses, recommendations, status (enum: draft, submitted, approved, rejected), notes, organization_id, branch_id, created_by, evaluated_by (FK, nullable), timestamps, soft_deletes

**Files to Create**:
- Migration: `database/migrations/YYYY_MM_DD_HHMMSS_create_subcontractor_evaluations_table.php`
- Model: `app/Models/SubcontractorEvaluation.php`
- Controller: `app/Http/Controllers/SubcontractorEvaluationController.php`
- Views: `resources/views/transactions/subcontractor-evaluations/` (index, create, edit, show)
- Route: `Route::resource('subcontractor-evaluations', SubcontractorEvaluationController::class);`

**Dependencies**: Suppliers, Organizations, Branches, Users tables

---

## FORM 24: Reports

**Purpose**: Generate various reports (module, not traditional CRUD)

**Implementation**: 
- Controller: `app/Http/Controllers/ReportController.php`
- Views: `resources/views/reports/` (index, sales, purchase, inventory, etc.)
- Routes: `Route::prefix('reports')->group(...)`

**Common Reports**:
1. Sales Reports (Quotations, Proforma Invoices)
2. Purchase Reports (Purchase Indents)
3. Inventory Reports (Products, Raw Materials)
4. Customer Reports
5. Supplier Reports
6. Production Reports
7. Financial Reports

**Dependencies**: All transaction and master data tables, PDF/Excel export packages

---

## Implementation Order Recommendation

**Phase 1 - Master Data (No Dependencies)**:
1. Units
2. Tax
3. Discounts

**Phase 2 - Product Master Data**:
4. Raw Material Categories
5. Raw Material Sub Categories
6. Raw Materials
7. Product Categories
8. Products

**Phase 3 - Customer & Supplier Master Data**:
9. Customers
10. Suppliers
11. Billing Addresses

**Phase 4 - Transaction Forms**:
12. Quotations
13. Proforma Invoices
14. Tenders
15. Customer Complaints

**Phase 5 - HR Master Data**:
16. Departments
17. Designations
18. Production Departments
19. Employees

**Phase 6 - Production Management**:
20. Processes
21. BOM Processes

**Phase 7 - Purchase Management**:
22. Purchase Indents
23. Subcontractor Evaluations

**Phase 8 - Reports**:
24. Reports Module

---

## Standard CRUD Operations for Each Form

1. **index()** - List all records with pagination, search, sorting
2. **create()** - Show create form
3. **store()** - Save new record with validation
4. **show()** - Display single record details
5. **edit()** - Show edit form
6. **update()** - Update existing record with validation
7. **destroy()** - Soft delete record

**Additional Operations** (as needed):
- Approve/Reject (for approval workflows)
- Print/Export (for documents)
- Convert (e.g., Quotation to Proforma Invoice)
- Status changes

---

## Standard Folder Structure Per Form

```
app/
├── Models/
│   └── [ModelName].php
├── Http/
│   ├── Controllers/
│   │   └── [ModelName]Controller.php
│   └── Requests/ (Optional)
│       ├── Store[ModelName]Request.php
│       └── Update[ModelName]Request.php

database/
└── migrations/
    └── YYYY_MM_DD_HHMMSS_create_[table_name]_table.php

resources/
└── views/
    └── [category]/ (masters or transactions)
        └── [kebab-case-name]/
            ├── index.blade.php
            ├── create.blade.php
            ├── edit.blade.php
            └── show.blade.php
```

---

## Notes

- All forms should follow the same pattern as existing forms (Users, Roles, Branches, etc.)
- Use soft deletes for data integrity
- Include organization_id and branch_id for multi-tenancy
- Add created_by for audit trail
- Implement proper validation
- Add authorization checks based on permissions
- Use resource routes for standard CRUD
- Update MenuFormSeeder after creating each form
- Permissions are already seeded in ModuleActionPermissionSeeder

