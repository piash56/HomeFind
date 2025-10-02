<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\EmailTemplate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $template = EmailTemplate::where('type', 'New Order Admin')->first();
        if ($template) {
            $template->body = '<h2 style="color: #333; font-family: Arial, sans-serif;">New Order Received!</h2>
<p style="color: #666; font-family: Arial, sans-serif;"><strong>Order Details:</strong></p>
<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; margin: 10px 0;">
    <tr style="background-color: #f8f9fa;">
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Transaction Number:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{transaction_number}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Customer Name:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{customer_name}</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Phone Number:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{customer_phone}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Delivery Address:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{customer_address}</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Product Name:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{product_name}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Quantity:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{quantity}</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Unit Price:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{unit_price}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Total Price:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold; color: #28a745;">{total_price}</td>
    </tr>
    <tr style="background-color: #f8f9fa;">
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Payment Method:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{payment_method}</td>
    </tr>
    <tr>
        <td style="border: 1px solid #ddd; padding: 8px;"><strong>Order Status:</strong></td>
        <td style="border: 1px solid #ddd; padding: 8px;">{order_status}</td>
    </tr>
</table>
<p style="color: #666; font-family: Arial, sans-serif; margin-top: 15px;"><strong>Bulk Pricing Information:</strong></p>
<p style="color: #333; font-family: Arial, sans-serif; background-color: #e9ecef; padding: 10px; border-left: 4px solid #007bff;">{bulk_pricing_info}</p>
<p style="color: #333; font-family: Arial, sans-serif; margin-top: 20px;">Please process this order accordingly.</p>
<p style="color: #666; font-family: Arial, sans-serif; margin-top: 20px;">Best regards,<br><strong>{site_title}</strong></p>';
            $template->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $template = EmailTemplate::where('type', 'New Order Admin')->first();
        if ($template) {
            $template->body = '<p>You Got a order, Transaction number {transaction_number}</p>';
            $template->save();
        }
    }
};
