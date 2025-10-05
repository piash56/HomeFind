<?php

namespace App\Http\Controllers\Back;

use App\{
    Models\EmailTemplate,
    Http\Controllers\Controller,
};
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailSettingController extends Controller
{

    /**
     * Constructor Method.
     */
    public function __construct()
    {
        $this->middleware('adminlocalize');
        $this->middleware('auth:admin');
    }

    /**
     * Show the form for updating resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function email()
    {
        return view('back.settings.email', [
            'datas' => EmailTemplate::get()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailTemplate $template)
    {
        return view('back.email_template.edit', compact('template'));
    }

    public function emailUpdate(Request $request)
    {
        $request->validate([

            "email_host" => "required:max:200",
            "email_port" => "required|max:10",
            "email_encryption" => "required|max:10",
            "email_user" => "required|max:100",
            "email_pass" => "required|max:100",
            "email_from" => "required|max:100",
            "email_from_name" => "required|max:100",
            "contact_email" => "required|max:100",
        ]);

        $input = $request->all();
        if (isset($request['smtp_check'])) {
            $input['smtp_check'] = 1;
        } else {
            $input['smtp_check'] = 0;
        }
        if (isset($request['order_mail'])) {
            $input['order_mail'] = 1;
        } else {
            $input['order_mail'] = 0;
        }
        if (isset($request['ticket_mail'])) {
            $input['ticket_mail'] = 1;
        } else {
            $input['ticket_mail'] = 0;
        }
        if (isset($request['is_queue_enabled'])) {
            $input['is_queue_enabled'] = 1;
        } else {
            $input['is_queue_enabled'] = 0;
        }
        if (isset($request['is_mail_verify'])) {
            $input['is_mail_verify'] = 1;
        } else {
            $input['is_mail_verify'] = 0;
        }

        Setting::first()->update($input);
        return redirect()->back()->withSuccess(__('Data Updated Successfully.'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmailTemplate $template)
    {
        $template->update($request->all());
        return redirect()->route('back.setting.email')->withSuccess(__('Email Template Updated Successfully.'));
    }

    /**
     * Test email functionality
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function testEmail(Request $request)
    {
        try {
            $setting = Setting::first();

            if (!$setting->smtp_check && !env('MAIL_USERNAME')) {
                return response()->json(['success' => false, 'message' => 'SMTP is not enabled and no .env email configuration found']);
            }

            $emailData = [
                'to' => $setting->contact_email ?: env('MAIL_FROM_ADDRESS', 'homefindbd@gmail.com'),
                'subject' => 'Email Test - HomeFindBD',
                'body' => '<h2>Email Test Successful!</h2><p>Your email configuration is working correctly.</p><p>This is a test email sent from HomeFindBD.com</p>',
            ];

            $emailHelper = new \App\Helpers\EmailHelper();
            $emailHelper->sendCustomMail($emailData);

            return response()->json(['success' => true, 'message' => 'Test email sent successfully']);
        } catch (\Exception $e) {
            \Log::error('Email test failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }
}
