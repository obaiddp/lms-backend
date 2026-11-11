<?php

namespace App\Http\Controllers\Addon\Product\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Addon\Product\Product;
use App\Models\Order_item;
use App\Traits\ApiStatusTrait;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use General , ImageSaveTrait, SendNotification, ApiStatusTrait;
    public function dashboard()
    {
        $data['title'] = __('Dashboard');
        $data['navProductActiveClass'] = 'has-open';
        $data['subNavProductDashboardActiveClass'] = 'active';

        $data['pendingOrder'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->where('delivery_status', 0)
        ->count();
        
        $data['processingOrder'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereIn('delivery_status', [1,2])
        ->count();
        
        $data['deliveredOrder'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->where('delivery_status', 3)
        ->count();
       
        $data['cancelledOrder'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->where('delivery_status', 4)
        ->count();
       
        $data['thisMonthSale'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->count();
        
        $data['thisMonthEarning'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->sum('owner_balance');

        $data['thisYearSale'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->count();
        
        $data['thisYearEarning'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })->whereNotNull('product_id')
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->sum('owner_balance');

        $data['products'] =  Product::where('user_id', auth()->id())->where('status', STATUS_SUCCESS)->paginate(10);


        $top5Product = Product::where('products.user_id', auth()->id())
                ->join('order_items', 'order_items.product_id', 'products.id')
                ->join('orders', 'orders.id', 'order_items.order_id')
                ->where('orders.payment_status', 'paid')
                ->selectRaw('products.title, COALESCE(sum(order_items.unit),0) total')
                ->groupBy('products.id')
                ->orderBy('total','desc')
                ->take(5)
                ->get();

        $data['top5ProductLabels'] = $top5Product->pluck('title')->toArray();
        $data['top5ProductSeries'] = $top5Product->pluck('total')->toArray();

        $thisMonthSale = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })
        ->whereNotNull('product_id')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->selectRaw('DATE_FORMAT(created_at, "%d") as day, COALESCE(sum(order_items.unit),0) total')
        ->groupBy('day')
        ->get();
       
        $thisMonthEarning = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
            $q->where('owner_user_id', auth()->id());
        })
        ->whereNotNull('product_id')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->where('delivery_status', '!=', 4)
        ->selectRaw('DATE_FORMAT(created_at, "%d") as day, COALESCE(sum(order_items.owner_balance),0) total')
        ->groupBy('day')
        ->get();

        $chartThisMonthSaleData = [];
        $chartThisMonthSaleCategories = [];
        $chartThisMonthEarningData = [];
        $chartThisMonthEarningCategories = [];

        for ($x = 1; $x <= Carbon::now()->month()->daysInMonth; $x++) {
            array_push($chartThisMonthSaleCategories, $x);
            array_push($chartThisMonthSaleData, $thisMonthSale->where('day', $x)->sum('total', 0));
            array_push($chartThisMonthEarningCategories, $x);
            array_push($chartThisMonthEarningData, $thisMonthEarning->where('day', $x)->sum('total', 0));
        }


        $data['chartThisMonthSaleData'] = $chartThisMonthSaleData;
        $data['chartThisMonthSaleCategories'] = $chartThisMonthSaleCategories;
        $data['chartThisMonthEarningData'] = $chartThisMonthEarningData;
        $data['chartThisMonthEarningCategories'] = $chartThisMonthEarningCategories;
       

        return view('addon.product.instructor.dashboard', $data);
    }
}
