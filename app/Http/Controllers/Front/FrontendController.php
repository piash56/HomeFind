<?php

namespace App\Http\Controllers\Front;

use App\{
    Http\Controllers\Controller,
    Repositories\Front\FrontRepository
};
use App\Models\Item;
use App\Models\Order;
use App\Models\TrackOrder;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    protected $repository;

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Front\FrontRepository $repository
     *
     */
    public function __construct(FrontRepository $repository)
    {
        $this->middleware('localize');
        $this->repository = $repository;
    }

    // -------------------------------- PRODUCT ----------------------------------------

    public function product($slug)
    {
        $item = Item::where('slug', $slug)->firstOrFail();
        $galleries = $item->galleries;
        $attributes = $item->attributes()->with(['options' => function ($query) {
            $query->with('galleryImage');
        }])->get();
        $reviews = $item->reviews()->paginate(5);

        // Process specifications
        $sec_name = [];
        $sec_details = [];
        if ($item->is_specification == 1 && $item->specification_name && $item->specification_description) {
            $sec_name = json_decode($item->specification_name, true) ?? [];
            $sec_details = json_decode($item->specification_description, true) ?? [];
        }

        // Get related products - use selected ones if available, otherwise use category-based
        $related_products = [];
        if ($item->related_products) {
            $relatedProductIds = json_decode($item->related_products, true);
            if (!empty($relatedProductIds)) {
                $related_products = Item::with('attributes')
                    ->whereIn('id', $relatedProductIds)
                    ->where('id', '!=', $item->id)
                    ->whereStatus(1)
                    ->take(8)
                    ->get();
            }
        }

        // Fallback to category-based if no related products selected
        if (empty($related_products)) {
            $related_products = Item::with('attributes')
                ->where('category_id', $item->category_id)
                ->where('id', '!=', $item->id)
                ->whereStatus(1)
                ->take(8)
                ->get();
        }

        return view('front.catalog.product', [
            'item' => $item,
            'galleries' => $galleries,
            'attributes' => $attributes,
            'reviews' => $reviews,
            'sec_name' => $sec_name,
            'sec_details' => $sec_details,
            'related_products' => $related_products,
            'related_items' => $related_products,
        ]);
    }

    // -------------------------------- TRACK ORDER ----------------------------------------

    public function trackOrder()
    {
        return view('front.track_order');
    }

    public function track(Request $request)
    {
        $order = Order::where('transaction_number', $request->order_number)->first();
        if ($order) {
            $track_orders = TrackOrder::where('order_id', $order->id)->orderby('id', 'desc')->get();
            return view('front.track_order', [
                'order' => $order,
                'track_orders' => $track_orders,
            ]);
        } else {
            return back()->with('error', __('Order Not Found'));
        }
    }
}
