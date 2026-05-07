<?php

namespace Modules\LandingPage\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\LandingPage\Entities\LandingPageSetting;

class LandingPageDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $data['topbar_status'] = 'on';
        $data['topbar_notification_msg'] = '70% Special Offer. Don’t Miss it. The offer ends in 72 hours.';

        $data['menubar_status'] = 'on';
        $data['menubar_page'] = json_encode([
            [
                "menubar_page_name" => "About Us",
                "page_url" => "https://idabtech.com/about/",
                "header" => "on",
                "footer" => "on",
                "login" => "on"
            ],
            [
                "menubar_page_name" => "Terms and Conditions",
                "page_url" => "https://idabtech.com/terms-and-condition/",
                "header" => "on",
                "footer" => "on",
                "login" => "on"
            ],
            [
                "menubar_page_name" => "Privacy Policy",
                "page_url" => "https://idabtech.com/privacy-policy/",
                "header" => "on",
                "footer" => "on",
                "login" => "on"
            ],
        ]);

        $data['site_logo'] = 'site_logo.png';
        $data['site_description'] = 'We build modern web tools to help you jump-start your daily business work.';
        $data['home_status'] = 'on';
        $data['home_offer_text'] = '70% Special Offer';
        $data['home_title'] = 'Home';
        $data['home_heading'] = 'IDAB TECH - HRM and Payroll Tool';
        $data['home_description'] = 'Use these awesome forms to login or create new account in your project for free.';
        $data['home_trusted_by'] = '1000+ Customer';
        $data['home_live_demo_link'] = 'https://demo.workdo.io/hrmgo-saas/login';
        $data['home_buy_now_link'] = 'https://idabtech.com/pricing/';
        $data['home_banner'] = 'home_banner.png';
        $data['home_logo'] = 'site_logo.png';

        $data['feature_status'] = 'on';
        $data['feature_title'] = 'Features';
        $data['feature_heading'] = 'All In One Place HRM System';
        $data['feature_description'] = 'Use these awesome forms to login or create new account in your project for free. Use these awesome forms to login or create new account in your project for free.';
        $data['feature_buy_now_link'] = 'https://idabtech.com/pricing/';
        $data['feature_of_features'] = '[{"feature_logo":"1686545757-feature_logo.png","feature_heading":"Support","feature_description":"Use these awesome forms to login or create new account in your project for free.Use these awesome forms to login or create new account in your project for free."},{"feature_logo":"1686546152-feature_logo.png","feature_heading":"Integration","feature_description":"Use these awesome forms to login or create new account in your project for free.Use these awesome forms to login or create new account in your project for free."}]';

        $data['highlight_feature_heading'] = 'IDAB TECH - HRM and Payroll Tool';
        $data['highlight_feature_description'] = 'Use these awesome forms to login or create new account in your project for free.';
        $data['highlight_feature_image'] = 'highlight_feature_image.png';

        $data['screenshots_status'] = 'on';
        $data['screenshots_heading'] = 'IDAB TECH - HRM and Payroll Tool';
        $data['screenshots_description'] = 'Use these awesome forms to login or create new account in your project for free.';
        $data['screenshots'] = '[{"screenshots":"1728551950-screenshots.png","screenshots_heading":"HRM Dashboard"},{"screenshots":"1728551960-screenshots.png","screenshots_heading":"User Roles"},{"screenshots":"1728551971-screenshots.png","screenshots_heading":"Profile Overview"}]';

        $data['footer_status'] = 'on';
        $data['joinus_status'] = 'on';
        $data['joinus_heading'] = 'Join Our Community';
        $data['joinus_description'] = 'We build modern web tools to help you jump-start your daily business work.';


        foreach($data as $key => $value){
            if(!LandingPageSetting::where('name', '=', $key)->exists()){
                LandingPageSetting::updateOrCreate(['name' =>  $key],['value' => $value]);
            }
        }
    }
}
