<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductImageController as AdminProductImageController;
use App\Http\Controllers\Admin\ProductVariantController as AdminProductVariantController;
use App\Http\Controllers\Admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Admin\ReturnController as AdminReturnController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login');
Route::post('/inscription', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register');
Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
Route::post('/email/renvoyer', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1')->name('verification.resend');

Route::middleware('auth')->prefix('mon-compte')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::post('/adresse', [AccountController::class, 'updateAddress'])->name('address.update');
    Route::delete('/', [AccountController::class, 'destroy'])->name('destroy');
    Route::get('/commandes', [AccountController::class, 'orders'])->name('orders');
    Route::get('/commandes/{order}', [AccountController::class, 'orderShow'])->name('orders.show');
    Route::get('/retours', [ReturnController::class, 'index'])->name('returns');
    Route::post('/retours', [ReturnController::class, 'store'])->name('returns.store');
});

Route::get('/recherche', [SearchController::class, 'index'])->name('search');
Route::get('/recherche/suggestions', [SearchController::class, 'suggest'])->name('search.suggestions');

Route::post('/assistant/message', [AssistantController::class, 'message'])->middleware('throttle:15,1')->name('assistant.message');

Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
Route::post('/panier/ajouter', [CartController::class, 'store'])->name('cart.add');
Route::post('/panier/variante', [CartController::class, 'assignVariant'])->name('cart.assignVariant');
Route::post('/panier/retirer', [CartController::class, 'destroy'])->name('cart.remove');

Route::get('/commande', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/commande', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/commande/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::get('/commandes/{order}/facture', [InvoiceController::class, 'show'])->name('orders.invoice');

Route::get('/produit/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/produit/{product}/avis', [ReviewController::class, 'store'])->middleware('auth')->name('products.reviews.store');

// Back-office (sections 40 à 49 du cahier des charges) — réservé Gestionnaire/Admin/Super Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'staff'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', AdminProductController::class)->except('show');
    Route::post('products/{product}/images', [AdminProductImageController::class, 'store'])->name('products.images.store');
    Route::delete('products/{product}/images/{image}', [AdminProductImageController::class, 'destroy'])->name('products.images.destroy');
    Route::post('products/{product}/variants', [AdminProductVariantController::class, 'store'])->name('products.variants.store');
    Route::put('products/{product}/variants/{variant}', [AdminProductVariantController::class, 'update'])->name('products.variants.update');
    Route::delete('products/{product}/variants/{variant}', [AdminProductVariantController::class, 'destroy'])->name('products.variants.destroy');

    Route::resource('categories', AdminCategoryController::class)->except('show');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/confirmer', [AdminOrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('orders/{order}/annuler', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/statut', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::resource('promotions', AdminPromotionController::class)->except('show');
    Route::resource('coupons', AdminCouponController::class)->except('show');
    Route::resource('banners', AdminBannerController::class)->except('show');

    Route::get('reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('reviews/{review}/publier', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('retours', [AdminReturnController::class, 'index'])->name('returns.index');
    Route::put('retours/{return}', [AdminReturnController::class, 'update'])->name('returns.update');

    Route::get('staff', [AdminStaffController::class, 'index'])->name('staff.index');
    Route::get('staff/create', [AdminStaffController::class, 'create'])->name('staff.create');
    Route::post('staff', [AdminStaffController::class, 'store'])->name('staff.store');
    Route::delete('staff/{user}', [AdminStaffController::class, 'destroy'])->name('staff.destroy');
});

// Route catalogue en dernier : {category:slug} matcherait sinon les segments ci-dessus
Route::get('/{category:slug}', [CatalogController::class, 'show'])->name('catalog.show');
