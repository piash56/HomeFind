<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Repositories\Front\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    protected $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    /**
     * Cart page.
     */
    public function index()
    {
        $this->enrichCartWithPreviousPrice();
        return view('front.cart.index');
    }

    /**
     * Ensure each cart item has previous_price (for old/cut price display).
     */
    private function enrichCartWithPreviousPrice(): void
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return;
        }
        foreach ($cart as $key => $item) {
            // Only enrich if previous_price is not set AND there are no options (plain product)
            if ((!isset($item['previous_price']) || $item['previous_price'] === null) && empty($item['options_id'])) {
                $itemId = explode('-', $key, 2)[0];
                $product = Item::where('id', $itemId)->select('previous_price')->first();
                if ($product) {
                    $cart[$key]['previous_price'] = $product->previous_price ?? 0;
                }
            }
        }
        Session::put('cart', $cart);
    }

    /**
     * Add product to cart (GET, used by AJAX from product page and listing).
     */
    public function add(Request $request)
    {
        $result = $this->cartRepository->store($request);
        if (is_array($result) && isset($result['status']) && in_array($result['status'], ['outStock', 'alreadyInCart'])) {
            return response()->json($result);
        }
        return response()->json($result);
    }

    /**
     * Clear entire cart.
     */
    public function clear()
    {
        Session::forget('cart');
        Session::forget('coupon');
        return redirect()->route('front.cart');
    }

    /**
     * Remove one item from cart.
     */
    public function destroy($key)
    {
        $cart = Session::get('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            Session::put('cart', $cart);
            if (empty($cart)) {
                Session::forget('coupon');
            }
        }
        return redirect()->back();
    }

    /**
     * Return cart HTML for AJAX refresh (header or cart table).
     */
    public function getLoad()
    {
        $this->enrichCartWithPreviousPrice();
        return view('includes.cart');
    }

    /**
     * Apply promo code (POST).
     */
    public function promoSubmit(Request $request)
    {
        $result = $this->cartRepository->promoStore($request);
        return response()->json($result);
    }

    /**
     * Remove promo code.
     */
    public function promoDestroy()
    {
        Session::forget('coupon');
        return redirect()->back();
    }
}
