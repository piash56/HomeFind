<?php

namespace App\Repositories\Front;

use App\{
    Models\Cart,
    Models\Item,
    Models\PromoCode,
    Helpers\PriceHelper
};
use App\Models\AttributeOption;
use App\Models\Attribute;
use Illuminate\Support\Facades\Session;

class CartRepository
{

    /**
     * Store cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store($request)
    {

        if (empty($request->all())) {
            $parsedUrl = parse_url($request->getRequestUri(), PHP_URL_QUERY); // Extracts the query part
            parse_str($parsedUrl, $queryArray);
            $request = (object)$queryArray;
            $qty_check  = 0;
            $input = $queryArray;
        } else {
            $input = $request->all();
        }

        $qty_check  = 0;

        $input['option_name'] = [];
        $input['option_price'] = [];
        $input['attr_name'] = [];

        $qty = isset($input['quantity']) ? $input['quantity'] : 1;



        $qty = is_numeric($qty) ? $qty : 1;


        if ($input['options_ids']) {
            foreach (explode(',', $input['options_ids']) as $optionId) {
                $option = AttributeOption::findOrFail($optionId);
                if ($qty > $option->stock) {
                    $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                    return $data;
                }
            }
        }

        $cart = Session::get('cart');

        $item = Item::where('id', $input['item_id'])->select('id', 'name', 'photo', 'discount_price', 'previous_price', 'slug', 'item_type', 'license_name', 'license_key', 'stock')->first();

        if ($item->item_type == 'normal') {
            if ($item->stock < $request->quantity) {
                $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                return $data;
            }
        }



        $single = isset($request->type) ? ($request->type == '1' ? 1 : 0) : 0;

        if (Session::has('cart')) {
            if ($item->item_type == 'digital' || $item->item_type == 'license') {
                $check = array_key_exists($input['item_id'], Session::get('cart'));

                if ($check) {
                    $data = ['message' => 'Product already added', 'status' => 'alreadyInCart'];
                    return $data;
                } else {
                    if (array_key_exists($input['item_id'] . '-', Session::get('cart'))) {

                        $data = ['message' => 'Product already added', 'status' => 'alreadyInCart'];
                        return $data;
                    }
                }
            }
        }

        $option_id = [];

        if ($single == 1) {
            $attr_name = [];
            $option_name = [];
            $option_price = [];

            if (count($item->attributes) > 0) {
                foreach ($item->attributes as $attr) {
                    if (isset($attr->options[0]->name)) {
                        $attr_name[] = $attr->name;
                        $option_name[] = $attr->options[0]->name;
                        $option_price[] = $attr->options[0]->price;
                        $option_id[] = $attr->options[0]->id;
                    }
                }
            }

            $input['attr_name'] = $attr_name;
            $input['option_price'] = $option_price;
            $input['option_name'] = $option_name;
            $input['option_id'] = $option_id;

            if ($request->quantity != 'NaN') {
                $qty = $request->quantity;
                $qty_check = 1;
            } else {
                $qty = 1;
            }
        } else {


            if ($input['attribute_ids']) {
                foreach (explode(',', $input['attribute_ids']) as $attrId) {
                    $attr = Attribute::findOrFail($attrId);
                    $attr_name[] = $attr->name;
                }
                $input['attr_name'] = $attr_name;
            }

            if ($input['options_ids']) {
                foreach (explode(',', $input['options_ids']) as $optionId) {
                    $option = AttributeOption::findOrFail($optionId);
                    $option_name[] = $option->name;
                    $option_price[] = $option->price;
                    $option_id[] = $option->id;
                }
                $input['option_name'] = $option_name;
                $input['option_price'] = $option_price;
            }
        }




        if (!$item) {
            abort(404);
        }


        $option_price = array_sum($input['option_price']);
        $attribute['names'] = $input['attr_name'];
        $attribute['option_name'] = $input['option_name'];

        if (isset($request->item_key) && $request->item_key != (int) 0) {
            $cart_item_key = explode('-', $request->item_key)[1];
        } else {
            $cart_item_key = str_replace(' ', '', implode(',', $attribute['option_name']));
        }

        $attribute['option_price'] = $input['option_price'];
        $cart = Session::get('cart');
        // if cart is empty then this the first product
        if (!$cart || !isset($cart[$item->id . '-' . $cart_item_key])) {
            $license_name = json_decode($item->license_name, true);
            $license_key = json_decode($item->license_name, true);
            $cart[$item->id . '-' . $cart_item_key] = [
                'options_id' => $option_id,
                'attribute' => $attribute,
                'attribute_price' => $option_price,
                "name" => $item->name,
                "slug" => $item->slug,
                "qty" => $qty,
                "price" => PriceHelper::grandPrice($item),
                "main_price" => $item->discount_price,
                "previous_price" => $item->previous_price ?? 0,
                "photo" => $item->photo,
                "type" => $item->item_type,
                "item_type" => $item->item_type,
                'item_l_n' => $item->item_type == 'license' ? end($license_name) : null,
                'item_l_k' => $item->item_type == 'license' ? end($license_key) : null
            ];

            Session::put('cart', $cart);


            $coupon = Session::get('coupon');

            if ($coupon) {
                $promo_code = (object)$coupon['code'];

                $cart = Session::get('cart');
                $cartTotal = PriceHelper::cartTotal($cart, 2);
                $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

                $coupon = [
                    'discount' => $discount['sub'],
                    'code'  => $promo_code
                ];
                Session::put('coupon', $coupon);
            }

            $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
            return $mgs;
        }


        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$item->id . '-' . $cart_item_key])) {

            $cart = Session::get('cart');

            if ($qty_check == 1) {
                $cart[$item->id . '-' . $cart_item_key]['qty'] =  $qty;
            } else {
                $cart[$item->id . '-' . $cart_item_key]['qty'] +=  $qty;
            }

            if ($item->item_type == 'normal') {

                if ($item->stock <= (int)$cart[$item->id . '-' . $cart_item_key]['qty']) {
                    $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                    return $data;
                }
            }


            Session::put('cart', $cart);

            $coupon = Session::get('coupon');

            if ($coupon) {
                $promo_code = (object)$coupon['code'];

                $cart = Session::get('cart');
                $cartTotal = PriceHelper::cartTotal($cart, 2);
                $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

                $coupon = [
                    'discount' => $discount['sub'],
                    'code'  => $promo_code
                ];
                Session::put('coupon', $coupon);
            }



            if ($qty_check == 1) {
                $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
            } else {
                $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
            }

            $qty_check = 0;
            return $mgs;
        }

        $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
        return $mgs;
    }

    public function promoStore($request)
    {
        $input = $request->all();
        
        // First check if coupon code exists at all
        $code_exists = PromoCode::where('code_name', $input['code'])->first();
        
        if (!$code_exists) {
            return [
                'status'  => false,
                'message' => __('Invalid coupon code')
            ];
        }
        
        // Check if coupon is enabled
        if ($code_exists->status != 1) {
            return [
                'status'  => false,
                'message' => __('This coupon code is currently disabled')
            ];
        }
        
        // Check if coupon has uses remaining
        if ($code_exists->no_of_times <= 0) {
            return [
                'status'  => false,
                'message' => __('This coupon code has been fully used')
            ];
        }
        
        $promo_code = $code_exists;
        
        // Check if promo code is within valid date range
        if (!$promo_code->isValidDate()) {
            return [
                'status'  => false,
                'message' => __('This coupon code has expired or is not yet active')
            ];
        }
        
        // Check if this is a single product page request
        // If product_id is provided in request, treat as single product validation
        if ($request->has('product_id')) {
            $productId = $request->product_id;
            
            // If coupon is product-specific, validate it
            if ($promo_code->product_id && $promo_code->product_id != $productId) {
                $productName = $promo_code->product ? $promo_code->product->name : 'specific product';
                return [
                    'status'  => false,
                    'message' => __('This coupon is only valid for') . ': ' . $productName
                ];
            }
            
            // Get product details
            $product = Item::find($productId);
            if (!$product) {
                return [
                    'status'  => false,
                    'message' => __('Product not found')
                ];
            }
            
            // Calculate discount based on product price (single unit)
            $productPrice = $product->discount_price;
            $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $productPrice);
            
            // Return discount info without saving to session (will be applied at checkout)
            return [
                'status'  => true,
                'success' => true,
                'discount' => $discount['sub'],
                'title' => $promo_code->title,
                'code' => $promo_code->code_name,
                'message' => __('Coupon applied successfully')
            ];
        }
        
        // Cart-based coupon application
        $cart = Session::get('cart');
        
        // If promo code is for a specific product, check if that product is in cart
        if ($promo_code->product_id) {
            $productInCart = false;
            $productPrice = 0;
            
            foreach ($cart as $key => $item) {
                $itemId = explode('-', $key, 2)[0];
                if ($itemId == $promo_code->product_id) {
                    $productInCart = true;
                    // Calculate product total (price + attributes) * quantity
                    $productPrice = ($item['main_price'] + $item['attribute_price']) * $item['qty'];
                    break;
                }
            }
            
            if (!$productInCart) {
                $productName = $promo_code->product ? $promo_code->product->name : 'specific product';
                return [
                    'status'  => false,
                    'message' => __('This coupon is only valid for') . ': ' . $productName
                ];
            }
            
            // Calculate discount based on specific product price
            $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $productPrice);
        } else {
            // Apply to entire cart
            $cartTotal = PriceHelper::cartTotal($cart, 2);
            $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);
        }

        $coupon = [
            'discount' => $discount['sub'],
            'code'  => $promo_code
        ];
        Session::put('coupon', $coupon);

        return [
            'status'  => true,
            'message' => __('Promo code applied successfully!')
        ];
    }



    public function getCart()
    {
        $cart = Session::has('cart') ? Session::get('cart') : null;
        return $cart;
    }

    public function getDiscount($discount, $type, $price)
    {
        if ($type == 'amount') {
            $sub = $discount;
            $total = $price - $sub;
        } else {
            $val = $price / 100;
            $sub = $val * $discount;
            $total = $price - $sub;
        }

        return [
            'sub' => $sub,
            'total' => $total
        ];
    }
}
