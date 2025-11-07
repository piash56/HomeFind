<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;
use App\Models\Item;
use App\Helpers\PriceHelper;

class ReviewController extends Controller
{
    // Verify order for review eligibility
    public function verifyOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'phone' => 'required|string',
            'item_id' => 'required|exists:items,id'
        ]);

        $order = Order::where('transaction_number', $request->order_id)
            ->where('order_status', 'delivered')
            ->first();

        // Check if phone number matches
        if ($order) {
            $shippingInfo = json_decode($order->shipping_info, true);
            if ($shippingInfo['ship_phone'] !== $request->phone) {
                $order = null;
            }
        }

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or not delivered yet. Only delivered orders can be reviewed.'
            ], 404);
        }

        // Check if order contains the item
        $cart = json_decode($order->cart, true);
        $itemFound = false;
        $shippingInfo = json_decode($order->shipping_info, true);
        $customerName = $shippingInfo['ship_first_name'] . ' ' . $shippingInfo['ship_last_name'];

        foreach ($cart as $itemId => $itemData) {
            if ($itemId == $request->item_id) {
                $itemFound = true;
                break;
            }
        }

        if (!$itemFound) {
            return response()->json([
                'success' => false,
                'message' => 'This item was not found in the specified order.'
            ], 404);
        }

        // Check if review already exists
        $existingReview = Review::where('order_id', $order->id)
            ->where('item_id', $request->item_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this item for this order.'
            ], 409);
        }

        return response()->json([
            'success' => true,
            'customer_name' => $customerName,
            'phone' => $request->phone,
            'order_id' => $request->order_id
        ]);
    }

    // Submit review
    public function submitReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'phone' => 'required|string',
            'item_id' => 'required|exists:items,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Verify order again
        $order = Order::where('transaction_number', $request->order_id)
            ->where('order_status', 'delivered')
            ->first();

        // Check if phone number matches
        if ($order) {
            $shippingInfo = json_decode($order->shipping_info, true);
            if ($shippingInfo['ship_phone'] !== $request->phone) {
                $order = null;
            }
        }

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order verification failed.'
            ], 404);
        }

        // Check if review already exists
        $existingReview = Review::where('order_id', $order->id)
            ->where('item_id', $request->item_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Review already exists for this order.'
            ], 409);
        }

        // Handle multiple image uploads
        $reviewImages = [];
        if ($request->hasFile('review_images')) {
            foreach ($request->file('review_images') as $file) {
                $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/reviews'), $name);
                $reviewImages[] = 'assets/images/reviews/' . $name;
            }
        }

        // Create review
        $shippingInfo = json_decode($order->shipping_info, true);
        $customerName = $shippingInfo['ship_first_name'] . ' ' . $shippingInfo['ship_last_name'];

        $review = Review::create([
            'item_id' => $request->item_id,
            'order_id' => $order->id,
            'customer_name' => $customerName,
            'customer_phone' => $request->phone,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'review_images' => json_encode($reviewImages),
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully! It will be published after admin approval.'
        ]);
    }

    // Get reviews for a product
    public function getReviews($item_id)
    {
        $reviews = Review::where('item_id', $item_id)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews);
    }

    // Admin methods
    public function adminIndex()
    {
        $reviews = Review::with(['item', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('back.review.index', compact('reviews'));
    }

    public function adminCreate()
    {
        return view('back.review.create');
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'order_id' => 'nullable|string',
            'item_id' => 'required|exists:items,id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'admin_reply' => 'nullable|string|max:1000'
        ]);

        $data = [
            'item_id' => $request->item_id,
            'order_id' => $request->order_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'status' => $request->status,
            'admin_reply' => $request->admin_reply,
            'is_admin_added' => true
        ];

        // Handle multiple image uploads
        if ($request->hasFile('review_images')) {
            $reviewImages = [];
            foreach ($request->file('review_images') as $file) {
                $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/reviews'), $name);
                $reviewImages[] = 'assets/images/reviews/' . $name;
            }
            $data['review_images'] = json_encode($reviewImages);
        }

        // Set admin reply date if admin reply is provided
        if ($request->admin_reply) {
            $data['admin_reply_date'] = now();
        }

        Review::create($data);

        return redirect()->route('admin.review.index')
            ->with('success', 'Review created successfully.');
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            // Show last 6 products when no search query
            $products = Item::select('id', 'name', 'thumbnail', 'photo')
                ->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        } else {
            // Search products by name
            $products = Item::select('id', 'name', 'thumbnail', 'photo')
                ->where('status', 1)
                ->where('name', 'like', '%' . $query . '%')
                ->limit(6)
                ->get();
        }

        // Transform thumbnail URLs
        $products->transform(function ($product) {
            // Use thumbnail if available, otherwise use photo
            $imagePath = $product->thumbnail ?: $product->photo;

            if ($imagePath) {
                // Check if path already includes 'storage/images/'
                if (strpos($imagePath, 'storage/images/') !== false) {
                    $product->thumbnail = asset('storage/images/' . basename($imagePath));
                } elseif (strpos($imagePath, 'assets/images/') === 0) {
                    $product->thumbnail = asset($imagePath);
                } else {
                    // Default to storage/images path
                    $product->thumbnail = asset('storage/images/' . $imagePath);
                }
            } else {
                $product->thumbnail = asset('storage/images/placeholder.png');
            }
            return $product;
        });

        return response()->json(['products' => $products]);
    }

    public function adminShow($id)
    {
        $review = Review::with(['item', 'order'])->findOrFail($id);
        return view('back.review.show', compact('review'));
    }

    public function adminEdit($id)
    {
        $review = Review::with(['item', 'order'])->findOrFail($id);
        return view('back.review.edit', compact('review'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,approved,rejected',
            'review_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'admin_reply' => 'nullable|string|max:1000',
            'review_date' => 'nullable|date',
            'admin_reply_date' => 'nullable|date'
        ]);

        $review = Review::findOrFail($id);

        $data = [
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'status' => $request->status,
            'admin_reply' => $request->admin_reply
        ];

        // Handle review date
        if ($request->filled('review_date')) {
            $data['created_at'] = $request->review_date;
        }

        // Handle admin reply date
        if ($request->filled('admin_reply_date')) {
            $data['admin_reply_date'] = $request->admin_reply_date;
        }

        // Handle multiple image uploads
        if ($request->hasFile('review_images')) {
            $existingImages = $review->getReviewImages();
            $newImages = [];

            foreach ($request->file('review_images') as $file) {
                $name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/reviews'), $name);
                $newImages[] = 'assets/images/reviews/' . $name;
            }

            // Combine existing and new images (max 3 total)
            $allImages = array_merge($existingImages, $newImages);
            if (count($allImages) > 3) {
                $allImages = array_slice($allImages, 0, 3);
            }

            $data['review_images'] = json_encode($allImages);
        }

        $review->update($data);

        return redirect()->route('admin.review.index')
            ->with('success', 'Review updated successfully.');
    }

    public function adminDestroy($id)
    {
        $review = Review::findOrFail($id);

        // Delete image if exists
        if ($review->review_image && file_exists(public_path($review->review_image))) {
            unlink(public_path($review->review_image));
        }

        $review->delete();

        return redirect()->route('admin.review.index')
            ->with('success', 'Review deleted successfully.');
    }

    // Admin reply to review
    public function adminReply(Request $request)
    {
        $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'admin_reply' => 'required|string|max:1000'
        ]);

        $review = Review::findOrFail($request->review_id);

        $updateData = [
            'admin_reply' => $request->admin_reply,
            'admin_reply_date' => now()
        ];

        // Only change status to approved if review is currently pending
        if ($review->status === 'pending') {
            $updateData['status'] = 'approved';
        }

        $review->update($updateData);

        $message = 'Reply submitted successfully.';
        if ($review->status === 'pending') {
            $message .= ' Review has been approved.';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // Remove image from review
    public function removeImage(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $imageIndex = $request->image_index;

        $images = $review->getReviewImages();
        if (isset($images[$imageIndex])) {
            // Delete file from storage
            $imagePath = public_path($images[$imageIndex]);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Remove from array
            unset($images[$imageIndex]);
            $images = array_values($images); // Re-index array

            // Update review
            $review->setReviewImages($images);
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Image removed successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image not found.'
        ]);
    }
}
