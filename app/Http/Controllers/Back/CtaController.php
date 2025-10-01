<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Repositories\Back\SettingRepository;
use Illuminate\Http\Request;

class CtaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    public function index()
    {
        $setting = \App\Models\Setting::first();
        return view('back.setting.cta', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cta_phone' => 'required|string|max:20',
            'cta_whatsapp' => 'nullable|string|max:20',
            'cta_text' => 'nullable|string|max:255',
            'cta_enabled' => 'nullable|boolean',
        ]);

        $setting = \App\Models\Setting::first();

        $setting->cta_phone = $request->cta_phone;
        $setting->cta_whatsapp = $request->cta_whatsapp ?: $request->cta_phone;
        $setting->cta_text = $request->cta_text;
        $setting->cta_enabled = $request->has('cta_enabled') ? 1 : 0;

        $setting->save();

        return redirect()->back()->withSuccess(__('Call to Action settings updated successfully.'));
    }
}
