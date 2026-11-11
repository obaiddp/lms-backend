<?php

use App\Http\Controllers\Addon\Product\Instructor\ProductController;
use App\Http\Controllers\Addon\Product\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Addon\Product\Admin\ProductCategoryController;
use App\Http\Controllers\Addon\Product\Admin\ProductTagController;
use App\Http\Controllers\Addon\Product\Frontend\HomeController;
use App\Http\Controllers\Addon\Product\Instructor\DashboardController;
use App\Http\Controllers\Addon\Product\Student\PurchaseController;
use Illuminate\Support\Facades\Route;
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

Route::group(['as' => 'lms_product.'], function () {
    Route::as('frontend.')->group(function () {
        // Route::get('/product-list', [HomeController::class, 'list'])->name('list');
        Route::get('/product-list/{category?}', [HomeController::class, 'list'])->name('list');
        Route::get('/product-view/{slug}', [HomeController::class, 'view'])->name('view');
        Route::get('/product-view-student/{slug}', [HomeController::class, 'view2'])->name('view-student');
    });

    Route::group(['middleware' => 'auth'], function(){
        Route::as('student.')->prefix('student')->group(function () {
            Route::get('/purchase-list', [PurchaseController::class, 'list'])->name('purchase_list');
            Route::post('product-review-create', [PurchaseController::class, 'reviewCreate'])->name('review.create');
        });

        Route::as('instructor.')->prefix('instructor')->group(function () {
            Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
                Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
                Route::get('my-product', [ProductController::class, 'index'])->name('my-product');
                Route::get('upload/{step}', [ProductController::class, 'upload'])->name('upload');
                Route::post('upload', [ProductController::class, 'store'])->name('upload.store');
                Route::delete('delete/{slug}', [ProductController::class, 'delete'])->name('delete');
                Route::get('edit/{slug}', [ProductController::class, 'edit'])->name('edit');
                Route::patch('update/{slug}', [ProductController::class, 'update'])->name('update');
                Route::post('add-stock', [ProductController::class, 'addStock'])->name('add-stock');
                Route::get('orders', [ProductController::class, 'orders'])->name('orders');
                Route::post('orders/status-change', [ProductController::class, 'statusChange'])->name('orders.status_change');
            });
        });


        Route::as('admin.')->prefix('admin')->group(function () {
            Route::group(['prefix' => 'product-category', 'as' => 'category.'], function () {
                Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
                Route::get('create', [ProductCategoryController::class, 'create'])->name('create');
                Route::post('store', [ProductCategoryController::class, 'store'])->name('store');
                Route::get('edit/{slug}', [ProductCategoryController::class, 'edit'])->name('edit');
                Route::post('update/{slug}', [ProductCategoryController::class, 'update'])->name('update');
                Route::get('delete/{slug}', [ProductCategoryController::class, 'delete'])->name('delete');
            });

            Route::group(['prefix' => 'product-tag', 'as' => 'tag.'], function () {
                Route::get('/', [ProductTagController::class, 'index'])->name('index');
                Route::get('create', [ProductTagController::class, 'create'])->name('create');
                Route::post('store', [ProductTagController::class, 'store'])->name('store');
                Route::get('edit/{slug}', [ProductTagController::class, 'edit'])->name('edit');
                Route::patch('update/{slug}', [ProductTagController::class, 'update'])->name('update');
                Route::get('delete/{slug}', [ProductTagController::class, 'delete'])->name('delete');
            });

            Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
                Route::get('/dashboard', [AdminProductController::class, 'dashboard'])->name('dashboard');
                Route::get('/', [AdminProductController::class, 'index'])->name('index');
                Route::get('create/{step}', [AdminProductController::class, 'create'])->name('create');
                Route::post('store', [AdminProductController::class, 'store'])->name('store');
                Route::get('edit/{slug}', [AdminProductController::class, 'edit'])->name('edit');
                Route::patch('update/{slug}', [AdminProductController::class, 'update'])->name('update');
                Route::get('approved', [AdminProductController::class, 'approved'])->name('approved');
                Route::get('pending', [AdminProductController::class, 'pending'])->name('pending');
                Route::get('upcoming', [AdminProductController::class, 'upcoming'])->name('upcoming');
                Route::get('hold', [AdminProductController::class, 'hold'])->name('hold');
                Route::get('status-change/{slug}/{status}', [AdminProductController::class, 'statusChange'])->name('status-change');
                Route::post('feature-change', [AdminProductController::class, 'featureChange'])->name('feature-change');
                Route::get('delete/{slug}', [AdminProductController::class, 'delete'])->name('delete');
                Route::post('add-stock', [AdminProductController::class, 'addStock'])->name('add-stock');
                Route::get('orders', [AdminProductController::class, 'orders'])->name('orders');
                Route::post('orders/status-change', [AdminProductController::class, 'statusChangeOrder'])->name('orders.status_change');
            });
        });
    });
});
