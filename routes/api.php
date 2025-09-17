<?php

use App\Http\Controllers\Api\BlogController as ApiBlogController;
use App\Http\Controllers\Admin\TreeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CMSPageController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\FAQController;
use App\Http\Controllers\Api\FeedTreeController;
use App\Http\Controllers\Api\FirebasePhoneAuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShippingAddressController;
use App\Http\Controllers\Api\Supervisor\SupervisorController;
use App\Http\Controllers\Api\TreeController as ApiTreeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WishListController;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/signup', [AuthController::class, 'signUp']);
Route::post('/signin', [AuthController::class, 'signIn']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::get('/app-config', [AuthController::class, 'getAppConfig']);
Route::post('/reactivate-account', [AuthController::class, 'reactivateAccount']);

// middleware('auth:supervisors')
// Route::middleware('auth:sanctum')->group(function () {
Route::middleware('auth:users')->group(function () {
    Route::post('/deactivate-account', [AuthController::class, 'deactivateAccount']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [UserController::class, 'show']);
    Route::get('user/referrals', [UserController::class, 'referrals']);
    Route::put('user', [UserController::class, 'update']);
    Route::get('trees', [ApiTreeController::class, 'index']);
    Route::get('adopt-trees', [ApiTreeController::class, 'adoptTrees']);
    Route::get('tree/{id}', [ApiTreeController::class, 'show']);
    Route::get('feed-trees', [FeedTreeController::class, 'index']);
    Route::get('feed-tree/{id}', [FeedTreeController::class, 'show']);
    Route::post('feed-tree/{campaignId}/donation/initiate', [DonationController::class, 'initiatePayment']);
    Route::post('update-fcm-token', [AuthController::class, 'updateFcmToken']);
    Route::post('feed-tree/donation-payment/callback', [DonationController::class, 'paymentCallback']);
    Route::post('/cart/add/{productId}', [CartController::class, 'addCart'])->name('cart.add');
    Route::post('/cart/addDetails/{cartId}', [CartController::class, 'addCartDetails'])->name('cart.addCartDetails');
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::delete('/cart/remove/{cartId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');

    Route::post('/checkout', [PaymentController::class, 'createOrder'])->name('cart.checkout');
    // Route::post('/order/place', [CartController::class, 'placeOrder'])->name('order.place');


    // Route::post('/payment/order', [PaymentController::class, 'createOrder'])->name('payment.createOrder');
    Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');



    Route::post('/wishlist/add/{tree}', [WishListController::class, 'add']);
    Route::delete('/wishlist/remove/{tree}', [WishListController::class, 'remove']);
    Route::get('/wishlist', [WishListController::class, 'index']);

    Route::get('notifications', [NotificationController::class, 'getNotifications']);
    Route::get('/orders', [PaymentController::class, 'getMyOrders']);
    Route::get('/my-donations', [DonationController::class, 'getUserDonations']);
    Route::get('/order/{orderId}', [PaymentController::class, 'getOrderDetails']);

    //Start Ecommerce
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/product/{id}', [ProductController::class, 'show']);
    Route::get('/product/{id}/ratings', [ProductController::class, 'getRatings']);
    Route::get('/product/{id}/reviews', [ProductController::class, 'getReviews']);
    Route::get('/categories', [ProductController::class, 'getCategories']);
    // Route::apiResource('shipping-addresses', ShippingAddressController::class);
    Route::get('/shipping-addresses', [ShippingAddressController::class, 'index']); // Get all addresses
    Route::post('/shipping-address', [ShippingAddressController::class, 'store']); // Add new address
    Route::put('/shipping-address/{id}', [ShippingAddressController::class, 'update']); // Update address
    Route::delete('/shipping-address/{id}', [ShippingAddressController::class, 'destroy']); // Delete address
    Route::post('/apply-coupon', [PaymentController::class, 'applyCoupon']);
    Route::post('/remove-coupon', [PaymentController::class, 'removeCoupon']);
    Route::get('/coupons', [ProductController::class, 'getCoupons']);

    Route::post('/cart/apply-coupon-all', [CartController::class, 'applyCouponToAll']);

    Route::post('product/{id}/reviews', [ProductController::class, 'writeReview']);
    Route::put('product/{id}/reviews', [ProductController::class, 'updateReview']);
    Route::get('product/{id}/can-review', [ProductController::class, 'canReview']);
    Route::get('products/recommendation', [ProductController::class, 'getCategoriesWiseProducts']);
    Route::put('order/{id}/update-shipping-address', [PaymentController::class, 'updateShippingAddress']);

    //End Ecommerce

});

Route::get('blogs', [ApiBlogController::class, 'index']);
Route::get('sliders', [ApiBlogController::class, 'getSliders']);
Route::get('blog/{id}', [ApiBlogController::class, 'show']);

Route::get('/states', [LocationController::class, 'getStates']);
Route::get('/cities/{state_id}', [LocationController::class, 'getCities']);
Route::get('/areas/{city_id}', [LocationController::class, 'getAreas']);
Route::get('/tree-locations', [LocationController::class, 'getTreeLocations']);
Route::get('/tree-locations/states', [LocationController::class, 'getTreeStates']);
Route::get('/tree-locations/states/{stateId}/areas', [LocationController::class, 'getTreeAreasByState']);

Route::get('about-app', [CMSPageController::class, 'aboutApp'])->name('cms.about-app');
Route::get('privacy-policy', [CMSPageController::class, 'privacyPolicy'])->name('cms.privacy-policy');
Route::get('terms-of-use', [CMSPageController::class, 'termsOfUse'])->name('cms.terms-of-use');
Route::get('quick-start', [CMSPageController::class, 'quickStart'])->name('cms.quick-start');
Route::get('faqs', [FAQController::class, 'index']);
// Route::post('testOtp', [FirebasePhoneAuthController::class, 'sendVerificationCode'])->name('fcm.test');



// Route::resource('campaigns', CampaignController::class);
// Route::post('campaigns/{campaign}/donate', [DonationController::class, 'store'])->name('donations.store');

// Route::get('/', [CampaignController::class, 'index']);
// Route::resource('campaigns', CampaignController::class);
// Route::post('campaigns/{campaign}/donate', [DonationController::class, 'store']);

// Supervisor Routes



Route::prefix('supervisor')->group(function () {
    Route::post('/signin', [SupervisorController::class, 'signIn']);
    Route::post('/verify-otp', [SupervisorController::class, 'verifyOtp']);
    Route::post('/resend-otp', [SupervisorController::class, 'resendOtp']);
});
Route::prefix('supervisor')->middleware('auth:supervisors')->group(function () {
    Route::get('orders', [SupervisorController::class, 'orders']);
    Route::post('logout', [SupervisorController::class, 'logout']);
    // Route::post('tree_plantations', [SupervisorController::class, 'updateTreePlantationDetails']);
    Route::post('tree-plantation/update', [SupervisorController::class, 'updateTreePlantation']);
    Route::get('tree-plantation/details/{tree_id?}', [SupervisorController::class, 'getTreePlantationDetailsByTreeId']);
    Route::post('tree-plantation/upload-images/{tree_plantation_id}', [SupervisorController::class, 'uploadImages']);
// Route::prefix('supervisor')->middleware('auth:supervisors')->group(function () {
//     Route::get('/dashboard', [SupervisorController::class, 'dashboard']);
//     // Other supervisor-specific routes...
});
