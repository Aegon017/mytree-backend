<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ApiLogController;
use App\Http\Controllers\Admin\AreasController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CMSController;
use App\Http\Controllers\Admin\TreeController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\EcomOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\LocationsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\CKEditorController;
use Google\Cloud\Location\Location;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\PermissionMiddleware;
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/


// Route::get('/', [AdminAuthController::class, 'index'])->name('Admin');
Route::get('/', [AdminAuthController::class, 'index'])->name('Admin');
Route::get('/login', [AdminAuthController::class, 'index'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');


Route::group(['middleware' => 'auth:admin'], function () {
    Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/location', [AdminAuthController::class, 'location'])->name('location');
    Route::post('/store-location', [AdminAuthController::class, 'storeLocation'])->name('store-location');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/change-password', [AdminAuthController::class, 'changePassword'])->name('admin.changePassword')->middleware(['permission:change_password']);
    Route::post('/update-password', [AdminAuthController::class, 'updatePassword'])->name('admin.updatePassword')->middleware(['permission:change_password']);


     /*-->Tree*/
     Route::get('/tree-list', [TreeController::class, 'manageTree'])->name('admin.tree')->middleware(['permission:tree_list']);
     Route::post('/tree-status', [TreeController::class, 'enableOrDisableTree'])->name('tree-status')->middleware(['permission:tree_status']); //->middleware(['auth'])
     Route::post('/tree-delete', [TreeController::class, 'destroyTree'])->name('tree-delete')->middleware(['permission:tree_delete']);

     Route::resource(
         'tree',
         'App\Http\Controllers\Admin\TreeController'
     )->except(['create'])->middleware(['permission:tree_view|tree_insert|tree_update']);
     //     /*End Tree<--*/

     /*-->Campaign*/
     Route::get('/campaign-list', [CampaignController::class, 'manageTree'])->name('admin.campaign');
     Route::post('/campaign-status', [CampaignController::class, 'enableOrDisableTree'])->name('campaign-status');
     Route::post('/campaign-delete', [CampaignController::class, 'destroyTree'])->name('campaign-delete');

     Route::resource(
         'campaign',
         'App\Http\Controllers\Admin\CampaignController'
     )->except(['create']);
     //     /*End Campaign<--*/


     /*-->Blog*/
     Route::get('/blog-list', [BlogController::class, 'manageBlog'])->name('admin.blogs');
     Route::post('/blog-status', [BlogController::class, 'enableOrDisableBlog'])->name('blogs-status'); //->middleware(['auth'])
     Route::post('/blog-delete', [BlogController::class, 'destroyBlog'])->name('blogs-delete');

     Route::resource(
         'blogs',
         'App\Http\Controllers\Admin\BlogController'
     )->except(['create']);
     //     /*End Blog<--*/

      /*-->Slider*/
      Route::get('/slider-list', [SliderController::class, 'manageSlider'])->name('admin.sliders');
      Route::post('/slider-status', [SliderController::class, 'enableOrDisableSlider'])->name('sliders-status'); //->middleware(['auth'])
      Route::post('/slider-delete', [SliderController::class, 'destroySlider'])->name('sliders-delete');

      Route::resource(
          'sliders',
          'App\Http\Controllers\Admin\SliderController'
      )->except(['create']);
      //     /*End Slider<--*/




    /*-->Employee*/
    Route::middleware(['permission:employee_list|employee_view|employee_insert|employee_delete|employee_update|employee_status'])->group(function () {
        //     Route::get('/articles/edit', [ArticleController::class, 'edit']);
        // });
    Route::get('/employee-list', [EmployeeController::class, 'manageEmployee'])->name('admin.employee');
    Route::post('/employee-status', [EmployeeController::class, 'enableOrDisableEmployee'])->name('employee-status'); //->middleware(['auth'])
    Route::post('/employee-delete', [EmployeeController::class, 'destroyEmployee'])->name('employee-delete');
    Route::resource(
        'employee',
        'App\Http\Controllers\Admin\EmployeeController'
    )->except(['create']);
    });
    //     /*End Employee<--*/

    Route::middleware(['permission:users_list'])->group(function () {
    Route::get('/user-list', [UserController::class, 'manageUser'])->name('admin.user');
    Route::resource(
        'user',
        'App\Http\Controllers\Admin\UserController'
    )->except(['create']);
    });
    Route::get('/contact-list', [ContactController::class, 'manageContact'])->name('admin.contact');
    Route::resource(
        'contact',
        'App\Http\Controllers\Admin\ContactController'
    )->except(['create']);

    /*orders*/
    Route::get('/order-list', [OrderController::class, 'manageOrder'])->name('admin.order');
    Route::get('/order-invoice/{orderId}', [OrderController::class, 'invoice'])->name('order.invoice');
    Route::resource(
        'order',
        'App\Http\Controllers\Admin\OrderController'
    )->except(['create']);
    /*orders*/

         /*-->Reports*/
         Route::get('/reports-list', [ReportController::class, 'manageReports'])->name('admin.report');
         Route::get('/reports-orders', [ReportController::class, 'manageOrderReports'])->name('admin.report.orders');
         Route::get('/reports-orders-list', [ReportController::class, 'getMonthlyOrderReport'])->name('admin.report.orders.list');
         Route::get('/reports-orders-counts/{start}/{end}', [ReportController::class, 'getMonthlyOrderCountReport'])->name('admin.report.orders.count');

         Route::resource(
             'reports',
             'App\Http\Controllers\Admin\ReportController'
         )->except(['create']);
         //     /*End Reports<--*/

    // Route::middleware(['permission:edit articles'])->group(function () {
    //     Route::get('/articles/edit', [ArticleController::class, 'edit']);
    // });
    Route::middleware(['permission:locations_manage'])->group(function () {
    /*locations*/
        /*-->states*/
        Route::get('/states-list', [StateController::class, 'manageState'])->name('admin.states');
        Route::post('/states-status', [StateController::class, 'enableOrDisableState'])->name('states-status'); //->middleware(['auth'])
        Route::post('/states-delete', [StateController::class, 'destroyState'])->name('states-delete');
        Route::resource(
            'states',
            'App\Http\Controllers\Admin\StateController'
        )->except(['create']);
        /*End states<--*/
        /*-->Cities*/
        Route::get('city/{stateId}', [CityController::class, 'getCities'])->name('getCities');
        Route::get('cities/{cityId}', [CityController::class, 'getCity'])->name('cities');
        Route::get('/cities-list', [CityController::class, 'manageCity'])->name('admin.cities');
        Route::post('/cities-status', [CityController::class, 'enableOrDisableCity'])->name('cities-status'); //->middleware(['auth'])
        Route::post('/cities-delete', [CityController::class, 'destroyCity'])->name('cities-delete');
        Route::resource(
            'cities',
            'App\Http\Controllers\Admin\CityController'
        )->except(['create']);
        /*End Cities<--*/
        /*-->Areas*/
        Route::get('areas/{cityId}', [AreasController::class, 'getAreas'])->name('area');
        Route::get('/areas-list', [AreasController::class, 'manageAreas'])->name('admin.areas');
        Route::post('/areas-status', [AreasController::class, 'enableOrDisableAreas'])->name('areas-status'); //->middleware(['auth'])
        Route::post('/areas-delete', [AreasController::class, 'destroyAreas'])->name('areas-delete');
        Route::resource(
            'areas',
            'App\Http\Controllers\Admin\AreasController'
        )->except(['create']);
        /*End Areas<--*/

        /*-->Tree Locations*/
        Route::get('/locations-list', [LocationsController::class, 'manageLocations'])->name('admin.locations');
        Route::post('/locations-status', [LocationsController::class, 'enableOrDisableLocation'])->name('locations-status');
        Route::post('/locations-delete', [LocationsController::class, 'destroyLocation'])->name('locations-delete');
        Route::resource(
            'locations',
            'App\Http\Controllers\Admin\LocationsController'
        )->except(['create']);
        /*End Tree Locations<--*/
    });
    /*locations*/

    Route::middleware(['permission:admin'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
    Route::get('/api-logs', [ApiLogController::class, 'index'])->name('admin.api-logs')->middleware(['permission:api_logs']);

    Route::middleware(['permission:cms'])->group(function () {
        // Route::get('/cms', [CMSController::class, 'index'])->name('cms.index');
        // Route::get('/cms/{id}/edit', [CMSController::class, 'edit'])->name('cms.edit');
        // Route::put('/cms/{id}', [CMSController::class, 'update'])->name('cms.update');

        Route::prefix('cms')->group(function () {
            Route::get('/', [CMSController::class, 'index'])->name('cms.index');
            Route::get('/create', [CMSController::class, 'create'])->name('cms.create');
            Route::post('/', [CMSController::class, 'store'])->name('cms.store');
            Route::get('/{id}/edit', [CMSController::class, 'edit'])->name('cms.edit');
            Route::put('/{id}', [CMSController::class, 'update'])->name('cms.update');
            Route::delete('/{id}', [CMSController::class, 'destroy'])->name('cms.destroy');
        });
    });

    Route::resource('faqs', FAQController::class)->middleware(['permission:faqs']);

    Route::resource('notifications', NotificationController::class)->middleware(['permission:notifications']);

    Route::get('getUsers', [NotificationController::class, 'loadUsers'])->name('notifications.loadUsers');
    
    Route::post('product/image/delete', [ProductController::class, 'deleteImage'])->name('product.image.delete');
    Route::post('tree/image/delete', [TreeController::class, 'deleteImage'])->name('tree.image.delete');


    //Ecommerce Start

        Route::middleware(['permission:ecommerce_manage'])->group(function () {
            /*-->category*/
            Route::get('/category-list', [CategoryController::class, 'manageCategory'])->name('admin.category');
            Route::post('/category-status', [CategoryController::class, 'enableOrDisableCategory'])->name('category-status'); //->middleware(['auth'])
            Route::post('/category-delete', [CategoryController::class, 'destroyCategory'])->name('category-delete');
            Route::resource(
                'category',
                'App\Http\Controllers\Admin\CategoryController'
            )->except(['create']);
            /*End category<--*/

             /*-->Products*/
            Route::get('/product-list', [ProductController::class, 'manageProduct'])->name('admin.product');
            Route::post('/product-status', [ProductController::class, 'enableOrDisableProduct'])->name('product-status');
            Route::post('/product-delete', [ProductController::class, 'destroyProduct'])->name('product-delete');

            Route::resource(
                'product',
                'App\Http\Controllers\Admin\ProductController'
            )->except(['create']);
         /*End Products<--*/

         /*-->coupons*/
         Route::get('/coupon-list', [CouponController::class, 'manageCoupon'])->name('admin.coupon');
         Route::post('/coupon-status', [CouponController::class, 'enableOrDisableCoupon'])->name('coupon-status'); //->middleware(['auth'])
         Route::post('/coupon-delete', [CouponController::class, 'destroyCoupon'])->name('coupon-delete');
         Route::resource(
             'coupon',
             'App\Http\Controllers\Admin\CouponController'
         )->except(['create']);
         /*End coupons<--*/

         /*ecom-orders*/
    Route::get('/ecomorder-list', [EcomOrderController::class, 'manageOrder'])->name('admin.ecomorder');
    Route::get('/ecomorder-invoice/{orderId}', [EcomOrderController::class, 'invoice'])->name('ecomorder.invoice');
    Route::resource(
        'ecomorder',
        'App\Http\Controllers\Admin\EcomOrderController'
    )->except(['create']);
    /*ecom-orders*/

        });


        //

        //Ecommerce End

        Route::post('/ckeditor', [StateController::class, 'enableOrDisableState'])->name('states-status');
});

Route::post('/ckeditor/upload', [CKEditorController::class, 'upload'])->name('ckeditor.upload');
// Route::post('/upload-image', [CKEditorController::class, 'upload'])->name('upload.image');
