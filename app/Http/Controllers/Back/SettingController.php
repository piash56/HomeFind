<?php

namespace App\Http\Controllers\Back;

use Illuminate\Http\Request;

use App\{
    Models\Setting,
    Models\Language,
    Models\EmailTemplate,
    Http\Controllers\Controller,
    Http\Requests\SettingRequest,
    Repositories\Back\SettingRepository
};
use App\Models\ExtraSetting;

class SettingController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\SettingRepository $repository
     *
     */
    public function __construct(SettingRepository $repository)
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
        $this->repository = $repository;
    }

    /**
     * Show the form for updating resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function system()
    {
        $setting = Setting::first();
        return view('back.settings.general', compact('setting'));
    }


    /**
     * Show the form for updating resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function menu()
    {

        return view('back.settings.menu');
    }

    /**
     * Show the form for updating resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function language()
    {
        $data = Language::first();
        $data_results = file_get_contents(resource_path() . '/lang/' . $data->file);
        $lang = json_decode($data_results, true);
        return view('back.settings.language', compact('data', 'lang'));
    }

    /**
     * Show the form for updating resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function social()
    {
        return view('back.settings.social', [
            'google_url' => url('/auth/google/callback'),
            'facebook_url' => preg_replace("/^http:/i", "https:", url('/auth/facebook/callback'))
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request)
    {
        $this->repository->update($request);
        return redirect()->back()->withSuccess(__('Data Updated Successfully.'));
    }


    public function section()
    {
        return view('back.settings.section');
    }

    public function visiable(Request $request)
    {

        $feilds = [
            'is_slider',
            'is_three_c_b_first',
            'is_popular_category',
            'is_three_c_b_second',
            'is_highlighted',
            'is_two_column_category',
            'is_popular_brand',
            'is_featured_category',
            'is_two_c_b',
            'is_blogs',
            'is_service',
            'is_t2_slider',
            'is_t2_service_section',
            'is_t2_3_column_banner_first',
            'is_t2_flashdeal',
            'is_t2_new_product',
            'is_t2_3_column_banner_second',
            'is_t2_featured_product',
            'is_t2_bestseller_product',
            'is_t2_toprated_product',
            'is_t2_2_column_banner',
            'is_t2_blog_section',
            'is_t2_brand_section',
            'is_t3_slider',
            'is_t3_service_section',
            'is_t3_3_column_banner_first',
            'is_t3_popular_category',
            'is_t3_flashdeal',
            'is_t3_3_column_banner_second',
            'is_t3_pecialpick',
            'is_t3_brand_section',
            'is_t3_2_column_banner',
            'is_t3_blog_section',
            'is_t4_slider',
            'is_t4_featured_banner',
            'is_t4_specialpick',
            'is_t4_3_column_banner_first',
            'is_t4_flashdeal',
            'is_t4_3_column_banner_second',
            'is_t4_popular_category',
            'is_t4_2_column_banner',
            'is_t4_blog_section',
            'is_t4_brand_section',
            'is_t4_service_section',
            'is_t1_falsh',
            'is_t2_falsh',
            'is_t3_falsh',
            'is_t2_three_column_category',
            'is_t3_three_column_category',
        ];


        $extrasetting = ExtraSetting::find(1);
        $setting = Setting::find(1);

        foreach ($feilds as $field) {
            if ($request->has($field)) {
                $setting_input[$field] = 1;
                $input[$field] = 1;
            } else {
                if ($this->checkVisibaltyUrl(url()->previous())) {
                    $input[$field] = 0;
                    $setting_input[$field] = 0;
                }
            }
        }


        $extrasetting->update($input);
        $setting->update($setting_input);

        return redirect()->back()->withSuccess(__('Data Updated Successfully.'));
    }

    public function checkVisibaltyUrl($url)
    {
        $segment = explode('/', url()->previous());
        $value = end($segment);
        if ($value == 'section') {
            return true;
        } else {
            return false;
        }
    }


    public function announcement()
    {
        return view('back.settings.announcement');
    }

    public function cookie()
    {
        return view('back.settings.cookie');
    }

    public function maintainance()
    {
        return view('back.settings.maintainance');
    }

    /**
     * Show the form for editing home page hero section.
     *
     * @return \Illuminate\Http\Response
     */
    public function homePage()
    {
        $setting = Setting::first();
        return view('back.settings.homepage', compact('setting'));
    }

    /**
     * Update home page hero section settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateHomePage(SettingRequest $request)
    {
        $setting = Setting::first();

        // Store hero section settings as JSON
        $heroSettings = [
            'badge_text' => $request->hero_badge_text,
            'badge_icon' => $request->hero_badge_icon,
            'headline_line1' => $request->hero_headline_line1,
            'headline_line2' => $request->hero_headline_line2,
            'description' => $request->hero_description,
            'button1_text' => $request->hero_button1_text,
            'button1_link' => $request->hero_button1_link,
            'button1_icon' => $request->hero_button1_icon,
            'button2_text' => $request->hero_button2_text,
            'button2_link' => $request->hero_button2_link,
            'button2_icon' => $request->hero_button2_icon,
            'stat1_number' => $request->hero_stat1_number,
            'stat1_label' => $request->hero_stat1_label,
            'stat2_number' => $request->hero_stat2_number,
            'stat2_label' => $request->hero_stat2_label,
            'stat3_number' => $request->hero_stat3_number,
            'stat3_label' => $request->hero_stat3_label,
        ];

        $setting->hero_section_settings = json_encode($heroSettings);
        $setting->save();
        
        return redirect()->back()->withSuccess(__('Home Page Settings Updated Successfully.'));
    }

    /**
     * Show the form for editing footer settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function footer()
    {
        $setting = Setting::first();
        return view('back.settings.footer', compact('setting'));
    }

    /**
     * Update footer settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateFooter(Request $request)
    {
        $setting = Setting::first();
        $input = $request->all();

        // Store quick links as JSON (3 links)
        $quickLinks = [];
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($request->input("quick_link_label_{$i}")) && !empty($request->input("quick_link_url_{$i}"))) {
                $quickLinks[] = [
                    'label' => $request->input("quick_link_label_{$i}"),
                    'url' => $request->input("quick_link_url_{$i}"),
                ];
            }
        }
        $input['footer_quick_links'] = json_encode($quickLinks);

        // Handle social links (same as existing)
        if ($request->social_icons && $request->social_links) {
            $links = ['icons' => $request->social_icons, 'links' => $request->social_links];
            $input['social_link'] = json_encode($links, true);
        }

        // Update copyright
        if ($request->has('copy_right')) {
            $input['copy_right'] = $request->copy_right;
        }

        // Update footer contact info
        if ($request->has('footer_address')) {
            $input['footer_address'] = $request->footer_address;
        }
        if ($request->has('footer_phone')) {
            $input['footer_phone'] = $request->footer_phone;
        }
        if ($request->has('footer_email')) {
            $input['footer_email'] = $request->footer_email;
        }
        if ($request->has('working_days_from_to')) {
            $input['working_days_from_to'] = $request->working_days_from_to;
        }
        if ($request->has('friday_start')) {
            $input['friday_start'] = $request->friday_start;
        }
        if ($request->has('friday_end')) {
            $input['friday_end'] = $request->friday_end;
        }

        $setting->update($input);
        
        return redirect()->back()->withSuccess(__('Footer Settings Updated Successfully.'));
    }
}
