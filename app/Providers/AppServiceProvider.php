<?php

namespace App\Providers;

use Illuminate\{
    Support\ServiceProvider,
    Support\Facades\DB
};
use Illuminate\Pagination\Paginator;
use App\Models\Setting;
use App\Models\PurchaseNotification;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Suppress deprecated warnings for Carbon library
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        Paginator::useBootstrap();
        view()->composer('*', function ($settings) {
            // Use Eloquent model instead of DB::table to get model methods
            $settings->with('setting', Setting::first());
            $settings->with('extra_settings', DB::table('extra_settings')->find(1));
            $settings->with('menus', DB::table('menus')->find(1));
            
            // Get active purchase notifications for popup
            try {
                $purchaseNotifications = PurchaseNotification::active()
                    ->with('item')
                    ->whereHas('item', function($query) {
                        $query->where('status', 1);
                    })
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('id', 'desc')
                    ->get();
                
                // Format notifications for JavaScript
                $formattedNotifications = $purchaseNotifications->map(function($notification) {
                    return [
                        'customer_name' => $notification->customer_name,
                        'minutes_ago' => $notification->minutes_ago,
                        'created_at' => $notification->created_at ? $notification->created_at->timestamp : time(),
                        'product_name' => $notification->item ? $notification->item->name : '',
                        'product_slug' => $notification->item ? $notification->item->slug : '',
                    ];
                })->values()->toArray();
                
                $settings->with('purchaseNotifications', $purchaseNotifications);
                $settings->with('purchaseNotificationsJson', json_encode($formattedNotifications));
                
                // Get popup interval and break interval from settings (default 2 seconds)
                $setting = Setting::first();
                $popupInterval = $setting && isset($setting->purchase_popup_interval) ? $setting->purchase_popup_interval : 2000;
                $breakInterval = $setting && isset($setting->purchase_popup_break_interval) ? $setting->purchase_popup_break_interval : 2000;
                $settings->with('purchasePopupInterval', $popupInterval);
                $settings->with('purchasePopupBreakInterval', $breakInterval);
            } catch (\Exception $e) {
                // If table doesn't exist yet, provide empty collection
                $settings->with('purchaseNotifications', collect());
                $settings->with('purchaseNotificationsJson', '[]');
                $settings->with('purchasePopupInterval', 2000);
                $settings->with('purchasePopupBreakInterval', 2000);
            }

            if (!session()->has('popup')) {
                view()->share('visit', 1);
            }
            session()->put('popup', 1);
        });
    }

    public function register() {}
}
