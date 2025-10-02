<?php

/**
 * Created by UniverseCode.
 */

namespace App\Helpers;

use App\{
    Models\EmailTemplate,
    Models\Setting
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\{
    PHPMailer,
    Exception
};

class EmailHelper
{

    public $mail;
    public $setting;

    public function __construct()
    {
        $this->setting = Setting::first();

        try {
            $this->mail = new PHPMailer(true);

            if ($this->setting->smtp_check == 1) {

                $this->mail->isSMTP();
                $this->mail->Host       = $this->setting->email_host;
                $this->mail->SMTPAuth   = true;
                $this->mail->Username   = $this->setting->email_user;
                $this->mail->Password   = $this->setting->email_pass;
                if ($this->setting->email_encryption == 'ssl') {
                    $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                } else {
                    $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
                $this->mail->Port           = $this->setting->email_port;
                $this->mail->CharSet        = 'UTF-8';
            }
        } catch (\Exception $e) {
            \Log::error('PHPMailer initialization failed: ' . $e->getMessage());
            $this->mail = null;
        }
    }

    public function sendTemplateMail(array $emailData)
    {
        $template = EmailTemplate::whereType($emailData['type'])->first();
        try {
            $email_body = preg_replace("/{user_name}/", $emailData['user_name'], $template->body);
            $email_body = preg_replace("/{order_cost}/", $emailData['order_cost'], $email_body);
            $email_body = preg_replace("/{transaction_number}/", $emailData['transaction_number'], $email_body);
            $email_body = preg_replace("/{site_title}/", $this->setting->title, $email_body);

            $this->mail->setFrom($this->setting->email_from, $this->setting->email_from_name);
            $this->mail->addAddress($emailData['to']);
            $this->mail->isHTML(true);
            $this->mail->Subject = $template->subject;
            $this->mail->Body = $email_body;
            $this->mail->send();
            if ($this->setting->order_mail == 1) {
                $this->adminMail($emailData);
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
        }

        return true;
    }

    public function sendCustomMail(array $emailData)
    {

        try {

            $this->mail->setFrom($this->setting->email_from, $this->setting->email_from_name);
            $this->mail->addAddress($emailData['to']);
            $this->mail->isHTML(true);
            $this->mail->Subject = $emailData['subject'];
            $this->mail->Body = $emailData['body'];

            $this->mail->send();
        } catch (Exception $e) {
            dd($e->getMessage());
        }

        return true;
    }


    public static function getEmail()
    {
        $user = Auth::user();
        if (isset($user)) {
            $email = $user->email;
        } else {
            $email = Session::get('billing_address')['bill_email'];
        }
        return $email;
    }


    public function adminMail(array $emailData)
    {
        try {
            // Check if PHPMailer is available
            if (!$this->mail) {
                \Log::error('PHPMailer not available, skipping email notification');
                return;
            }

            $template = EmailTemplate::whereType('New Order Admin')->first();

            // Replace all template variables
            $email_body = $template->body;
            $email_body = preg_replace("/{transaction_number}/", $emailData['transaction_number'] ?? '', $email_body);
            $email_body = preg_replace("/{customer_name}/", $emailData['customer_name'] ?? '', $email_body);
            $email_body = preg_replace("/{customer_phone}/", $emailData['customer_phone'] ?? '', $email_body);
            $email_body = preg_replace("/{customer_address}/", $emailData['customer_address'] ?? '', $email_body);
            $email_body = preg_replace("/{product_name}/", $emailData['product_name'] ?? '', $email_body);
            $email_body = preg_replace("/{quantity}/", $emailData['quantity'] ?? '', $email_body);
            $email_body = preg_replace("/{unit_price}/", $emailData['unit_price'] ?? '', $email_body);
            $email_body = preg_replace("/{total_price}/", $emailData['total_price'] ?? '', $email_body);
            $email_body = preg_replace("/{payment_method}/", $emailData['payment_method'] ?? '', $email_body);
            $email_body = preg_replace("/{order_status}/", $emailData['order_status'] ?? '', $email_body);
            $email_body = preg_replace("/{bulk_pricing_info}/", $emailData['bulk_pricing_info'] ?? '', $email_body);
            $email_body = preg_replace("/{site_title}/", $this->setting->title, $email_body);

            // Legacy support for old template variables
            $email_body = preg_replace("/{user_name}/", $emailData['customer_name'] ?? $emailData['user_name'] ?? '', $email_body);
            $email_body = preg_replace("/{order_cost}/", $emailData['total_price'] ?? $emailData['order_cost'] ?? '', $email_body);

            $this->mail->setFrom($this->setting->email_from, $this->setting->email_from_name);
            $this->mail->clearAddresses();
            $this->mail->addAddress($this->setting->contact_email);
            $this->mail->isHTML(true);
            $this->mail->Subject = $template->subject;
            $this->mail->Body = $email_body;

            $this->mail->send();
        } catch (\Throwable $th) {
            \Log::error('Admin email failed: ' . $th->getMessage());
            // Don't throw error to prevent order creation failure
        }
    }
}
