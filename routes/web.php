<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('redirect','RedirectController@redirect')->name('redirect');

Route::get('/flush', function () {
    Session::flush();
    return redirect('/login');
})->name('flush');

/**
 * Auth routes
 */
Route::group(['namespace' => 'Auth'], function () {

    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')->name('logout');

    // Registration Routes...
    if (config('auth.users.registration')) {
        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'RegisterController@register');
    }

    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');

    // Confirmation Routes...
    if (config('auth.users.confirm_email')) {
        Route::get('confirm/{user_by_code}', 'ConfirmController@confirm')->name('confirm');
        Route::get('confirm/resend/{user_by_email}', 'ConfirmController@sendEmail')->name('confirm.send');
    }

    // Social Authentication Routes...
    Route::get('social/redirect/{provider}', 'SocialLoginController@redirect')->name('social.redirect');
    Route::get('social/login/{provider}', 'SocialLoginController@login')->name('social.login');
});

/**
 * Backend routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'auth'], function () {

    // Notification
    Route::get('/notifications', 'NotificationController@notifications');
    Route::get('/test_notify', 'NotificationController@testNotify')->name('notify');

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/index_mr', 'DashboardController@indexMr')->name('dashboard_mr_chart');
    Route::get('/index_pr', 'DashboardController@indexPr')->name('dashboard_pr_chart');
    Route::get('/index_po', 'DashboardController@indexPo')->name('dashboard_po_chart');

    //Users
    Route::get('users', 'UserController@index')->name('users');
    //Route::get('users/{user}', 'UserController@show')->name('users.show');
    Route::get('users/tambah', 'UserController@create')->name('users.create');
    Route::post('users/simpan', 'UserController@store')->name('users.store');
    Route::get('users/{user}/ubah', 'UserController@edit')->name('users.edit');
    Route::put('users/ubah/{user}', 'UserController@update')->name('users.update');
    Route::post('users/hapus', 'UserController@destroy')->name('users.destroy');
    Route::get('permissions', 'PermissionController@index')->name('permissions');
    Route::get('permissions/{user}/repeat', 'PermissionController@repeat')->name('permissions.repeat');
    Route::get('dashboard/log-chart', 'DashboardController@getLogChartData')->name('dashboard.log.chart');
    Route::get('dashboard/registration-chart', 'DashboardController@getRegistrationChartData')->name('dashboard.registration.chart');

    //Sites
    Route::get('sites', 'SiteController@index')->name('sites');
    Route::get('sites/tambah', 'SiteController@create')->name('sites.create');
    Route::post('sites/simpan', 'SiteController@store')->name('sites.store');
    Route::get('sites/{site}/ubah/', 'SiteController@edit')->name('sites.edit');
    Route::put('sites/ubah/{site}', 'SiteController@update')->name('sites.update');
    Route::post('sites/hapus', 'SiteController@destroy')->name('sites.destroy');

    //Suppliers
    Route::get('suppliers', 'SupplierController@index')->name('suppliers');
    Route::get('suppliers/tambah', 'SupplierController@create')->name('suppliers.create');
    Route::post('suppliers/simpan', 'SupplierController@store')->name('suppliers.store');
    Route::get('suppliers/{supplier}/ubah/', 'SupplierController@edit')->name('suppliers.edit');
    Route::put('suppliers/ubah/{supplier}', 'SupplierController@update')->name('suppliers.update');
    Route::post('suppliers/hapus', 'SupplierController@destroy')->name('suppliers.destroy');
    Route::get('suppliers/download_excel', 'SupplierController@downloadExcel')->name('suppliers.download_excel');

    //Employees
    Route::get('employees', 'EmployeeController@index')->name('employees');
    Route::get('employees/tambah', 'EmployeeController@create')->name('employees.create');
    Route::post('employees/simpan', 'EmployeeController@store')->name('employees.store');
    Route::get('employees/{employee}/ubah/', 'EmployeeController@edit')->name('employees.edit');
    Route::put('employees/ubah/{employee}', 'EmployeeController@update')->name('employees.update');

    //Items
    Route::get('items', 'ItemController@index')->name('items');
    Route::get('items/detil/{item}', 'ItemController@show')->name('items.show');
    Route::get('items/tambah', 'ItemController@create')->name('items.create');
    Route::post('items/simpan', 'ItemController@store')->name('items.store');
    Route::get('items/{item}/ubah', 'ItemController@edit')->name('items.edit');
    Route::put('items/ubah/{item}', 'ItemController@update')->name('items.update');
    Route::post('items/hapus', 'ItemController@destroy')->name('items.destroy');
    Route::get('items/peringatan_stok', 'ItemController@indexStockNotification')->name('items.stock_notifications');
    Route::get('items/{item}/invoice_report', 'ItemController@reportInvoice')->name('items.invoice_report');
    Route::post('items/download_invoice_report', 'ItemController@downloadReportInvoice')->name('items.download_invoice_report');
    Route::get('items/excel', 'ItemController@excel')->name('items.excel');
    Route::post('items/download_excel', 'ItemController@downloadExcel')->name('items.download_excel');

    //Item Stocks
    Route::get('item_stocks', 'ItemStockController@index')->name('item_stocks');
    Route::get('item_stocks/{item}/tambah', 'ItemStockController@create')->name('item_stocks.option.create');
    Route::post('item_stocks/tambah/simpan', 'ItemStockController@store')->name('item_stocks.option.store');
    Route::post('item_stocks/tambah', 'ItemStockController@store')->name('item_stocks.store');
    Route::post('item_stocks/ubah', 'ItemStockController@update')->name('item_stocks.update');
    Route::get('item_stocks/{item_stock}/ubah', 'ItemStockController@edit')->name('item_stocks.option.edit');
    Route::put('item_stocks/{item_stock}/ubah/simpan', 'ItemStockController@update')->name('item_stocks.option.update');
    Route::post('item_stocks/lokasi/ubah', 'ItemStockController@updateLocation')->name('item_stocks.location.update');
    Route::post('item_stocks/hapus', 'ItemStockController@destroy')->name('item_stocks.destroy');
    Route::get('item_stocks/excel', 'ItemStockController@excel')->name('item_stocks.excel');
    Route::post('item_stocks/download_excel', 'ItemStockController@downloadExcel')->name('item_stocks.download_excel');
    Route::get('item_stocks/report', 'ItemStockController@report')->name('item_stocks.report');
    Route::post('item_stocks/download_report', 'ItemStockController@downloadReport')->name('item_stocks.download_report');

    //Statuses
    Route::get('statuses', 'StatusController@index')->name('statuses');
    Route::get('statuses/tambah', 'StatusController@create')->name('statuses.create');
    Route::post('statuses/simpan', 'StatusController@store')->name('statuses.store');
    Route::get('statuses/{status}/ubah', 'StatusController@edit')->name('statuses.edit');
    Route::put('statuses/ubah/{status}', 'StatusController@update')->name('statuses.update');
    Route::post('statuses/hapus', 'StatusController@destroy')->name('statuses.destroy');

    //Roles
    Route::get('roles', 'RoleController@index')->name('roles');
    Route::get('roles/{role}/ubah', 'RoleController@edit')->name('roles.edit');
    Route::put('roles/ubah/{role}', 'RoleController@update')->name('roles.update');
    Route::get('roles/tambah', 'RoleController@create')->name('roles.create');
    Route::post('roles/simpan', 'RoleController@store')->name('roles.store');

    //Approval Rule
    Route::get('approval_rules', 'ApprovalRuleController@index')->name('approval_rules');
    Route::get('approval_rules/{approval_rule}/ubah', 'ApprovalRuleController@edit')->name('approval_rules.edit');
    Route::put('approval_rules/ubah/{approval_rule}', 'ApprovalRuleController@update')->name('approval_rules.update');
    Route::get('approval_rules/tambah', 'ApprovalRuleController@create')->name('approval_rules.create');
    Route::post('approval_rules/simpan', 'ApprovalRuleController@store')->name('approval_rules.store');
    Route::post('approval_rules/hapus', 'ApprovalRuleController@destroy')->name('approval_rules.destroy');
    Route::get('approval_rules/mr_approval/{material_request}', 'ApprovalRuleController@mrApproval')->name('approval_rules.mr_approval');
    Route::get('approval_rules/mr_approve/{material_request}', 'ApprovalRuleController@approveMr')->name('approval_rules.approve_mr');
    Route::post('approval_rules/mr_reject/{material_request}', 'ApprovalRuleController@rejectMr')->name('approval_rules.reject_mr');
    Route::get('approval_rules/pr_approval/{approval_rule}', 'ApprovalRuleController@prApproval')->name('approval_rules.pr_approval');
    Route::get('approval_rules/pr_approve/{approval_rule}', 'ApprovalRuleController@approvePr')->name('approval_rules.approve_pr');
    Route::get('approval_rules/po_approval/{approval_rule}', 'ApprovalRuleController@poApproval')->name('approval_rules.po_approval');
    Route::get('approval_rules/po_approve/{approval_rule}', 'ApprovalRuleController@approvePo')->name('approval_rules.approve_po');
    Route::get('approval_rules/po_reject/{approval_rule}', 'ApprovalRuleController@rejectPo')->name('approval_rules.reject_po');

    //Permission Menu
    Route::get('permission_menus', 'PermissionMenuController@index')->name('permission_menus');
    Route::get('permission_menus/detil/{permission_menu}', 'PermissionMenuController@show')->name('permission_menus.show');
    Route::get('permission_menus/{permission_menu}/ubah', 'PermissionMenuController@edit')->name('permission_menus.edit');
    Route::post('permission_menus/ubah', 'PermissionMenuController@update')->name('permission_menus.update');
    Route::get('permission_menus/tambah', 'PermissionMenuController@create')->name('permission_menus.create');
    Route::post('permission_menus/simpan', 'PermissionMenuController@store')->name('permission_menus.store');
    Route::get('permission_menus/hapus/[permission_menu]', 'PermissionMenuController@destroy')->name('permission_menus.destroy');

    //Permission Document
    Route::get('permission_documents', 'PermissionDocumentController@index')->name('permission_documents');
    Route::get('permission_documents/{permission_document}/ubah', 'PermissionDocumentController@edit')->name('permission_documents.edit');
    Route::put('permission_documents/ubah/{permission_document}', 'PermissionDocumentController@update')->name('permission_documents.update');
    Route::get('permission_documents/tambah', 'PermissionDocumentController@create')->name('permission_documents.create');
    Route::post('permission_documents/simpan', 'PermissionDocumentController@store')->name('permission_documents.store');

    //Groups
    Route::get('groups', 'GroupController@index')->name('groups');
    Route::get('groups/{group}/ubah', 'GroupController@edit')->name('groups.edit');
    Route::put('groups/ubah/{group}', 'GroupController@update')->name('groups.update');
    Route::get('groups/tambah', 'GroupController@create')->name('groups.create');
    Route::post('groups/simpan', 'GroupController@store')->name('groups.store');
    Route::post('groups/hapus', 'GroupController@destroy')->name('groups.destroy');

    //Machinery Types
    Route::get('machinery_types', 'MachineryTypeController@index')->name('machinery_types');
    Route::get('machinery_types/{machinery_type}/ubah', 'MachineryTypeController@edit')->name('machinery_types.edit');
    Route::put('machinery_types/ubah/{machinery_type}', 'MachineryTypeController@update')->name('machinery_types.update');
    Route::get('machinery_types/tambah', 'MachineryTypeController@create')->name('machinery_types.create');
    Route::post('machinery_types/simpan', 'MachineryTypeController@store')->name('machinery_types.store');
    Route::post('machinery_types/hapus', 'MachineryTypeController@destroy')->name('machinery_types.destroy');

    //Machinery Categories
    Route::get('machinery_categories', 'MachineryCategoryController@index')->name('machinery_categories');
    Route::get('machinery_categories/{machinery_category}/ubah', 'MachineryCategoryController@edit')->name('machinery_categories.edit');
    Route::put('machinery_categories/ubah/{machinery_category}', 'MachineryCategoryController@update')->name('machinery_categories.update');
    Route::get('machinery_categories/tambah', 'MachineryCategoryController@create')->name('machinery_categories.create');
    Route::post('machinery_categories/simpan', 'MachineryCategoryController@store')->name('machinery_categories.store');
    Route::post('machinery_categories/hapus', 'MachineryCategoryController@destroy')->name('machinery_categories.destroy');

    //Machinery Brands
    Route::get('machinery_brands', 'MachineryBrandController@index')->name('machinery_brands');
    Route::get('machinery_brands/{machinery_brand}/ubah', 'MachineryBrandController@edit')->name('machinery_brands.edit');
    Route::put('machinery_brands/ubah/{machinery_brand}', 'MachineryBrandController@update')->name('machinery_brands.update');
    Route::get('machinery_brands/tambah', 'MachineryBrandController@create')->name('machinery_brands.create');
    Route::post('machinery_brands/simpan', 'MachineryBrandController@store')->name('machinery_brands.store');
    Route::post('machinery_brands/hapus', 'MachineryBrandController@destroy')->name('machinery_brands.destroy');

    //Machineries
    Route::get('machineries', 'MachineryController@index')->name('machineries');
    Route::get('machineries/detil/{machinery}', 'MachineryController@show')->name('machineries.show');
    Route::get('machineries/{machinery}/ubah', 'MachineryController@edit')->name('machineries.edit');
    Route::put('machineries/ubah/{machinery}', 'MachineryController@update')->name('machineries.update');
    Route::get('machineries/tambah', 'MachineryController@create')->name('machineries.create');
    Route::post('machineries/simpan', 'MachineryController@store')->name('machineries.store');
    Route::post('machineries/hapus', 'MachineryController@destroy')->name('machineries.destroy');
    Route::get('machineries/report', 'MachineryController@report')->name('machineries.report');
    Route::post('machineries/report/download', 'MachineryController@downloadReport')->name('machineries.download_report');

    //Departments
    Route::get('departments', 'DepartmentController@index')->name('departments');
    Route::get('departments/{department}/ubah', 'DepartmentController@edit')->name('departments.edit');
    Route::put('departments/ubah/{department}', 'DepartmentController@update')->name('departments.update');
    Route::get('departments/tambah', 'DepartmentController@create')->name('departments.create');
    Route::post('departments/simpan', 'DepartmentController@store')->name('departments.store');
    Route::post('departments/hapus', 'DepartmentController@destroy')->name('departments.destroy');

    //Documents
    Route::get('documents', 'DocumentController@index')->name('documents');
    Route::get('documents/{document}/ubah', 'DocumentController@edit')->name('documents.edit');
    Route::put('documents/ubah/{document}', 'DocumentController@update')->name('documents.update');
    Route::get('documents/tambah', 'DocumentController@create')->name('documents.create');
    Route::post('documents/simpan', 'DocumentController@store')->name('documents.store');
    Route::post('documents/hapus', 'DocumentController@destroy')->name('documents.destroy');

    //Payment Methods
    Route::get('payment_methods', 'PaymentMethodController@index')->name('payment_methods');
    Route::get('payment_methods/{payment_method}/ubah', 'PaymentMethodController@edit')->name('payment_methods.edit');
    Route::put('payment_methods/ubah/{payment_method}', 'PaymentMethodController@update')->name('payment_methods.update');
    Route::get('payment_methods/tambah', 'PaymentMethodController@create')->name('payment_methods.create');
    Route::post('payment_methods/simpan', 'PaymentMethodController@store')->name('payment_methods.store');

    //UOMs
    Route::get('uoms', 'UOMController@index')->name('uoms');
    Route::get('uoms/{uom}/ubah', 'UOMController@edit')->name('uoms.edit');
    Route::put('uoms/ubah/{uom}', 'UOMController@update')->name('uoms.update');
    Route::get('uoms/tambah', 'UOMController@create')->name('uoms.create');
    Route::post('uoms/simpan', 'UOMController@store')->name('uoms.store');
    Route::post('uoms/hapus', 'UOMController@destroy')->name('uoms.destroy');

    //Warehouses
    Route::get('warehouses', 'WarehouseController@index')->name('warehouses');
    Route::get('warehouses/{warehouse}/ubah', 'WarehouseController@edit')->name('warehouses.edit');
    Route::put('warehouses/ubah/{warehouse}', 'WarehouseController@update')->name('warehouses.update');
    Route::get('warehouses/tambah', 'WarehouseController@create')->name('warehouses.create');
    Route::post('warehouses/simpan', 'WarehouseController@store')->name('warehouses.store');
    Route::post('warehouses/hapus', 'WarehouseController@destroy')->name('warehouses.destroy');

    //Menus
    Route::get('menus', 'MenuController@index')->name('menus');
    Route::get('menus/{menu}/ubah', 'MenuController@edit')->name('menus.edit');
    Route::put('menus/ubah/{menu}', 'MenuController@update')->name('menus.update');
    Route::get('menus/tambah', 'MenuController@create')->name('menus.create');
    Route::post('menus/simpan', 'MenuController@store')->name('menus.store');
    Route::post('menus/hapus', 'MenuController@destroy')->name('menus.destroy');

    //Menu Headers
    Route::get('menu_headers', 'MenuHeaderController@index')->name('menu_headers');
    Route::get('menu_headers/{menu_header}/ubah', 'MenuHeaderController@edit')->name('menu_headers.edit');
    Route::put('menu_headers/ubah/{menu_header}', 'MenuHeaderController@update')->name('menu_headers.update');
    Route::get('menu_headers/tambah', 'MenuHeaderController@create')->name('menu_headers.create');
    Route::post('menu_headers/simpan', 'MenuHeaderController@store')->name('menu_headers.store');
    Route::post('menu_headers/hapus', 'MenuHeaderController@destroy')->name('menu_headers.destroy');

    //Menu Subs
    Route::get('menu_subs', 'MenuSubController@index')->name('menu_subs');
    Route::get('menu_subs/{menu_sub}/ubah', 'MenuSubController@edit')->name('menu_subs.edit');
    Route::put('menu_subs/ubah/{menu_sub}', 'MenuSubController@update')->name('menu_subs.update');
    Route::get('menu_subs/tambah', 'MenuSubController@create')->name('menu_subs.create');
    Route::post('menu_subs/simpan', 'MenuSubController@store')->name('menu_subs.store');
    Route::post('menu_subs/hapus', 'MenuSubController@destroy')->name('menu_subs.destroy');

    //Material Request Headers Primary
    Route::post('material_requests/simpan', 'Inventory\MaterialRequestHeaderController@store')->name('material_requests.store');
    Route::put('material_requests/ubah/{material_request}', 'Inventory\MaterialRequestHeaderController@update')->name('material_requests.update');
    Route::post('material_requests/tutup', 'Inventory\MaterialRequestHeaderController@close')->name('material_requests.close');
    Route::get('material_requests/print/{material_request}', 'Inventory\MaterialRequestHeaderController@printDocument')->name('material_requests.print');
    Route::post('material_requests/feedback/{material_request}', 'Inventory\MaterialRequestHeaderController@feedback')->name('material_requests.feedback');

    //Material Request Details Primary
    Route::post('material_request_details/simpan', 'Inventory\MaterialRequestDetailController@store')->name('material_request_details.store');
    Route::put('material_request_details/ubah', 'Inventory\MaterialRequestDetailController@update')->name('material_request_details.update');
    Route::post('material_request_details/hapus', 'Inventory\MaterialRequestDetailController@delete')->name('material_request_details.delete');
    Route::get('material_requests/laporan', 'Inventory\MaterialRequestHeaderController@report')->name('material_requests.report');
    Route::post('material_requests/laporan/unduh', 'Inventory\MaterialRequestHeaderController@downloadReport')->name('material_requests.report.download');
    Route::get('material_requests/download/pdf/{material_request}', 'Inventory\MaterialRequestHeaderController@downloadPdf')->name('material_requests.pdf.download');

    //Material Request Headers for Other
    Route::get('material_requests/inventory', 'Inventory\MaterialRequestHeaderController@indexOther')->name('material_requests.other');
    Route::get('material_requests/inventory/detil/{material_request}', 'Inventory\MaterialRequestHeaderController@showOther')->name('material_requests.other.show');
    Route::get('material_requests/inventory/{material_request}/ubah', 'Inventory\MaterialRequestHeaderController@editOther')->name('material_requests.other.edit');
    Route::get('material_requests/inventory/tambah', 'Inventory\MaterialRequestHeaderController@createOther')->name('material_requests.other.create');
    Route::get('material_requests/inventory/download/{material_request}', 'Inventory\MaterialRequestHeaderController@downloadOther')->name('material_requests.other.download');

    //Material Request Headers for Fuel
    Route::get('material_requests/bbm', 'Inventory\MaterialRequestHeaderController@indexFuel')->name('material_requests.fuel');
    Route::get('material_requests/bbm/detil/{material_request}', 'Inventory\MaterialRequestHeaderController@showFuel')->name('material_requests.fuel.show');
    Route::get('material_requests/bbm/{material_request}/ubah', 'Inventory\MaterialRequestHeaderController@editFuel')->name('material_requests.fuel.edit');
    Route::get('material_requests/bbm/tambah', 'Inventory\MaterialRequestHeaderController@createFuel')->name('material_requests.fuel.create');
    Route::get('material_requests/bbm/download/{material_request}', 'Inventory\MaterialRequestHeaderController@downloadFuel')->name('material_requests.fuel.download');

    //Material Request Headers for Oil
    Route::get('material_requests/oli', 'Inventory\MaterialRequestHeaderController@indexOil')->name('material_requests.oil');
    Route::get('material_requests/oli/detil/{material_request}', 'Inventory\MaterialRequestHeaderController@showOil')->name('material_requests.oil.show');
    Route::get('material_requests/oli/{material_request}/ubah', 'Inventory\MaterialRequestHeaderController@editOil')->name('material_requests.oil.edit');
    Route::get('material_requests/oli/tambah', 'Inventory\MaterialRequestHeaderController@createOil')->name('material_requests.oil.create');
    Route::get('material_requests/oli/download/{material_request}', 'Inventory\MaterialRequestHeaderController@downloadOil')->name('material_requests.oil.download');

    //Material Request Headers for Service
    Route::get('material_requests/servis', 'Inventory\MaterialRequestHeaderController@indexService')->name('material_requests.service');
    Route::get('material_requests/servis/detil/{material_request}', 'Inventory\MaterialRequestHeaderController@showService')->name('material_requests.service.show');
    Route::get('material_requests/servis/{material_request}/ubah', 'Inventory\MaterialRequestHeaderController@editService')->name('material_requests.service.edit');
    Route::get('material_requests/servis/tambah', 'Inventory\MaterialRequestHeaderController@createService')->name('material_requests.service.create');
    Route::post('material_requests/servos/simpan', 'Inventory\MaterialRequestHeaderController@storeService')->name('material_requests.service.store');
    Route::get('material_requests/servis/download/{material_request}', 'Inventory\MaterialRequestHeaderController@downloadService')->name('material_requests.service.download');

    //Purchase Request Headers
    Route::get('purchase_requests', 'Purchasing\PurchaseRequestHeaderController@index')->name('purchase_requests');
    Route::get('purchase_requests/warning', 'Purchasing\PurchaseRequestHeaderController@indexWarning')->name('purchase_requests.warning');
    Route::get('purchase_requests/detil/{purchase_request}', 'Purchasing\PurchaseRequestHeaderController@show')->name('purchase_requests.show');
    Route::get('purchase_requests/{purchase_request}/ubah', 'Purchasing\PurchaseRequestHeaderController@edit')->name('purchase_requests.edit');
    Route::put('purchase_requests/ubah/{purchase_request}', 'Purchasing\PurchaseRequestHeaderController@update')->name('purchase_requests.update');
    Route::get('purchase_requests/pilihmr', 'Purchasing\PurchaseRequestHeaderController@beforeCreate')->name('purchase_requests.before_create');
    Route::get('purchase_requests/tambah', 'Purchasing\PurchaseRequestHeaderController@create')->name('purchase_requests.create');
    Route::post('purchase_requests/simpan', 'Purchasing\PurchaseRequestHeaderController@store')->name('purchase_requests.store');
    Route::post('purchase_requests/servis/simpan', 'Purchasing\PurchaseRequestHeaderController@storeService')->name('purchase_requests.service.store');
    Route::post('purchase_requests/tutup', 'Purchasing\PurchaseRequestHeaderController@close')->name('purchase_requests.close');
    Route::get('purchase_requests/print/{purchase_request}', 'Purchasing\PurchaseRequestHeaderController@printDocument')->name('purchase_requests.print');
    Route::get('purchase_requests/download/{purchase_request}', 'Purchasing\PurchaseRequestHeaderController@download')->name('purchase_requests.download');
    Route::get('purchase_requests/report', 'Purchasing\PurchaseRequestHeaderController@report')->name('purchase_requests.report');
    Route::post('purchase_requests/download_report', 'Purchasing\PurchaseRequestHeaderController@downloadReport')->name('purchase_requests.download-report');

    //Purchase Request Details
    Route::get('purchase_request_details', 'Purchasing\PurchaseRequestDetailController@index')->name('purchase_request_details');
    Route::post('purchase_request_details/simpan', 'Purchasing\PurchaseRequestDetailController@store')->name('purchase_request_details.store');
    Route::put('purchase_request_details/ubah', 'Purchasing\PurchaseRequestDetailController@update')->name('purchase_request_details.update');
    Route::post('purchase_request_details/hapus', 'Purchasing\PurchaseRequestDetailController@delete')->name('purchase_request_details.delete');

    //Purchase Order Headers
    Route::get('purchase_orders', 'Purchasing\PurchaseOrderHeaderController@index')->name('purchase_orders');
    Route::get('purchase_orders/detil/{purchase_order}', 'Purchasing\PurchaseOrderHeaderController@show')->name('purchase_orders.show');
    Route::get('purchase_orders/{purchase_order}/ubah', 'Purchasing\PurchaseOrderHeaderController@edit')->name('purchase_orders.edit');
    Route::put('purchase_orders/ubah/{purchase_order}', 'Purchasing\PurchaseOrderHeaderController@update')->name('purchase_orders.update');
    Route::get('purchase_orders/pilihpr', 'Purchasing\PurchaseOrderHeaderController@beforeCreate')->name('purchase_orders.before_create');
    Route::get('purchase_orders/tambah', 'Purchasing\PurchaseOrderHeaderController@create')->name('purchase_orders.create');
    Route::post('purchase_orders/simpan', 'Purchasing\PurchaseOrderHeaderController@store')->name('purchase_orders.store');
    Route::post('purchase_orders/servis/simpan', 'Purchasing\PurchaseOrderHeaderController@storeService')->name('purchase_orders.service.store');
    Route::post('purchase_orders/tutup', 'Purchasing\PurchaseOrderHeaderController@close')->name('purchase_orders.close');
    Route::get('purchase_orders/print/{purchase_order}', 'Purchasing\PurchaseOrderHeaderController@printDocument')->name('purchase_orders.print');
    Route::get('purchase_orders/download/{purchase_order}', 'Purchasing\PurchaseOrderHeaderController@download')->name('purchase_orders.download');
    Route::get('purchase_orders/report', 'Purchasing\PurchaseOrderHeaderController@report')->name('purchase_orders.report');
    Route::post('purchase_orders/download_report', 'Purchasing\PurchaseOrderHeaderController@downloadReport')->name('purchase_orders.download-report');
    Route::get('purchase_orders/download/quotation1/{purchase_order}', 'Purchasing\PurchaseOrderHeaderController@downloadPdf1')->name('purchase_orders.pdf1.download');
    Route::get('purchase_orders/download/quotation2/{purchase_order}', 'Purchasing\PurchaseOrderHeaderController@downloadPdf2')->name('purchase_orders.pdf2.download');
    Route::get('purchase_orders/download/quotation3/{purchase_order}', 'Purchasing\PurchaseOrderHeaderController@downloadPdf3')->name('purchase_orders.pdf3.download');

    //Purchase Order Details
    Route::post('purchase_order_details/simpan', 'Purchasing\PurchaseOrderDetailController@store')->name('purchase_order_details.store');
    Route::put('purchase_order_details/ubah', 'Purchasing\PurchaseOrderDetailController@update')->name('purchase_order_details.update');
    Route::post('purchase_order_details/hapus', 'Purchasing\PurchaseOrderDetailController@delete')->name('purchase_order_details.delete');

    //Quotation Headers
    Route::get('quotations', 'Purchasing\QuotationHeaderController@index')->name('quotations');
    Route::get('quotations/detil/{quotation}', 'Purchasing\QuotationHeaderController@show')->name('quotations.show');
    Route::get('quotations/{quotation}/ubah', 'Purchasing\QuotationHeaderController@edit')->name('quotations.edit');
    Route::put('quotations/ubah/{quotation}', 'Purchasing\QuotationHeaderController@update')->name('quotations.update');
    Route::get('quotations/pilihpr', 'Purchasing\QuotationHeaderController@beforeCreate')->name('quotations.before_create');
    Route::get('quotations/tambah', 'Purchasing\QuotationHeaderController@create')->name('quotations.create');
    Route::post('quotations/simpan', 'Purchasing\QuotationHeaderController@store')->name('quotations.store');
    Route::get('quotations/print/{quotation}', 'Purchasing\QuotationHeaderController@print')->name('quotations.print');
    Route::get('quotations/empty/pilihpr', 'Purchasing\QuotationHeaderController@beforeCreateEmpty')->name('quotations.before_create_empty');
    Route::get('quotations/empty/tambah', 'Purchasing\QuotationHeaderController@createEmpty')->name('quotations.create_empty');
    Route::post('quotations/empty/print', 'Purchasing\QuotationHeaderController@printEmpty')->name('quotations.print_empty');

    //Quotation Details
    Route::post('quotation_details/simpan', 'Purchasing\QuotationDetailController@store')->name('quotation_details.store');
    Route::put('quotation_details/ubah', 'Purchasing\QuotationDetailController@update')->name('quotation_details.update');
    Route::post('quotation_details/hapus', 'Purchasing\QuotationDetailController@delete')->name('quotation_details.delete');

    //Purchase Invoice Headers
    Route::get('purchase_invoices', 'Purchasing\PurchaseInvoiceHeaderController@index')->name('purchase_invoices');
    Route::get('purchase_invoices/detil/{purchase_invoice}', 'Purchasing\PurchaseInvoiceHeaderController@show')->name('purchase_invoices.show');
    Route::get('purchase_invoices/{purchase_invoice}/ubah', 'Purchasing\PurchaseInvoiceHeaderController@edit')->name('purchase_invoices.edit');
    Route::put('purchase_invoices/ubah/{purchase_invoice}', 'Purchasing\PurchaseInvoiceHeaderController@update')->name('purchase_invoices.update');
    Route::get('purchase_invoices/pilihpo', 'Purchasing\PurchaseInvoiceHeaderController@beforeCreate')->name('purchase_invoices.before_create');
    Route::get('purchase_invoices/tambah', 'Purchasing\PurchaseInvoiceHeaderController@create')->name('purchase_invoices.create');
    Route::post('purchase_invoices/simpan', 'Purchasing\PurchaseInvoiceHeaderController@store')->name('purchase_invoices.store');
    Route::post('purchase_invoices/servis/simpan', 'Purchasing\PurchaseInvoiceHeaderController@storeService')->name('purchase_invoices.service.store');
    Route::post('purchase_invoices/pelunasan', 'Purchasing\PurchaseInvoiceHeaderController@repayment')->name('purchase_invoices.repayment');
    Route::post('purchase_invoices/pelunasan-ubah', 'Purchasing\PurchaseInvoiceHeaderController@repaymentUpdate')->name('purchase_invoices.repayment-update');
    Route::get('purchase_invoices/report', 'Purchasing\PurchaseInvoiceHeaderController@report')->name('purchase_invoices.report');
    Route::get('purchase_invoices/site/report', 'Purchasing\PurchaseInvoiceHeaderController@reportSite')->name('purchase_invoices.site.report');
    Route::post('purchase_invoices/download_report', 'Purchasing\PurchaseInvoiceHeaderController@downloadReport')->name('purchase_invoices.download-report');
    Route::post('purchase_invoices/site/download_report', 'Purchasing\PurchaseInvoiceHeaderController@downloadReportSite')->name('purchase_invoices.site.download_report');

    //Purchase Invoice Details
    Route::post('purchase_invoice_details/simpan', 'Purchasing\PurchaseInvoiceDetailController@store')->name('purchase_invoice_details.store');
    Route::put('purchase_invoice_details/ubah', 'Purchasing\PurchaseInvoiceDetailController@update')->name('purchase_invoice_details.update');
    Route::post('purchase_invoice_details/hapus', 'Purchasing\PurchaseInvoiceDetailController@delete')->name('purchase_invoice_details.delete');

    //Retur Headers
    Route::get('returs', 'Purchasing\ReturHeaderController@index')->name('returs');
    Route::get('returs/detil/{retur}', 'Purchasing\ReturHeaderController@show')->name('returs.show');
    Route::get('returs/{retur}/ubah', 'Purchasing\ReturHeaderController@edit')->name('returs.edit');
    Route::put('returs/ubah/{retur}', 'Purchasing\ReturHeaderController@update')->name('returs.update');
    Route::get('returs/pilih_invoice', 'Purchasing\ReturHeaderController@beforeCreate')->name('returs.before_create');
    Route::get('returs/tambah', 'Purchasing\ReturHeaderController@create')->name('returs.create');
    Route::post('returs/simpan', 'Purchasing\ReturHeaderController@store')->name('returs.store');
    Route::get('returs/print/{retur}', 'Purchasing\ReturHeaderController@printDocument')->name('returs.print');
    Route::get('returs/report', 'Purchasing\ReturHeaderController@report')->name('returs.report');
    Route::post('returs/download_report', 'Purchasing\ReturHeaderController@downloadReport')->name('returs.download-report');

    //Retur Details
    Route::post('retur_details/simpan', 'Purchasing\ReturDetailController@store')->name('retur_details.store');
    Route::put('retur_details/ubah', 'Purchasing\ReturDetailController@update')->name('retur_details.update');
    Route::post('retur_details/hapus', 'Purchasing\ReturDetailController@delete')->name('retur_details.delete');

    //Payment Request
    Route::get('payment_requests', 'Purchasing\PaymentRequestController@index')->name('payment_requests');
    Route::get('payment_requests/detil/{payment_request}', 'Purchasing\PaymentRequestController@show')->name('payment_requests.show');
    Route::get('payment_requests/{payment_request}/ubah', 'Purchasing\PaymentRequestController@edit')->name('payment_requests.edit');
    Route::put('payment_requests/ubah/{payment_request}', 'Purchasing\PaymentRequestController@update')->name('payment_requests.update');
    Route::post('payment_requests/hapus', 'Purchasing\PaymentRequestController@destroy')->name('payment_requests.destroy');
    Route::get('payment_requests/print/{payment_request}', 'Purchasing\PaymentRequestController@printDocument')->name('payment_requests.print');

    Route::get('payment_requests/pilihvendor', 'Purchasing\PaymentRequestController@chooseVendor')->name('payment_requests.choose_vendor');
    Route::get('payment_requests/pilihpi', 'Purchasing\PaymentRequestController@beforeCreateFromPi')->name('payment_requests.before_create_pi');
    Route::post('payment_requests/tambahdaripi', 'Purchasing\PaymentRequestController@createFromPi')->name('payment_requests.create_from_pi');

    Route::get('payment_requests/pilihvendorpo', 'Purchasing\PaymentRequestController@chooseVendorPo')->name('payment_requests.choose_vendor_po');
    Route::get('payment_requests/pilihpo', 'Purchasing\PaymentRequestController@beforeCreateFromPo')->name('payment_requests.before_create_po');
    Route::post('payment_requests/tambahdaripo', 'Purchasing\PaymentRequestController@createFromPo')->name('payment_requests.create_from_po');

    Route::post('payment_requests/simpan', 'Purchasing\PaymentRequestController@store')->name('payment_requests.store');
    Route::get('payment_requests/report', 'Purchasing\PaymentRequestController@report')->name('payment_requests.report');
    Route::post('payment_requests/download_report', 'Purchasing\PaymentRequestController@downloadReport')->name('payment_requests.download-report');

    //Payment Request Details
    Route::post('payment_request_details/simpan', 'Purchasing\PaymentRequestDetailController@store')->name('payment_request_details.store');
    Route::post('payment_request_details/ubah', 'Purchasing\PaymentRequestDetailController@update')->name('payment_request_details.update');
    Route::post('payment_request_details/hapus', 'Purchasing\PaymentRequestDetailController@delete')->name('payment_request_details.delete');

    //Delivery Order Headers
    Route::get('delivery_orders', 'Inventory\DeliveryOrderHeaderController@index')->name('delivery_orders');
    Route::get('delivery_orders/detil/{delivery_order}', 'Inventory\DeliveryOrderHeaderController@show')->name('delivery_orders.show');
    Route::put('delivery_orders/ubah/{delivery_order}', 'Inventory\DeliveryOrderHeaderController@update')->name('delivery_orders.update');
    Route::get('delivery_orders/tambah', 'Inventory\DeliveryOrderHeaderController@create')->name('delivery_orders.create');
    Route::post('delivery_orders/simpan', 'Inventory\DeliveryOrderHeaderController@store')->name('delivery_orders.store');
    Route::post('delivery_orders/konfirmasi', 'Inventory\DeliveryOrderHeaderController@confirm')->name('delivery_orders.confirm');
    Route::post('delivery_orders/batal', 'Inventory\DeliveryOrderHeaderController@cancel')->name('delivery_orders.cancel');
    Route::get('delivery_orders/print/{delivery_order}', 'Inventory\DeliveryOrderHeaderController@print')->name('delivery_orders.print');
    Route::get('delivery_orders/report', 'Inventory\DeliveryOrderHeaderController@report')->name('delivery_orders.report');
    Route::post('delivery_orders/download_report', 'Inventory\DeliveryOrderHeaderController@downloadReport')->name('delivery_orders.download-report');

    //Delivery Order Details
    Route::post('delivery_order_details/simpan', 'Inventory\DeliveryOrderDetailController@store')->name('delivery_order_details.store');
    Route::put('delivery_order_details/ubah', 'Inventory\DeliveryOrderDetailController@update')->name('delivery_order_details.update');
    Route::post('delivery_order_details/hapus', 'Inventory\DeliveryOrderDetailController@delete')->name('delivery_order_details.delete');

    //Issued Docket Headers
    Route::get('issued_dockets', 'Inventory\DocketController@index')->name('issued_dockets');
    Route::get('issued_dockets/detil/{issued_docket}', 'Inventory\DocketController@show')->name('issued_dockets.show');
    Route::get('issued_dockets/{issued_docket}/ubah', 'Inventory\DocketController@edit')->name('issued_dockets.edit');
    Route::put('issued_dockets/ubah/{issued_docket}', 'Inventory\DocketController@update')->name('issued_dockets.update');
    Route::get('issued_dockets/hapus/{id}', 'Inventory\DocketController@destroy')->name('issued_dockets.destroy');
    Route::get('issued_dockets/pilih_mr', 'Inventory\DocketController@beforeCreate')->name('issued_dockets.before_create');
    Route::get('issued_dockets/tambah', 'Inventory\DocketController@create')->name('issued_dockets.create');
    Route::get('issued_dockets/tambah/bbm', 'Inventory\DocketController@createFuel')->name('issued_dockets.fuel.create');
    Route::post('issued_dockets/simpan', 'Inventory\DocketController@store')->name('issued_dockets.store');
    Route::post('issued_dockets/bbm/simpan', 'Inventory\DocketController@storeFuel')->name('issued_dockets.fuel.store');
    Route::get('issued_dockets/print/{issued_docket}', 'Inventory\DocketController@printDocument')->name('issued_dockets.print');
    Route::get('issued_dockets/download/{issued_docket}', 'Inventory\DocketController@download')->name('issued_dockets.download');
    Route::get('issued_dockets/report', 'Inventory\DocketController@report')->name('issued_dockets.report');
    Route::get('issued_dockets/ho/report', 'Inventory\DocketController@reportHo')->name('issued_dockets.ho.report');
    Route::post('issued_dockets/download_report', 'Inventory\DocketController@downloadReport')->name('issued_dockets.download_report');
    Route::post('issued_dockets/download_excel', 'Inventory\DocketController@downloadReportExcel')->name('issued_dockets.download_excel');
    Route::get('issued_dockets/cost_code/report', 'Inventory\DocketController@reportCostCode')->name('issued_dockets.cost_code.report');
    Route::post('issued_dockets/cost_code/download_report', 'Inventory\DocketController@downloadReportCostCode')->name('issued_dockets.cost_code.download_report');

    //Issued Docket Details
    Route::get('issued_docket_details', 'Inventory\DocketDetailController@index')->name('issued_docket_details');
    Route::post('issued_docket_details/simpan', 'Inventory\DocketDetailController@store')->name('issued_docket_details.store');
    Route::put('issued_docket_details/ubah', 'Inventory\DocketDetailController@update')->name('issued_docket_details.update');
    Route::post('issued_docket_details/hapus', 'Inventory\DocketDetailController@delete')->name('issued_docket_details.delete');
    Route::post('issued_docket_details/retur', 'Inventory\DocketDetailController@retur')->name('issued_docket_details.retur');

    //Item Receipts Headers
    Route::get('item_receipts', 'Inventory\ItemReceiptController@index')->name('item_receipts');
    Route::get('item_receipts/detil/{item_receipt}', 'Inventory\ItemReceiptController@show')->name('item_receipts.show');
//    Route::get('item_receipts/{item_receipt}/ubah', 'Inventory\ItemReceiptController@edit')->name('item_receipts.edit');
//    Route::put('item_receipts/ubah/{item_receipt}', 'Inventory\ItemReceiptController@update')->name('item_receipts.update');
    Route::get('item_receipts/tambah', 'Inventory\ItemReceiptController@create')->name('item_receipts.create');
    Route::post('item_receipts/simpan', 'Inventory\ItemReceiptController@store')->name('item_receipts.store');
    Route::get('item_receipts/print/{item_receipt}', 'Inventory\ItemReceiptController@printDocument')->name('item_receipts.print');
    Route::get('item_receipts/download/{item_receipt}', 'Inventory\ItemReceiptController@download')->name('item_receipts.download');
    Route::get('item_receipts/report', 'Inventory\ItemReceiptController@report')->name('item_receipts.report');
    Route::post('item_receipts/download_report', 'Inventory\ItemReceiptController@downloadReport')->name('item_receipts.download-report');
    Route::get('item_receipts/pilih_po', 'Inventory\ItemReceiptController@beforeCreate')->name('item_receipts.before_create');

    //Item Receipts Details
    Route::get('item_receipt_details', 'Inventory\ItemReceiptDetailController@index')->name('item_receipt_details');
    Route::post('item_receipt_details/simpan', 'Inventory\ItemReceiptDetailController@store')->name('item_receipt_details.store');
    Route::put('item_receipt_details/ubah', 'Inventory\ItemReceiptDetailController@update')->name('item_receipt_details.update');
    Route::post('item_receipt_details/hapus', 'Inventory\ItemReceiptDetailController@delete')->name('item_receipt_details.delete');

    //Stock Adjustment
    Route::get('stock_adjustments', 'Inventory\StockAdjustmentController@index')->name('stock_adjustments');
    Route::get('stock_adjustments/tambah', 'Inventory\StockAdjustmentController@create')->name('stock_adjustments.create');
    Route::post('stock_adjustments/simpan', 'Inventory\StockAdjustmentController@store')->name('stock_adjustments.store');

    //Stock In
    Route::get('stock_ins', 'Inventory\StockInController@index')->name('stock_ins');
    Route::get('stock_ins/tambah', 'Inventory\StockInController@create')->name('stock_ins.create');
    Route::post('stock_ins/simpan', 'Inventory\StockInController@store')->name('stock_ins.store');

    //Stock Card
    Route::get('stock_cards', 'Inventory\StockCardController@index')->name('stock_cards');
    Route::get('stock_cards/tambah', 'Inventory\StockCardController@create')->name('stock_cards.create');
    Route::post('stock_cards/simpan', 'Inventory\StockCardController@store')->name('stock_cards.store');
    Route::get('stock_cards/report', 'Inventory\StockCardController@report')->name('stock_cards.report');
    Route::post('stock_cards/report/download', 'Inventory\StockCardController@downloadReport')->name('stock_cards.download_report');

    //Stock Mutation
    Route::get('item_mutations', 'Inventory\ItemMutationController@index')->name('item_mutations');
    Route::get('item_mutations/tambah', 'Inventory\ItemMutationController@create')->name('item_mutations.create');
    Route::post('item_mutations/simpan', 'Inventory\ItemMutationController@store')->name('item_mutations.store');

    //Interchanges
    Route::get('interchanges', 'Inventory\InterchangeController@index')->name('interchanges');
    Route::get('interchanges/tambah', 'Inventory\InterchangeController@create')->name('interchanges.create');
    Route::post('interchanges/simpan', 'Inventory\InterchangeController@store')->name('interchanges.store');

    //Settings
    Route::get('settings/ubah', 'Setting\SettingController@edit')->name('settings.edit');
    Route::put('settings/ubah/{id}', 'Setting\SettingController@update')->name('settings.update');
    Route::get('settings/perusahaan/ubah', 'Setting\SettingController@preference')->name('settings.preference');
    Route::put('settings/perusahaan/ubah/{preference}', 'Setting\SettingController@preferenceUpdate')->name('settings.preference-update');

    //Notifications
    Route::get('notifications', 'NotificationController@index')->name('notifications');
    Route::post('notifications/read', 'NotificationController@read')->name('notifications.read');
    Route::get('notifications/all/read', 'NotificationController@readAll')->name('notifications.read.all');

    //Import
    Route::get('import/supplier/upload', 'ImportController@uploadSupplier')->name('import.suppliers.upload');
    Route::post('import/supplier/submit', 'ImportController@importSupplier')->name('import.suppliers.submit');
    Route::get('import/inventory/upload', 'ImportController@uploadInventory')->name('import.items.upload');
    Route::post('import/inventory/submit', 'ImportController@importInventory')->name('import.items.submit');
    Route::post('import/cost_code/submit', 'ImportController@importCostCode')->name('import.cost_codes.submit');

    //Monitoring MR
    Route::get('controlling/monitor/mr', 'Controlling\MonitoringMRController@index')->name('controlling.monitor.mr.index');
    Route::get('controlling/monitor/mr/report', 'Controlling\MonitoringMRController@report')->name('controlling.monitor.mr.report');
    Route::post('controlling/monitor/mr/report/unduh', 'Controlling\MonitoringMRController@downloadReport')->name('controlling.monitor.mr.report.download');
    Route::post('controlling/monitor/mr/report/excel/unduh', 'Controlling\MonitoringMRController@downloadExcel')->name('controlling.monitor.mr.report.excel.download');

    //Account
    Route::get('cost_codes', 'AccountController@index')->name('accounts');
    Route::get('cost_codes/tambah', 'AccountController@create')->name('accounts.create');
    Route::post('cost_codes/simpan', 'AccountController@store')->name('accounts.store');
    Route::get('cost_codes/{account}/ubah', 'AccountController@edit')->name('accounts.edit');
    Route::put('cost_codes/{account}/ubah/simpan', 'AccountController@update')->name('accounts.update');
    Route::post('cost_codes/hapus', 'AccountController@delete')->name('accounts.delete');
    Route::get('cost_codes/excel', 'AccountController@downloadExcel')->name('accounts.excel');

    //Reorder
    Route::get('reorder/menu', 'ReorderController@menu')->name('reorder.menu');
    Route::get('reorder/index', 'ReorderController@index')->name('reorder.list');
    Route::post('reorder', 'ReorderController@store')->name('reorder.store');
    Route::get('reorder/parts', 'ReorderController@part')->name('reorder.part');
    Route::get('reorder/fuel', 'ReorderController@fuel')->name('reorder.fuel');
    Route::get('reorder/oil', 'ReorderController@oil')->name('reorder.oil');
    Route::get('reorder/calibrate', 'ReorderController@calibrate')->name('reorder.calibrate');

    //Assignment
    Route::get('assignment/mr/assign', 'Controlling\AssignmentController@createAssignmentMr')->name('assignment.mr.assign');
    Route::get('assignment/pr/assign', 'Controlling\AssignmentController@createAssignmentPr')->name('assignment.pr.assign');
    Route::post('assignment/mr/assign/store', 'Controlling\AssignmentController@storeAssignmentMr')->name('assignment.mr.assign.store');
    Route::post('assignment/pr/assign/store', 'Controlling\AssignmentController@storeAssignmentPr')->name('assignment.pr.assign.store');
    Route::get('assignment/tugas_material', 'Controlling\Assignment2Controller@assigmentMRIndex')->name('assignment.mr');
    Route::get('assignment/tugas_purchase', 'Controlling\Assignment2Controller@assigmentPRIndex')->name('assignment.pr');
});


Route::get('/', 'Auth\LoginController@showLoginForm');

/**
 * Membership
 */
Route::group(['as' => 'protection.'], function () {
    Route::get('membership', 'MembershipController@index')->name('membership')->middleware('protection:' . config('protection.membership.product_module_number') . ',protection.membership.failed');
    Route::get('membership/access-denied', 'MembershipController@failed')->name('membership.failed');
    Route::get('membership/clear-cache/', 'MembershipController@clearValidationCache')->name('membership.clear_validation_cache');
});

/**
 * Select2
 */

Route::get('/select-users', 'Admin\UserController@getUsers')->name('select.users');
Route::get('/select-employees', 'Admin\EmployeeController@getEmployees')->name('select.employees');
Route::get('/select-items', 'Admin\ItemController@getItems')->name('select.items');
Route::get('/select-extended_items', 'Admin\ItemController@getExtendedItems')->name('select.extended_items');
Route::get('/select-warehouses', 'Admin\WarehouseController@getWarehouses')->name('select.warehouses');
Route::get('/select-extended_warehouses', 'Admin\WarehouseController@getExtendedWarehouses')->name('select.extended_warehouses');
Route::get('/select-groups', 'Admin\GroupController@getGroups')->name('select.groups');
Route::get('/select-machineries', 'Admin\MachineryController@getMachineries')->name('select.machineries');
Route::get('/select-extended_machineries', 'Admin\MachineryController@getDetailedMachineries')->name('select.extended_machineries');
Route::get('/select-machinery_types', 'Admin\MachineryTypeController@getMachineryTypes')->name('select.machinery_types');
Route::get('/select-suppliers', 'Admin\SupplierController@getSuppliers')->name('select.suppliers');
Route::get('/select-material_requests', 'Admin\Inventory\MaterialRequestHeaderController@getMaterialRequests')->name('select.material_requests');
Route::get('/select-purchase_requests', 'Admin\Purchasing\PurchaseRequestHeaderController@getPurchaseRequests')->name('select.purchase_requests');
Route::get('/select-purchase_orders', 'Admin\Purchasing\PurchaseOrderHeaderController@getPurchaseOrders')->name('select.purchase_orders');
Route::get('/select-delivery_orders', 'Admin\Inventory\DeliveryOrderHeaderController@getDeliveryOrders')->name('select.delivery_orders');
Route::get('/select-purchase_invoices', 'Admin\Purchasing\PurchaseInvoiceHeaderController@getPurchaseInvoices')->name('select.purchase_invoices');
Route::get('/select-item_receipts', 'Admin\Inventory\ItemReceiptController@getItemReceipts')->name('select.item_receipts');
Route::get('/select-accounts', 'Admin\AccountController@getAccounts')->name('select.accounts');
Route::get('/select-assignment-users', 'Admin\UserController@getUserForAssignment')->name('select.assignment.users');

/**
 * Datatables
 */

// MASTER DATA
Route::get('/datatables-users', 'Admin\UserController@getIndex')->name('datatables.users');
Route::get('/datatables-employees', 'Admin\EmployeeController@getIndex')->name('datatables.employees');
Route::get('/datatables-items', 'Admin\ItemController@getIndex')->name('datatables.items');
Route::get('/datatables-groups', 'Admin\GroupController@anyData')->name('datatables.groups');
Route::get('/datatables-machineries', 'Admin\MachineryController@getIndex')->name('datatables.machineries');
Route::get('/datatables-machinery_types', 'Admin\MachineryTypeController@getIndex')->name('datatables.machinery_types');
Route::get('/datatables-machinery_categories', 'Admin\MachineryCategoryController@getIndex')->name('datatables.machinery_categories');
Route::get('/datatables-machinery_brands', 'Admin\MachineryBrandController@getIndex')->name('datatables.machinery_brands');
Route::get('/datatables-departments', 'Admin\DepartmentController@getIndex')->name('datatables.departments');
Route::get('/datatables-documents', 'Admin\DocumentController@anyData')->name('datatables.documents');
Route::get('/datatables-payment_methods', 'Admin\PaymentMethodController@anyData')->name('datatables.payment_methods');
Route::get('/datatables-uoms', 'Admin\UOMController@getIndex')->name('datatables.uoms');
Route::get('/datatables-warehouses', 'Admin\WarehouseController@getIndex')->name('datatables.warehouses');
Route::get('/datatables-sites', 'Admin\SiteController@getIndex')->name('datatables.sites');
Route::get('/datatables-suppliers', 'Admin\SupplierController@getIndex')->name('datatables.suppliers');
Route::get('/datatables-menus', 'Admin\MenuController@anyData')->name('datatables.menus');
Route::get('/datatables-menu_headers', 'Admin\MenuHeaderController@anyData')->name('datatables.menu_headers');
Route::get('/datatables-menu_subs', 'Admin\MenuSubController@anyData')->name('datatables.menu_subs');
Route::get('/datatables-statuses', 'Admin\StatusController@getIndex')->name('datatables.statuses');
Route::get('/datatables-notifications', 'Admin\NotificationController@getIndex')->name('datatables.notifications');
Route::get('/datatables-item_stock_notifications', 'Admin\ItemController@getIndexStockNotification')->name('datatables.item_stock_notifications');
Route::get('/datatables-monitor_mr', 'Admin\Controlling\MonitoringMRController@getIndex')->name('datatables.controlling.monitor.mr');

// PURCHASING
Route::get('/datatables-purchase_requests', 'Admin\Purchasing\PurchaseRequestHeaderController@getIndex')->name('datatables.purchase_requests');
Route::get('/datatables-purchase_requests_warning', 'Admin\Purchasing\PurchaseRequestHeaderController@getIndexWarning')->name('datatables.purchase_requests.warning');
Route::get('/datatables-quotations', 'Admin\Purchasing\QuotationHeaderController@getIndex')->name('datatables.quotations');
Route::get('/datatables-purchase_orders', 'Admin\Purchasing\PurchaseOrderHeaderController@getIndex')->name('datatables.purchase_orders');
Route::get('/datatables-purchase_invoices', 'Admin\Purchasing\PurchaseInvoiceHeaderController@getIndex')->name('datatables.purchase_invoices');
Route::get('/datatables-payment_requests', 'Admin\Purchasing\PaymentRequestController@getIndex')->name('datatables.payment_requests');
Route::get('/datatables-returs', 'Admin\Purchasing\ReturHeaderController@getIndex')->name('datatables.returs');

// INVENTORY
Route::get('/datatables-material_requests', 'Admin\Inventory\MaterialRequestHeaderController@getIndex')->name('datatables.material_requests');
Route::get('/datatables-delivery_orders', 'Admin\Inventory\DeliveryOrderHeaderController@getIndex')->name('datatables.delivery_orders');
Route::get('/datatables-issued_dockets', 'Admin\Inventory\DocketController@getIndex')->name('datatables.issued_dockets');
Route::get('/datatables-item_receipts', 'Admin\Inventory\ItemReceiptController@getIndex')->name('datatables.item_receipts');
Route::get('/datatables-stock_adjustments', 'Admin\Inventory\StockAdjustmentController@getIndex')->name('datatables.stock_adjustments');
Route::get('/datatables-stock_ins', 'Admin\Inventory\StockInController@getIndex')->name('datatables.stock_ins');
Route::get('/datatables-stock_cards', 'Admin\Inventory\StockCardController@getIndex')->name('datatables.stock_cards');
Route::get('/datatables-item_mutations', 'Admin\Inventory\ItemMutationController@getIndex')->name('datatables.item_mutations');
Route::get('/datatables-interchanges', 'Admin\Inventory\InterchangeController@getIndex')->name('datatables.interchanges');
Route::get('/datatables-purchase_orders_for_gr', 'Admin\Inventory\ItemReceiptController@getPurchaseOrder')->name('datatables.purchase_orders_for_gr');
Route::get('/datatables-item_stocks', 'Admin\ItemStockController@getIndex')->name('datatables.item_stocks');

// AUTHORIZATION
Route::get('/datatables-permission-documents', 'Admin\PermissionDocumentController@getIndex')->name('datatables.permission_documents');
Route::get('/datatables-permission-menus', 'Admin\PermissionMenuController@getIndex')->name('datatables.permission_menus');
Route::get('/datatables-approval-rules', 'Admin\ApprovalRuleController@getIndex')->name('datatables.approval_rules');
Route::get('/datatables-roles', 'Admin\RoleController@getIndex')->name('datatables.roles');

// OTHER
Route::get('/datatables-accounts', 'Admin\AccountController@getIndex')->name('datatables.accounts');
Route::get('/datatables-reorders', 'Admin\ReorderController@getIndex')->name('datatables.reorders');

Route::get('/datatables-material_requests_index_chart', 'Admin\DashboardController@getIndexMR')->name('datatables.material_requests_index_chart');
Route::get('/datatables-purchase_request_index_chart', 'Admin\DashboardController@getIndexPR')->name('datatables.purchase_request_index_chart');
Route::get('/datatables-purchase_order_index_chart', 'Admin\DashboardController@getIndexPO')->name('datatables.purchase_order_index_chart');

// CONTROLLING
Route::get('/datatables-history_assigment_mr', 'Admin\Controlling\Assignment1Controller@getIndexMr')->name('datatables.history_assigment_mr');
Route::get('/datatables-history_assigment_pr', 'Admin\Controlling\AssignmentController@getIndexPr')->name('datatables.history_assigment_pr');
Route::get('/datatables-staff_assigment_mr', 'Admin\Controlling\Assignment2Controller@getIndexMr')->name('datatables.staff_assigment_mr');
Route::get('/datatables-staff_assigment_pr', 'Admin\Controlling\Assignment2Controller@getIndexPr')->name('datatables.staff_assigment_pr');

// DOCUMENTS
Route::get('/documents/purchase-request', function (){
   return view('documents.Victor23mega PR Example');
});

//Testing
Route::get('/excel', 'ExcelController@index')->name('excel-view');
Route::post('/excel-post', 'ExcelController@readData')->name('excel-post');
Route::get('/mail/test', 'Admin\setting\SettingController@emailTest')->name('mail_test');
Route::get('/test', 'HomeController@test')->name('approval_test');
Route::get('/testEmail', 'HomeController@testBulkEmail')->name('mail_test');