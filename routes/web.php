<?php

// ************************************ ADMIN PANEL **********************************************

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['adminlocalize', 'demo']], function () {
    Route::prefix('admin')->group(function () {
        //------------ SYSTEM: CACHE CLEAR ------------
        Route::get('/cache/clear', function () {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            return redirect()->route('back.dashboard')->withSuccess(__('System Cache Has Been Removed.'));
        })->name('back.cache.clear');
        //------------ AUTH ------------
        Route::get('/login', 'Auth\Back\LoginController@showForm')->name('back.login');
        Route::post('/login-submit', 'Auth\Back\LoginController@login')->name('back.login.submit');
        Route::get('/logout', 'Auth\Back\LoginController@logout')->name('back.logout');

        //------------ FORGOT ------------
        Route::get('/forgot', 'Auth\Back\ForgotController@showForm')->name('back.forgot');
        Route::post('/forgot-submit', 'Auth\Back\ForgotController@forgot')->name('back.forgot.submit');
        Route::get('/change-password/{token}', 'Auth\Back\ForgotController@showChangePassForm')->name('back.change.token');
        Route::post('/change-password-submit', 'Auth\Back\ForgotController@changepass')->name('back.change.password');

        //------------ DASHBOARD & PROFILE ------------
        Route::get('/', 'Back\AccountController@index')->name('back.dashboard');
        Route::get('/profile', 'Back\AccountController@profileForm')->name('back.profile');
        // General Settings (title, tagline, logo)
        Route::get('/setting/general', 'Back\SettingController@system')->name('back.setting.general');
        Route::post('/setting/update', 'Back\SettingController@update')->name('back.setting.update');
        Route::post('/profile/update', 'Back\AccountController@updateProfile')->name('back.profile.update');
        Route::get('/password', 'Back\AccountController@passwordResetForm')->name('back.password');
        Route::post('/password/update', 'Back\AccountController@updatePassword')->name('back.password.update');

        Route::get('bulk/deletes', 'Back\BulkDeleteController@bulkDelete')->name('back.bulk.delete');


        Route::group(['middleware' => 'permissions:Manage Orders'], function () {
            //------------ ORDER ------------
            Route::get('orders', 'Back\OrderController@index')->name('back.order.index');
            Route::delete('/order/delete/{id}', 'Back\OrderController@delete')->name('back.order.delete');
            Route::get('/order/edit/{id}', 'Back\OrderController@edit')->name('back.order.edit');
            Route::post('/order/update/{id}', 'Back\OrderController@update')->name('back.order.update');
            Route::get('/order/print/{id}', 'Back\OrderController@printOrder')->name('back.order.print');
            Route::get('/order/invoice/{id}', 'Back\OrderController@invoice')->name('back.order.invoice');
            Route::get('/order/status/{id}/{field}/{value}', 'Back\OrderController@status')->name('back.order.status');
            Route::get('/order/steadfast-parcel/{id}', 'Back\OrderController@createSteadFastParcel')->name('back.order.steadfast.parcel');
            Route::get('/order/get-product-data', 'Back\OrderController@getProductData')->name('admin.order.get-product-data');
        });

        Route::group(['middleware' => 'permissions:Manage Products'], function () {

            //------------ ITEM ------------

            Route::get('item/add', 'Back\ItemController@add')->name('back.item.add');
            Route::get('item/status/{item}/{status}', 'Back\ItemController@status')->name('back.item.status');
            Route::get('stock/out/product', 'Back\ItemController@stockOut')->name('back.item.stock.out');
            Route::resource('item', 'Back\ItemController', ['as' => 'back', 'except' => 'show', 'getsubCategory']);

            //------------ REVIEW ------------
            Route::get('reviews', '\App\Http\Controllers\ReviewController@adminIndex')->name('admin.review.index');
            Route::get('reviews/create', '\App\Http\Controllers\ReviewController@adminCreate')->name('admin.review.create');
            Route::post('reviews', '\App\Http\Controllers\ReviewController@adminStore')->name('admin.review.store');
            Route::get('reviews/search-products', '\App\Http\Controllers\ReviewController@searchProducts')->name('admin.review.search-products');
            Route::get('reviews/{id}', '\App\Http\Controllers\ReviewController@adminShow')->name('admin.review.show');
            Route::get('reviews/{id}/edit', '\App\Http\Controllers\ReviewController@adminEdit')->name('admin.review.edit');
            Route::put('reviews/{id}', '\App\Http\Controllers\ReviewController@adminUpdate')->name('admin.review.update');
            Route::delete('reviews/{id}', '\App\Http\Controllers\ReviewController@adminDestroy')->name('admin.review.destroy');
            Route::post('reviews/reply', '\App\Http\Controllers\ReviewController@adminReply')->name('admin.review.reply');
            Route::post('reviews/{id}/remove-image', '\App\Http\Controllers\ReviewController@removeImage')->name('admin.review.remove-image');
            Route::get('item/highlight/{item}', 'Back\ItemController@highlight')->name('back.item.highlight');
            Route::post('item/highlight/update/{item}', 'Back\ItemController@highlight_update')->name('back.item.highlight.update');
            Route::get('item/galleries/{item}', 'Back\ItemController@galleries')->name('back.item.gallery');
            Route::post('item/galleries/update', 'Back\ItemController@galleriesUpdate')->name('back.item.galleries.update');
            Route::delete('item/gallery/{gallery}/delete', 'Back\ItemController@galleryDelete')->name('back.item.gallery.delete');

            // Bulk product upload
            Route::get('/product/csv/export', 'Back\CsvProductController@export')->name('back.csv.export');
            Route::get('bulk/product/index', 'Back\CsvProductController@index')->name('back.bulk.product.index');
            Route::post('csv/import', 'Back\CsvProductController@import')->name('back.csv.import');
            Route::get('order/csv/export', 'Back\CsvProductController@orderExport')->name('back.csv.order.export');

            // summernote image upload 
            Route::post('/summernote/image/upload', 'Back\ItemController@summernoteUpload')->name('back.summernote.image.upload');


            // --------- DIGITAL PRODUCT -----------//
            Route::get('/digital/create', 'Back\ItemController@deigitalItemCreate')->name('back.digital.item.create');
            Route::post('/digital/store', 'Back\ItemController@deigitalItemStore')->name('back.digital.item.store');
            Route::get('/digital/edit/{id}', 'Back\ItemController@deigitalItemEdit')->name('back.digital.item.edit');

            // --------- LICENSE PRODUCT -----------//
            Route::get('/license/create', 'Back\ItemController@licenseItemCreate')->name('back.license.item.create');
            Route::post('/license/store', 'Back\ItemController@licenseItemStore')->name('back.license.item.store');
            Route::get('/license/edit/{id}', 'Back\ItemController@licenseItemEdit')->name('back.license.item.edit');

            // ----------- AFFILIATE PRODUCT -----------//
            Route::resource('affiliate', 'Back\AffiliateController', ['as' => 'back']);
            // ----------- AFFILIATE PRODUCT -----------//



            Route::prefix('{item}')->group(function () {
                //------------ ATTRIBUTE ------------
                Route::resource('attribute', 'Back\AttributeController', ['as' => 'back', 'except' => 'show']);
                //------------ ATTRIBUTE OPTION ------------
                Route::resource('option', 'Back\AttributeOptionController', ['as' => 'back', 'except' => 'show']);
            });



            //------------ BRAND ------------
            Route::get('brand/status/{id}/{status}/{type}', 'Back\BrandController@status')->name('back.brand.status');
            Route::resource('brand', 'Back\BrandController', ['as' => 'back', 'except' => 'show']);

            //------------ OLD REVIEW ROUTES REMOVED ----------------//
            // These routes were moved to the new admin.review routes above
        });

        //------------ NOTIFICATIONS ------------
        Route::get('/notifications', 'Back\NotificationController@notifications')->name('back.notifications');
        Route::get('/notifications/view', 'Back\NotificationController@view_notification')->name('back.view.notification');
        Route::get('/notification/delete/{id}', 'Back\NotificationController@delete')->name('back.notification.delete');
        Route::get('/notifications/clear', 'Back\NotificationController@clear_notf')->name('back.notifications.clear');


        Route::group(['middleware' => 'permissions:Manage Categories'], function () {
            //------------ CATEGORY ------------
            Route::get('category/status/{id}/{status}', 'Back\CategoryController@status')->name('back.category.status');
            Route::get('category/feature/{id}/{status}', 'Back\CategoryController@feature')->name('back.category.feature');
            Route::resource('category', 'Back\CategoryController', ['as' => 'back', 'except' => 'show']);
        });



        Route::group(['middleware' => 'permissions:Ecommerce'], function () {
            //------------ PROMO CODE ------------
            Route::get('code/status/{id}/{status}', 'Back\PromoCodeController@status')->name('back.code.status');
            Route::resource('code', 'Back\PromoCodeController', ['as' => 'back', 'except' => 'show']);
        });






        Route::group(['middleware' => 'permissions:Manage System User'], function () {

            //------------ ROLE ------------
            Route::resource('role', 'Back\RoleController', ['as' => 'back', 'except' => 'show']);

            //------------ STAFF ------------
            Route::resource('staff', 'Back\StaffController', ['as' => 'back', 'except' => 'show']);
        });


        Route::group(['middleware' => 'permissions:Manage Site'], function () {


            //------------ FEATURE ------------
            Route::get('feature/image', 'Back\FeatureController@featureImage')->name('back.feature.image');
            Route::resource('feature', 'Back\FeatureController', ['as' => 'back', 'except' => 'show']);

            //------------ SETTING ------------
            Route::get('/setting/menu', 'Back\SettingController@menu')->name('back.setting.menu');
            Route::post('/setting/update/visiable', 'Back\SettingController@visiable')->name('back.setting.visible.update');
            Route::get('/maintainance', 'Back\SettingController@maintainance')->name('back.setting.maintainance');

            // ------ Menu Builder 
            Route::get('/menu', 'Back\MenuController@index')->name('back.menu.index');
            Route::post('/menu/update', 'Back\MenuController@update')->name('back.menu.update');

            //   Home Page Customizations
            Route::get('home-page', 'Back\HomePageController@index')->name('back.homePage');
            Route::post('home-page/hero/banner/update', 'Back\HomePageController@hero_banner_update')->name('back.hero.banner.update');
            Route::post('home-page/first/banner/update', 'Back\HomePageController@first_banner_update')->name('back.first.banner.update');
            Route::post('home-page/secend/banner/update', 'Back\HomePageController@secend_banner_update')->name('back.secend.banner.update');
            Route::post('home-page/third/banner/update', 'Back\HomePageController@third_banner_update')->name('back.third.banner.update');
            Route::post('home-page/popular/category/update', 'Back\HomePageController@popular_category_update')->name('back.popular.category.update');
            Route::post('home-page/tree/cloumn/category/update', 'Back\HomePageController@tree_column_category_update')->name('back.tree.column.category.update');
            Route::post('home-page/feature/category/category/update', 'Back\HomePageController@feature_category_update')->name('back.feature.category.update');
            Route::post('home-page4/banner/update', 'Back\HomePageController@homepage4update')->name('back.home_page4.banner.update');
            Route::post('home-page4/category/update', 'Back\HomePageController@homepage4categoryupdate')->name('back.home4.category.update');


            //------------ EMAIL TEMPLATE ------------
            Route::get('/setting/email', 'Back\EmailSettingController@email')->name('back.setting.email');
            Route::post('/setting/email/update', 'Back\EmailSettingController@emailUpdate')->name('back.email.update');
            Route::post('/setting/email/test', 'Back\EmailSettingController@testEmail')->name('back.email.test');
            Route::get('email/template/{template}/edit', 'Back\EmailSettingController@edit')->name('back.template.edit');
            Route::put('email/template/update/{template}', 'Back\EmailSettingController@update')->name('back.template.update');

            // ----------- SMS SETTING ---------------//
            Route::get('/setting/configuration/sms', 'Back\SmsSettingController@sms')->name('back.setting.sms');
            Route::get('/setting/cta', 'Back\CtaController@index')->name('back.setting.cta');
            Route::post('/setting/cta/update', 'Back\CtaController@update')->name('back.setting.cta.update');
            Route::post('/setting/sms/update', 'Back\SmsSettingController@smsUpdate')->name('back.sms.update');
            // ----------- SMS SETTING ---------------//

            //------------ LANGUAGE SETTING ------------
            Route::resource('language', 'Back\LanguageController', ['as' => 'back']);
            Route::get('language/status/{id}/{status}', 'Back\LanguageController@status')->name('back.language.status');

            //------------ SLIDER ------------
            Route::resource('slider', 'Back\SliderController', ['as' => 'back', 'except' => 'show']);

            //------------ SERVICE ------------
            Route::resource('service', 'Back\ServiceController', ['as' => 'back', 'except' => 'show']);


            // --------- Genarate Sitemap _______
            Route::get('/sitemap', 'Back\SitemapController@index')->name('admin.sitemap.index');
            Route::get('/sitemap/add', 'Back\SitemapController@add')->name('admin.sitemap.add');
            Route::post('/sitemap/store', 'Back\SitemapController@store')->name('admin.sitemap.store');
            Route::delete('/sitemap/delete/{id}/', 'Back\SitemapController@delete')->name('admin.sitemap.delete');
            Route::post('/sitemap/download', 'Back\SitemapController@download')->name('admin.sitemap.download');
        });
    });
});


// ************************************ ADMIN PANEL ENDS**********************************************



// ************************************ GLOBAL LOCALIZATION **********************************************

Route::group(['middleware' => 'maintainance'], function () {
    Route::group(['middleware' => 'localize'], function () {

        // ************************************ USER PANEL **********************************************

        Route::prefix('user')->group(function () {

            //------------ AUTH ------------
            Route::get('/verify', 'Auth\User\LoginController@showVerifyForm')->name('user.verify');
            Route::post('/email/verify/submit/asdfasdf', 'Auth\User\LoginController@verifySubmit')->name('user.verify.submit');
            Route::get('/login', 'Auth\User\LoginController@showForm')->name('user.login');
            Route::post('/login-submit', 'Auth\User\LoginController@login')->name('user.login.submit');
            Route::get('/logout', 'Auth\User\LoginController@logout')->name('user.logout');
            Route::get('/remove/account', 'User\AccountController@removeAccount')->name('user.account.remove');

            //------------ REGISTER ------------
            Route::get('/register', 'Auth\User\RegisterController@showForm')->name('user.register');
            Route::post('/register-submit', 'Auth\User\RegisterController@register')->name('user.register.submit');
            Route::get('/verify-link/{token}', 'Auth\User\RegisterController@verify')->name('user.account.verify');

            //------------ FORGOT ------------
            Route::get('/forgot', 'Auth\User\ForgotController@showForm')->name('user.forgot');
            Route::post('/forgot-submit', 'Auth\User\ForgotController@forgot')->name('user.forgot.submit');
            Route::get('/change-password/{token}', 'Auth\User\ForgotController@showChangePassForm')->name('user.change.token');
            Route::post('/change-password-submit', 'Auth\User\ForgotController@changepass')->name('user.change.password');



            //------------ DASHBOARD ------------
            Route::get('/dashboard', 'User\AccountController@index')->name('user.dashboard');
            Route::get('/profile', 'User\AccountController@profile')->name('user.profile');


            //------------ SETTING ------------
            Route::post('/profile/update', 'User\AccountController@profileUpdate')->name('user.profile.update');
            Route::get('/addresses', 'User\AccountController@addresses')->name('user.address');
            Route::post('/billing/addresses', 'User\AccountController@billingSubmit')->name('user.billing.submit');
            Route::post('/shipping/addresses', 'User\AccountController@shippingSubmit')->name('user.shipping.submit');

            //------------ ORDER ------------
            Route::get('/orders', 'User\OrderController@index')->name('user.order.index');
            Route::get('/order/print/{id}', 'User\OrderController@printOrder')->name('user.order.print');
            Route::get('/order/invoice/{id}', 'User\OrderController@details')->name('user.order.invoice');
            //------------ WISHLIST ------------
            Route::get('/wishlists', 'User\WishlistController@index')->name('user.wishlist.index');
            Route::get('/wishlist/store/{id}', 'User\WishlistController@store')->name('user.wishlist.store');
            Route::get('/wishlist/delete/{id}', 'User\WishlistController@delete')->name('user.wishlist.delete');
            Route::get('/wishlista/delete/all', 'User\WishlistController@alldelete')->name('user.wishlist.delete.all');
        });


        Route::get('auth/{provider}', 'User\SocialLoginController@redirectToProvider')->name('social.provider');
        Route::get('auth/{provider}/callback', 'User\SocialLoginController@handleProviderCallback');

        // ************************************ USER PANEL ENDS**********************************************



        // ************************************ FRONTEND **********************************************

        //------------ FRONT ------------
        // Redirect home page to a simple landing or product catalog
        Route::get('/', 'Front\CatalogController@index')->name('front.index');
        Route::get('/product/{slug}', 'Front\FrontendController@product')->name('front.product');



        //------------ DIRECT ORDER ------------
        Route::post('/order/place-direct', 'Front\CheckoutController@placeDirectOrder')->name('front.order.direct');

        // Email test route (remove this after debugging)
        Route::get('/test-email', function () {
            try {
                $emailHelper = new \App\Helpers\EmailHelper();
                $result = $emailHelper->testEmail();
                return response()->json($result);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        });
        Route::get('/checkout/success', 'Front\CheckoutController@paymentSuccess')->name('front.checkout.success');

        //------------ REVIEW ------------
        Route::post('/review/verify-order', 'ReviewController@verifyOrder')->name('front.review.verify');
        Route::post('/review/submit', 'ReviewController@submitReview')->name('front.review.submit');
        Route::get('/review/get/{item_id}', 'ReviewController@getReviews')->name('front.review.get');

        //------------ CATCH-ALL ROUTE ------------
        // Redirect any other URL to the root (shop page)
        Route::any('{any}', function () {
            return redirect()->route('front.index');
        })->where('any', '.*');

        // Removed front.cache.clear; admin uses back.cache.clear


        // ************************************ FRONTEND ENDS**********************************************

        // ************************************ GLOBAL LOCALIZATION ENDS **********************************************

    });
});


// run queue word route after finish all task then stop
Route::get('/run/queue', function () {
    Artisan::call('queue:work --stop-when-empty');
    return "Queue is running";
});
