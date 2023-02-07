<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use Session;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    # layout settings : bgcolor, project name or logo or images
    public function ui_settings()
    {
        if (request()->isMethod('post')) {

            $input = request()->except('_token');

            if (empty($input['input'])) {
                return back()->with('error', 'Please provide data to update');
            }

            if (!empty($input['input'])) {
                foreach ($input['input'] as $name => $value) {

                    # if logo
                    if (is_file($value)) {
                        $image = $value;
                        $destination = public_path();
                        $filename = $name . '.png';
                        $data['value'] = Helpers::logoUpload($image, $destination, $filename);

                        if ($data['value'] == '') {
                            return redirect()->back()->with('danger', 'Invalid extension of file you uploaded. You can only upload image.');
                        }

                        $data['name'] = $name;
                        $data['value'] = $value = $data['value'];
                    } else {
                        $data['name'] = $name;
                        $data['value'] = $value;
                    }

                    $is_exists = DB::table('settings')
                        ->where('name', $name)
                        ->first();

                    if (!empty($is_exists) and !empty($value)) {
                        DB::connection('mysql2')->table('settings')
                            ->where('name', $name)
                            ->update($data);
                    } else if (!empty($value)) {
                        DB::connection('mysql2')->table('settings')
                            ->insert($data);
                    }
                }
            }

            return redirect()->back()->with('success', 'Done');
        } else {
            $settings = DB::table('settings')
                ->pluck('value', 'name')
                ->toArray();

            $settings = (object) $settings;

            return view('ui_settings', compact('settings'));
        }
    }

    # facebook settings: app id or url
    public function facebook_settings()
    {
        if (request()->isMethod('post')) {

            $input = request()->except('_token');

            if (empty($input['input'])) {
                return back()->with('error', 'Please provide data to update');
            }

            if (!empty($input['input'])) {
                foreach ($input['input'] as $name => $value) {

                    $data['name'] = $name;
                    $data['value'] = $value;

                    $is_exists = DB::table('settings')
                        ->where('name', $name)
                        ->first();

                    if( empty($value) ) {
                        $data['value'] = '';
                    }

                    if (!empty($is_exists) ) {
                        DB::connection('mysql2')->table('settings')
                            ->where('name', $name)
                            ->update($data);
                    } else {
                        DB::connection('mysql2')->table('settings')
                            ->insert($data);
                    }
                }
            }

            return redirect()->back()->with('success', 'Done');
        } else {
            $settings = DB::table('settings')
                ->pluck('value', 'name')
                ->toArray();

            $settings = (object) $settings;

            return view('facebook_settings', compact('settings'));
        }
    }

    # google settings: app id or url
    public function google_settings()
    {
        if (request()->isMethod('post')) {

            $input = request()->except('_token');

            if (empty($input['input'])) {
                return back()->with('error', 'Please provide data to update');
            }

            if (!empty($input['input'])) {
                foreach ($input['input'] as $name => $value) {

                    $data['name'] = $name;
                    $data['value'] = $value;

                    $is_exists = DB::table('settings')
                        ->where('name', $name)
                        ->first();

                    if( empty($value) ) {
                        $data['value'] = '';
                    }

                    if (!empty($is_exists) ) {
                        DB::connection('mysql2')->table('settings')
                            ->where('name', $name)
                            ->update($data);
                    } else {
                        DB::connection('mysql2')->table('settings')
                            ->insert($data);
                    }
                }
            }

            return redirect()->back()->with('success', 'Done');
        } else {
            $settings = DB::table('settings')
                ->pluck('value', 'name')
                ->toArray();

            $settings = (object) $settings;

            return view('google_settings', compact('settings'));
        }
    }

    # alert settings: notification sms or email alerts
    public function alert_settings()
    {
        if (request()->isMethod('post')) {

            $input = request()->except('_token');

            if (empty($input['input'])) {
                return back()->with('error', 'Please provide data to update');
            }

            if (!empty($input['input'])) {
                foreach ($input['input'] as $name => $value) {

                    $data['name'] = $name;
                    $data['value'] = $value;

                    $is_exists = DB::table('settings')
                        ->where('name', $name)
                        ->first();

                    if( empty($value) ) {
                        $data['value'] = '';
                    }

                    if (!empty($is_exists) ) {
                        DB::connection('mysql2')->table('settings')
                            ->where('name', $name)
                            ->update($data);
                    } else {
                        DB::connection('mysql2')->table('settings')
                            ->insert($data);
                    }
                }
            }

            return redirect()->back()->with('success', 'Done');
        } else {
            $settings = DB::table('settings')
                ->pluck('value', 'name')
                ->toArray();

            $settings = (object) $settings;

            return view('alert_settings', compact('settings'));
        }
    }

    # payment gateway : app id or url
    public function payment_gateway_settings()
    {
        if (request()->isMethod('post')) {

            $input = request()->except('_token');

            if (empty($input['input'])) {
                return back()->with('error', 'Please provide data to update');
            }

            if (!empty($input['input'])) {
                foreach ($input['input'] as $name => $value) {

                    $name = $input['type'] . '_' . $name;
                    $data['name'] = $name;
                    $data['value'] = $value;

                    $is_exists = DB::table('settings')
                        ->where('name', $name)
                        ->first();

                    if( empty($value) ) {
                        $data['value'] = '';
                    }

                    if (!empty($is_exists) ) {
                        DB::connection('mysql2')->table('settings')
                            ->where('name', $name)
                            ->update($data);
                    } else {
                        DB::connection('mysql2')->table('settings')
                            ->insert($data);
                    }
                }
            }

            return redirect()->back()->with('success', 'Done');
        } else {
            $settings = DB::table('settings')
                ->orwhere('name', 'LIKE', '%cashfree_%')
                ->orwhere('name', 'LIKE', '%paytm_%')
                ->orwhere('name', 'LIKE', '%stripe_%')
                ->orwhere('name', 'LIKE', '%razorpay_%')
                ->pluck('value', 'name')
                ->toArray();

            $settings = (object) $settings;

            return view('payment_gateway_settings', compact('settings'));
        }
    }

    # payment gateway ajax data
    public function show_credentials_box()
    {
        $type = request()->get('type');

        if (!empty($type)) {

            $settings = DB::table('settings')
                ->orwhere('name', 'LIKE', '%' . $type . '_%')
                ->pluck('value', 'name')
                ->toArray();

            $settings = (array) $settings;

            return ($settings);
        }
    }

    # reset admin theme
    public function reset_admin_theme()
    {

        if(auth()->user()->masterpassword == request()->get('masterpassword')) {
            
            DB::connection('mysql2')->table('settings')
                ->where('name', 'LIKE', '%_rgb%')
                ->orWhere('name', 'LIKE', '%_hex%')
                ->orWhere('name', 'LIKE', '%_hsl%')
                ->orWhere('name', 'LIKE', '%font%')
                ->delete();

            return back()->with('success', 'Theme reset successfully');
        } else {
            return back()->with('error', 'Invalid master password');
        }      
    }

}
