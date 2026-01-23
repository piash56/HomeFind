<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\PurchaseNotification;
use App\Models\Item;
use Illuminate\Http\Request;

class PurchaseNotificationController extends Controller
{
    /**
     * Constructor Method.
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notifications = PurchaseNotification::with('item')->orderBy('sort_order', 'asc')->orderBy('id', 'desc')->get();
        $setting = \App\Models\Setting::first();
        $popupInterval = $setting && isset($setting->purchase_popup_interval) ? $setting->purchase_popup_interval : 2000;
        $breakInterval = $setting && isset($setting->purchase_popup_break_interval) ? $setting->purchase_popup_break_interval : 2000;
        return view('back.purchase-notification.index', compact('notifications', 'popupInterval', 'breakInterval'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = Item::where('status', 1)->orderBy('name', 'asc')->get();
        return view('back.purchase-notification.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'minutes_ago' => 'required|integer|min:0|max:999',
            'item_id' => 'required|exists:items,id',
            'status' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        PurchaseNotification::create([
            'customer_name' => $request->customer_name,
            'minutes_ago' => $request->minutes_ago,
            'item_id' => $request->item_id,
            'status' => $request->has('status') ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('back.purchase-notification.index')->withSuccess(__('Purchase Notification Added Successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseNotification $purchaseNotification)
    {
        $items = Item::where('status', 1)->orderBy('name', 'asc')->get();
        return view('back.purchase-notification.edit', compact('purchaseNotification', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseNotification $purchaseNotification)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'minutes_ago' => 'required|integer|min:0|max:999',
            'item_id' => 'required|exists:items,id',
            'status' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $purchaseNotification->update([
            'customer_name' => $request->customer_name,
            'minutes_ago' => $request->minutes_ago,
            'item_id' => $request->item_id,
            'status' => $request->has('status') ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('back.purchase-notification.index')->withSuccess(__('Purchase Notification Updated Successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseNotification $purchaseNotification)
    {
        $purchaseNotification->delete();
        return redirect()->route('back.purchase-notification.index')->withSuccess(__('Purchase Notification Deleted Successfully.'));
    }

    /**
     * Change the status.
     */
    public function status($id, $status)
    {
        PurchaseNotification::find($id)->update(['status' => $status]);
        return redirect()->route('back.purchase-notification.index')->withSuccess(__('Status Updated Successfully.'));
    }

    /**
     * Update popup interval setting.
     */
    public function updateInterval(Request $request)
    {
        $request->validate([
            'purchase_popup_interval' => 'required|integer|min:1000|max:60000',
            'purchase_popup_break_interval' => 'required|integer|min:1000|max:60000',
        ]);

        $setting = \App\Models\Setting::first();
        if ($setting) {
            $setting->purchase_popup_interval = $request->purchase_popup_interval;
            $setting->purchase_popup_break_interval = $request->purchase_popup_break_interval;
            $setting->save();
        }

        return redirect()->route('back.purchase-notification.index')->withSuccess(__('Popup Settings Updated Successfully.'));
    }
}
