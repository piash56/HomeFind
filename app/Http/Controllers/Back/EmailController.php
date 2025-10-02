<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('back.setting.email', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'smtp_check' => 'boolean',
            'email_host' => 'required_if:smtp_check,1|string|max:255',
            'email_port' => 'required_if:smtp_check,1|string|max:10',
            'email_encryption' => 'required_if:smtp_check,1|string|in:tls,ssl',
            'email_user' => 'required_if:smtp_check,1|string|max:255',
            'email_pass' => 'required_if:smtp_check,1|string|max:255',
            'email_from' => 'required_if:smtp_check,1|email|max:255',
            'email_from_name' => 'required_if:smtp_check,1|string|max:255',
            'contact_email' => 'required|email|max:255',
            'order_mail' => 'boolean',
        ]);

        $setting = Setting::first();
        
        $setting->update([
            'smtp_check' => $request->has('smtp_check') ? 1 : 0,
            'email_host' => $request->email_host,
            'email_port' => $request->email_port,
            'email_encryption' => $request->email_encryption,
            'email_user' => $request->email_user,
            'email_pass' => $request->email_pass,
            'email_from' => $request->email_from,
            'email_from_name' => $request->email_from_name,
            'contact_email' => $request->contact_email,
            'order_mail' => $request->has('order_mail') ? 1 : 0,
        ]);

        return redirect()->back()->with('success', 'Email settings updated successfully!');
    }

    public function test()
    {
        try {
            $setting = Setting::first();
            
            if (!$setting->smtp_check) {
                return response()->json(['success' => false, 'message' => 'SMTP is not enabled']);
            }

            $helper = new \App\Helpers\EmailHelper();
            
            $emailData = [
                'to' => $setting->contact_email,
                'subject' => 'Email Test - HomeFindBD',
                'body' => '<h2>Email Test Successful!</h2><p>Your email configuration is working correctly.</p><p>This is a test email sent from HomeFindBD.com</p>',
            ];

            $helper->sendCustomMail($emailData);
            
            return response()->json(['success' => true, 'message' => 'Test email sent successfully!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Test email failed: ' . $e->getMessage()]);
        }
    }
}
