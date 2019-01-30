<?php

use DaveJamesMiller\Breadcrumbs\Generator;

Breadcrumbs::register('admin.users', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push(__('views.admin.users.index.title'));
});

Breadcrumbs::register('admin.users.show', function (Generator $breadcrumbs, \App\Models\Auth\User\User $user) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push(__('views.admin.users.index.title'), route('admin.users'));
    $breadcrumbs->push(__('views.admin.users.show.title', ['name' => $user->name]));
});

Breadcrumbs::register('admin.users.edit', function (Generator $breadcrumbs, \App\Models\Auth\User\User $user) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push(__('views.admin.users.index.title'), route('admin.users'));
    $breadcrumbs->push(__('views.admin.users.edit.title', ['name' => $user->name]));
});

// Sites
Breadcrumbs::register('admin.sites', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Site');
});

Breadcrumbs::register('admin.sites.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Site', route('admin.sites'));
    $breadcrumbs->push('Tambah Site');
});

Breadcrumbs::register('admin.sites.edit', function (Generator $breadcrumbs, \App\Models\Site $site) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Site', route('admin.sites'));
    $breadcrumbs->push('Ubah Site', route('admin.sites.edit', ['site' => $site]));
});

// Suppliers
Breadcrumbs::register('admin.suppliers', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Vendor');
});

Breadcrumbs::register('admin.suppliers.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Vendor', route('admin.suppliers'));
    $breadcrumbs->push('Tambah Vendor');
});

Breadcrumbs::register('admin.suppliers.edit', function (Generator $breadcrumbs, \App\Models\Supplier $supplier) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Vendor', route('admin.suppliers'));
    $breadcrumbs->push('Ubah Vendor', route('admin.suppliers.edit', ['supplier' => $supplier]));
});

// Employees
Breadcrumbs::register('admin.employees', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Karyawan');
});

Breadcrumbs::register('admin.employees.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Karyawan', route('admin.employees'));
    $breadcrumbs->push('Tambah Karyawan');
});

Breadcrumbs::register('admin.employees.edit', function (Generator $breadcrumbs, \App\Models\Employee $employee) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Karyawan', route('admin.employees'));
    $breadcrumbs->push('Ubah Karyawan', route('admin.employees.edit', ['employee' => $employee]));
});

// Items
Breadcrumbs::register('admin.items', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Barang');
});

Breadcrumbs::register('admin.items.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Barang', route('admin.items'));
    $breadcrumbs->push('Tambah Barang');
});

Breadcrumbs::register('admin.items.edit', function (Generator $breadcrumbs, \App\Models\Item $item) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Barang', route('admin.items'));
    $breadcrumbs->push('Ubah Barang', route('admin.items.edit', ['item' => $item]));
});

// Interchanges
Breadcrumbs::register('admin.interchanges', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Interchanges');
});

Breadcrumbs::register('admin.interchanges.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Interchanges', route('admin.interchanges'));
    $breadcrumbs->push('Tambah Interchanges');
});

// Statuses
Breadcrumbs::register('admin.statuses', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Status');
});

Breadcrumbs::register('admin.statuses.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Status', route('admin.statuses'));
    $breadcrumbs->push('Tambah Status');
});

Breadcrumbs::register('admin.statuses.edit', function (Generator $breadcrumbs, \App\Models\Status $status) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Status', route('admin.statuses'));
    $breadcrumbs->push('Ubah Status', route('admin.statuses.edit', ['status' => $status]));
});

// Roles
Breadcrumbs::register('admin.roles', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Level Akses');
});

Breadcrumbs::register('admin.roles.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Level Akses', route('admin.roles'));
    $breadcrumbs->push('Tambah Level Akses');
});

Breadcrumbs::register('admin.roles.edit', function (Generator $breadcrumbs, \App\Models\Auth\Role\Role $role) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Level Akses', route('admin.roles'));
    $breadcrumbs->push('Ubah Level Akses', route('admin.roles.edit', ['role' => $role]));
});

// Approval Rule
Breadcrumbs::register('admin.approval_rules', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Pengaturan Approval');
});

Breadcrumbs::register('admin.approval_rules.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Pengaturan Approval', route('admin.approval_rules'));
    $breadcrumbs->push('Tambah Pengaturan Approval');
});

Breadcrumbs::register('admin.approval_rules.edit', function (Generator $breadcrumbs, \App\Models\ApprovalRule $approvalRule) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Pengaturan Approval', route('admin.approval_rules'));
    $breadcrumbs->push('Ubah Pengaturan Approval', route('admin.approval_rules.edit', ['approval_rule' => $approvalRule]));
});

Breadcrumbs::register('admin.approval_rules.pr_approval', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PR', route('admin.purchase_requests'));
    $breadcrumbs->push('Approve Purchase Request');
});

// Permission Menu
Breadcrumbs::register('admin.permission_menus', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Otorisasi Menu');
});

Breadcrumbs::register('admin.permission_menus.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Otorisasi Menu', route('admin.permission_menus'));
    $breadcrumbs->push('Tambah Otorisasi Menu');
});

Breadcrumbs::register('admin.permission_menus.edit', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Otorisasi Menu', route('admin.permission_menus'));
//    $breadcrumbs->push('Ubah Otorisasi Menu', route('admin.permission_menus.edit', ['permission_menu' => $permissionMenu]));
});

// Permission Documents
Breadcrumbs::register('admin.permission_documents', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Otorisasi Dokumen');
});

Breadcrumbs::register('admin.permission_documents.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Otorisasi Dokumen', route('admin.permission_documents'));
    $breadcrumbs->push('Tambah Otorisasi Dokumen');
});

Breadcrumbs::register('admin.permission_documents.edit', function (Generator $breadcrumbs, \App\Models\PermissionDocument $permissionDocument) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Otorisasi Dokumen', route('admin.permission_documents'));
    $breadcrumbs->push('Ubah Otorisasi Dokumen', route('admin.permission_documents.edit', ['permission_document' => $permissionDocument]));
});

// Groups
Breadcrumbs::register('admin.groups', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Inventory');
});

Breadcrumbs::register('admin.groups.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Inventory', route('admin.groups'));
    $breadcrumbs->push('Tambah Kategori Inventory');
});

Breadcrumbs::register('admin.groups.edit', function (Generator $breadcrumbs, \App\Models\Group $group) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Inventory', route('admin.groups'));
    $breadcrumbs->push('Ubah Kategori Inventory', route('admin.groups.edit', ['group' => $group]));
});

// Machineries
Breadcrumbs::register('admin.machineries', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Alat Berat');
});

Breadcrumbs::register('admin.machineries.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat', route('admin.machineries'));
    $breadcrumbs->push('Tambah Alat Berat');
});

Breadcrumbs::register('admin.machineries.edit', function (Generator $breadcrumbs, \App\Models\Machinery $machinery) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat', route('admin.machineries'));
    $breadcrumbs->push('Ubah Alat Berat', route('admin.machineries.edit', ['machinery' => $machinery]));
});

// Machinery Types
Breadcrumbs::register('admin.machinery_types', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat');
});

Breadcrumbs::register('admin.machinery_types.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat', route('admin.machinery_types'));
    $breadcrumbs->push('Tambah Kategori Alat Berat');
});

Breadcrumbs::register('admin.machinery_types.edit', function (Generator $breadcrumbs, \App\Models\MachineryType $machineryType) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat', route('admin.machinery_types'));
    $breadcrumbs->push('Ubah Kategori Alat Berat', route('admin.machinery_types.edit', ['machinery_type' => $machineryType]));
});

// Machinery Brands
Breadcrumbs::register('admin.machinery_brands', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat');
});

Breadcrumbs::register('admin.machinery_brands.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat', route('admin.machinery_brands'));
    $breadcrumbs->push('Tambah Kategori Alat Berat');
});

Breadcrumbs::register('admin.machinery_brands.edit', function (Generator $breadcrumbs, \App\Models\MachineryBrand $machineryBrand) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat', route('admin.machinery_brands'));
    $breadcrumbs->push('Ubah Kategori Alat Berat', route('admin.machinery_brands.edit', ['machinery_brand' => $machineryBrand]));
});

// Machinery Categories
Breadcrumbs::register('admin.machinery_categories', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat');
});

Breadcrumbs::register('admin.machinery_categories.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat', route('admin.machinery_categories'));
    $breadcrumbs->push('Tambah Kategori Alat Berat');
});

Breadcrumbs::register('admin.machinery_categories.edit', function (Generator $breadcrumbs, \App\Models\MachineryCategory $machineryCategory) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Kategori Alat Berat', route('admin.machinery_categories'));
    $breadcrumbs->push('Ubah Kategori Alat Berat', route('admin.machinery_categories.edit', ['machinery_category' => $machineryCategory]));
});

// Departments
Breadcrumbs::register('admin.departments', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Departemen');
});

Breadcrumbs::register('admin.departments.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Departemen', route('admin.departments'));
    $breadcrumbs->push('Tambah Departemen');
});

Breadcrumbs::register('admin.departments.edit', function (Generator $breadcrumbs, \App\Models\Department $department) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Departemen', route('admin.departments'));
    $breadcrumbs->push('Ubah Departemen', route('admin.departments.edit', ['department' => $department]));
});

// Documents
Breadcrumbs::register('admin.documents', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Dokumen');
});

Breadcrumbs::register('admin.documents.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Dokumen', route('admin.documents'));
    $breadcrumbs->push('Tambah Dokumen');
});

Breadcrumbs::register('admin.documents.edit', function (Generator $breadcrumbs, \App\Models\Document $document) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Dokumen', route('admin.documents'));
    $breadcrumbs->push('Ubah Dokumen', route('admin.documents.edit', ['document' => $document]));
});

// Payment Methods
Breadcrumbs::register('admin.payment_methods', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Metode Pembayaran');
});

Breadcrumbs::register('admin.payment_methods.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Metode Pembayaran', route('admin.payment_methods'));
    $breadcrumbs->push('Tambah Metode Pembayaran');
});

Breadcrumbs::register('admin.payment_methods.edit', function (Generator $breadcrumbs, \App\Models\PaymentMethod $paymentMethod) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Metode Pembayaran', route('admin.payment_methods'));
    $breadcrumbs->push('Ubah Metode Pembayaran', route('admin.payment_methods.edit', ['payment_method' => $paymentMethod]));
});

// UOMS
Breadcrumbs::register('admin.uoms', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Satuan Unit');
});

Breadcrumbs::register('admin.uoms.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Satuan Unit', route('admin.uoms'));
    $breadcrumbs->push('Tambah Satuan Unit');
});

Breadcrumbs::register('admin.uoms.edit', function (Generator $breadcrumbs, \App\Models\Uom $uom) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Satuan Unit', route('admin.uoms'));
    $breadcrumbs->push('Ubah Satuan Unit', route('admin.uoms.edit', ['uom' => $uom]));
});

// Warehouses
Breadcrumbs::register('admin.warehouses', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Gudang');
});

Breadcrumbs::register('admin.warehouses.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Gudang', route('admin.warehouses'));
    $breadcrumbs->push('Tambah Gudang');
});

Breadcrumbs::register('admin.warehouses.edit', function (Generator $breadcrumbs, \App\Models\Warehouse $warehouse) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Gudang', route('admin.warehouses'));
    $breadcrumbs->push('Ubah Gudang', route('admin.warehouses.edit', ['warehouse' => $warehouse]));
});

// Menus
Breadcrumbs::register('admin.menus', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Menu');
});

Breadcrumbs::register('admin.menus.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Menu', route('admin.menus'));
    $breadcrumbs->push('Tambah Menu');
});

Breadcrumbs::register('admin.menus.edit', function (Generator $breadcrumbs, \App\Models\Menu $menu) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Menu', route('admin.menus'));
    $breadcrumbs->push('Ubah Menu', route('admin.menus.edit', ['menu' => $menu]));
});

// Stock Adjustments
Breadcrumbs::register('admin.stock_adjustments', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Stock Adjustment');
});

Breadcrumbs::register('admin.stock_adjustments.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Stock Adjustment', route('admin.stock_adjustments'));
    $breadcrumbs->push('Tambah Stock Adjustment');
});

// Stock Ins
Breadcrumbs::register('admin.stock_ins', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Stock In');
});

Breadcrumbs::register('admin.stock_ins.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Stock In', route('admin.stock_ins'));
    $breadcrumbs->push('Tambah Stock In');
});

// Item Mutations
Breadcrumbs::register('admin.item_mutations', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Mutasi');
});

Breadcrumbs::register('admin.item_mutations.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Mutasi', route('admin.item_mutations'));
    $breadcrumbs->push('Tambah Mutasi');
});

// Material Request Part/Non-Part
Breadcrumbs::register('admin.material_requests.other', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Part/Non-Part');
});

Breadcrumbs::register('admin.material_requests.other.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Part/Non-Part', route('admin.material_requests.other'));
    $breadcrumbs->push('Tambah MR Part/Non-Part');
});

Breadcrumbs::register('admin.material_requests.other.show', function (Generator $breadcrumbs, \App\Models\MaterialRequestHeader $material_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Part/Non-Part', route('admin.material_requests.other'));
    $breadcrumbs->push('Data MR Part/Non-Part '. $material_request->code, route('admin.material_requests.other.show', ['material_request' => $material_request]));
});

Breadcrumbs::register('admin.material_requests.other.edit', function (Generator $breadcrumbs, \App\Models\MaterialRequestHeader $material_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Part/Non-Part', route('admin.material_requests.other'));
    $breadcrumbs->push('Ubah MR Part/Non-Part', route('admin.material_requests.other.edit', ['material_request' => $material_request]));
});

// Material Request BBM
Breadcrumbs::register('admin.material_requests.fuel', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR BBM');
});

Breadcrumbs::register('admin.material_requests.fuel.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR BBM', route('admin.material_requests.fuel'));
    $breadcrumbs->push('Tambah MR BBM');
});

Breadcrumbs::register('admin.material_requests.fuel.show', function (Generator $breadcrumbs, \App\Models\MaterialRequestHeader $material_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR BBM', route('admin.material_requests.fuel'));
    $breadcrumbs->push('Data MR BBM '. $material_request->code, route('admin.material_requests.fuel.show', ['material_request' => $material_request]));
});

Breadcrumbs::register('admin.material_requests.fuel.edit', function (Generator $breadcrumbs, \App\Models\MaterialRequestHeader $material_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR BBM', route('admin.material_requests.fuel'));
    $breadcrumbs->push('Ubah MR BBM', route('admin.material_requests.fuel.edit', ['material_request' => $material_request]));
});

// Material Request Oli
Breadcrumbs::register('admin.material_requests.oil', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Oli');
});

Breadcrumbs::register('admin.material_requests.oil.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Oli', route('admin.material_requests.oil'));
    $breadcrumbs->push('Tambah MR Oli');
});

Breadcrumbs::register('admin.material_requests.oil.show', function (Generator $breadcrumbs, \App\Models\MaterialRequestHeader $material_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Oli', route('admin.material_requests.oil'));
    $breadcrumbs->push('Data MR Oli '. $material_request->code, route('admin.material_requests.oil.show', ['material_request' => $material_request]));
});

Breadcrumbs::register('admin.material_requests.oil.edit', function (Generator $breadcrumbs, \App\Models\MaterialRequestHeader $material_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Oli', route('admin.material_requests.oil'));
    $breadcrumbs->push('Ubah MR Oli', route('admin.material_requests.oil.edit', ['material_request' => $material_request]));
});

// Material Request Servis
Breadcrumbs::register('admin.material_requests.service', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Servis');
});

Breadcrumbs::register('admin.material_requests.service.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Servis', route('admin.material_requests.service'));
    $breadcrumbs->push('Tambah MR Servis');
});

Breadcrumbs::register('admin.material_requests.service.show', function (Generator $breadcrumbs, \App\Models\MaterialRequestHeader $material_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Servis', route('admin.material_requests.service'));
    $breadcrumbs->push('Data MR Servis '. $material_request->code, route('admin.material_requests.service.show', ['material_request' => $material_request]));
});

Breadcrumbs::register('admin.material_requests.service.edit', function (Generator $breadcrumbs, \App\Models\MaterialRequestHeader $material_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar MR Servis', route('admin.material_requests.service'));
    $breadcrumbs->push('Ubah MR Servis', route('admin.material_requests.service.edit', ['material_request' => $material_request]));
});

// Purchase Request
Breadcrumbs::register('admin.purchase_requests', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PR');
});

Breadcrumbs::register('admin.purchase_requests.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PR', route('admin.purchase_requests'));
    $breadcrumbs->push('Tambah PR');
});

Breadcrumbs::register('admin.purchase_requests.show', function (Generator $breadcrumbs, \App\Models\PurchaseRequestHeader $purchase_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PR', route('admin.purchase_requests'));
    $breadcrumbs->push('Data PR '. $purchase_request->code, route('admin.purchase_requests.show', ['purchase_request' => $purchase_request]));
});

Breadcrumbs::register('admin.purchase_requests.edit', function (Generator $breadcrumbs, \App\Models\PurchaseRequestHeader $purchase_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PR', route('admin.purchase_requests'));
    $breadcrumbs->push('Ubah PR', route('admin.purchase_requests.edit', ['purchase_request' => $purchase_request]));
});

// RFQ
Breadcrumbs::register('admin.quotations', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFQ');
});

Breadcrumbs::register('admin.quotations.before_create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFQ', route('admin.quotations'));
    $breadcrumbs->push('Pilih PR');
});

Breadcrumbs::register('admin.quotations.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFQ', route('admin.quotations'));
    $breadcrumbs->push('Tambah RFQ');
});

Breadcrumbs::register('admin.quotations.show', function (Generator $breadcrumbs, \App\Models\QuotationHeader $quotation) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFQ', route('admin.quotations'));
    $breadcrumbs->push('Data RFQ '. $quotation->code, route('admin.quotations.show', ['quotation' => $quotation]));
});

Breadcrumbs::register('admin.quotations.edit', function (Generator $breadcrumbs, \App\Models\QuotationHeader $quotation) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFQ', route('admin.quotations'));
    $breadcrumbs->push('Ubah RFQ', route('admin.quotations.edit', ['quotation' => $quotation]));
});

// Purchase Order
Breadcrumbs::register('admin.purchase_orders', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PO');
});

Breadcrumbs::register('admin.purchase_orders.before_create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PO', route('admin.purchase_orders'));
    $breadcrumbs->push('Pilih PR');
});

Breadcrumbs::register('admin.purchase_orders.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PO', route('admin.purchase_orders'));
    $breadcrumbs->push('Pilih PR', route('admin.purchase_orders.before_create'));
    $breadcrumbs->push('Tambah PO');
});

Breadcrumbs::register('admin.purchase_orders.show', function (Generator $breadcrumbs, \App\Models\PurchaseOrderHeader $purchase_order) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PO', route('admin.purchase_orders'));
    $breadcrumbs->push('Data PO '. $purchase_order->code, route('admin.purchase_orders.show', ['purchase_order' => $purchase_order]));
});

Breadcrumbs::register('admin.purchase_orders.edit', function (Generator $breadcrumbs, \App\Models\PurchaseOrderHeader $purchase_order) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PO', route('admin.purchase_orders'));
    $breadcrumbs->push('Ubah PO', route('admin.purchase_orders.edit', ['purchase_order' => $purchase_order]));
});

// Goods Receipt
Breadcrumbs::register('admin.item_receipts', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Good Receipt');
});

Breadcrumbs::register('admin.item_receipts.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Good Receipt', route('admin.item_receipts'));
    $breadcrumbs->push('Tambah Good Receipt');
});

Breadcrumbs::register('admin.item_receipts.show', function (Generator $breadcrumbs, \App\Models\ItemReceiptHeader $item_receipt) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Good Receipt', route('admin.item_receipts'));
    $breadcrumbs->push('Data Good Receipt '. $item_receipt->code, route('admin.item_receipts.show', ['item_receipt' => $item_receipt]));
});

// Purchase Invoice
Breadcrumbs::register('admin.purchase_invoices', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Invoice');
});

Breadcrumbs::register('admin.purchase_invoices.before_create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar PO', route('admin.purchase_orders'));
    $breadcrumbs->push('Pilih PO');
});

Breadcrumbs::register('admin.purchase_invoices.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Invoice', route('admin.purchase_invoices'));
    $breadcrumbs->push('Pilih PO', route('admin.purchase_invoices.before_create'));
    $breadcrumbs->push('Tambah Invoice');
});

Breadcrumbs::register('admin.purchase_invoices.show', function (Generator $breadcrumbs, \App\Models\PurchaseInvoiceHeader $purchase_invoice) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Invoice', route('admin.purchase_orders'));
    $breadcrumbs->push('Data Invoice '. $purchase_invoice->code, route('admin.purchase_invoices.show', ['purchase_invoice' => $purchase_invoice]));
});

Breadcrumbs::register('admin.purchase_invoices.edit', function (Generator $breadcrumbs, \App\Models\PurchaseInvoiceHeader $purchase_invoice) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Invoice', route('admin.purchase_invoices'));
    $breadcrumbs->push('Ubah Invoice', route('admin.purchase_invoices.edit', ['purchase_invoice' => $purchase_invoice]));
});

// Payment Request
Breadcrumbs::register('admin.payment_requests', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFP');
});

Breadcrumbs::register('admin.payment_requests.choose_vendor', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFP', route('admin.payment_requests'));
    $breadcrumbs->push('Pilih Vendor');
});

Breadcrumbs::register('admin.payment_requests.choose_vendor_po', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFP', route('admin.payment_requests'));
    $breadcrumbs->push('Pilih Vendor');
});

Breadcrumbs::register('admin.payment_requests.before_create_pi', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFP', route('admin.payment_requests'));
    $breadcrumbs->push('Pilih Invoice');
});

Breadcrumbs::register('admin.payment_requests.before_create_po', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFP', route('admin.payment_requests'));
    $breadcrumbs->push('Pilih PO');
});

Breadcrumbs::register('admin.payment_requests.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFP', route('admin.payment_requests'));
    $breadcrumbs->push('Pilih PO', route('admin.payment_requests.before_create'));
    $breadcrumbs->push('Tambah RFP');
});

Breadcrumbs::register('admin.payment_requests.show', function (Generator $breadcrumbs, \App\Models\PaymentRequest $payment_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFP', route('admin.payment_requests'));
    $breadcrumbs->push('Data RFP '. $payment_request->code, route('admin.payment_requests.show', ['payment_request' => $payment_request]));
});

Breadcrumbs::register('admin.payment_requests.edit', function (Generator $breadcrumbs, \App\Models\PaymentRequest $payment_request) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar RFP', route('admin.payment_requests'));
    $breadcrumbs->push('Ubah RFP', route('admin.payment_requests.edit', ['payment_request' => $payment_request]));
});

// Retur
Breadcrumbs::register('admin.returs', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Retur');
});

Breadcrumbs::register('admin.returs.before_create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Retur', route('admin.returs'));
    $breadcrumbs->push('Pilih Invoice');
});

Breadcrumbs::register('admin.returs.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Retur', route('admin.returs'));
    $breadcrumbs->push('Pilih Invoice', route('admin.returs.before_create'));
    $breadcrumbs->push('Tambah Retur');
});

Breadcrumbs::register('admin.returs.show', function (Generator $breadcrumbs, \App\Models\ReturHeader $retur) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Retur', route('admin.returs'));
    $breadcrumbs->push('Data Retur '. $retur->code, route('admin.returs.show', ['retur' => $retur]));
});

//Breadcrumbs::register('admin.returs.edit', function (Generator $breadcrumbs, \App\Models\ReturHeader $retur) {
//    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
//    $breadcrumbs->push('Daftar Retur', route('admin.returs'));
//    $breadcrumbs->push('Ubah Retur', route('admin.returs.edit', ['retur' => $retur]));
//});

// Issued Docket
Breadcrumbs::register('admin.issued_dockets', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Issued Docket');
});

Breadcrumbs::register('admin.issued_dockets.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Issued Docket', route('admin.issued_dockets'));
    $breadcrumbs->push('Tambah Issued Docket');
});

Breadcrumbs::register('admin.issued_dockets.show', function (Generator $breadcrumbs, \App\Models\IssuedDocketHeader $issued_docket) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Issued Docket', route('admin.issued_dockets'));
    $breadcrumbs->push('Data Issued Docket '. $issued_docket->code, route('admin.issued_dockets.show', ['issued_docket' => $issued_docket]));
});

// Delivery Order
Breadcrumbs::register('admin.delivery_orders', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Surat Jalan');
});

Breadcrumbs::register('admin.delivery_orders.create', function (Generator $breadcrumbs) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Surat Jalan', route('admin.delivery_orders'));
    $breadcrumbs->push('Tambah Invoice');
});

Breadcrumbs::register('admin.delivery_orders.show', function (Generator $breadcrumbs, \App\Models\DeliveryOrderHeader $delivery_order) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Surat Jalan', route('admin.delivery_orders'));
    $breadcrumbs->push('Data Invoice '. $delivery_order->code, route('admin.delivery_orders.show', ['delivery_order' => $delivery_order]));
});

Breadcrumbs::register('admin.delivery_orders.edit', function (Generator $breadcrumbs, \App\Models\DeliveryOrderHeader $delivery_order) {
    $breadcrumbs->push(__('views.admin.dashboard.title'), route('admin.dashboard'));
    $breadcrumbs->push('Daftar Surat Jalan', route('admin.delivery_orders'));
    $breadcrumbs->push('Ubah Invoice', route('admin.delivery_orders.edit', ['delivery_order' => $delivery_order]));
});


