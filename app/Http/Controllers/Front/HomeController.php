<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use App\Models\PurchaseNotification;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('localize');
    }

    public function index()
    {
        $setting = Setting::first();

        // Get featured/hot deal products for hero slider
        // Check both is_featured flag and is_type for backward compatibility
        $featuredProducts = Item::where('status', 1)
            ->where(function ($query) {
                $query->where('is_featured', 1)
                    ->orWhere('is_type', 'feature')
                    ->orWhere('is_type', 'flash_deal');
            })
            ->with(['category', 'galleries', 'reviews' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->orderBy('id', 'desc')
            ->get();

        // Get dummy products for best selling section (show latest active products)
        $bestSellingProducts = Item::where('status', 1)
            ->with(['category' => function($query) {
                $query->withDefault();
            }, 'galleries', 'reviews' => function ($query) {
                $query->where('status', 'approved');
            }])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Get latest approved reviews (more for scrollable list)
        $latestReviews = Review::where('status', 'approved')
            ->with(['item' => function ($query) {
                $query->where('status', 1);
            }])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Calculate overall rating statistics
        $allApprovedReviews = Review::where('status', 'approved')->get();
        $totalReviews = $allApprovedReviews->count();
        $averageRating = $totalReviews > 0 ? round($allApprovedReviews->avg('rating'), 1) : 0;
        
        // Calculate star distribution
        $starDistribution = [
            5 => $allApprovedReviews->where('rating', 5)->count(),
            4 => $allApprovedReviews->where('rating', 4)->count(),
            3 => $allApprovedReviews->where('rating', 3)->count(),
            2 => $allApprovedReviews->where('rating', 2)->count(),
            1 => $allApprovedReviews->where('rating', 1)->count(),
        ];
        
        // Calculate percentages
        foreach ($starDistribution as $stars => $count) {
            $starDistribution[$stars] = [
                'count' => $count,
                'percentage' => $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0
            ];
        }

        // Get statistics for hero section
        $totalProducts = Item::where('status', 1)->count();
        $totalOrders = Order::where('order_status', '!=', 'canceled')->count();
        $totalCustomers = User::count(); // All users are customers (admins are in separate table)

        return view('front.home.index', [
            'featuredProducts' => $featuredProducts,
            'bestSellingProducts' => $bestSellingProducts,
            'latestReviews' => $latestReviews,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating,
            'starDistribution' => $starDistribution,
            'setting' => $setting,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
        ]);
    }
}
