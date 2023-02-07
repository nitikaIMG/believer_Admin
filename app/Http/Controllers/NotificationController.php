<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Helpers\Htmlhelpersemail;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use PHPExcel_IOFactory;
use Redirect;

class NotificationController extends Controller
{

    public function getusers(Request $request)
    {
        $gettypevalue = "";
        if (isset($_POST['gettypevalue'])) {
            $gettypevalue = $_POST['gettypevalue'];
            $getusers = explode(',', $_POST['userspresent']);
        }
        $qy = DB::table('registerusers');
        $findusers = $qy->whereNOTIn('id', $getusers)->where(function ($query) use ($gettypevalue) {
            $query->where('username', 'LIKE', '%' . $gettypevalue . '%')->orwhere('team', 'LIKE', '%' . $gettypevalue . '%')->orwhere('email', 'LIKE', '%' . $gettypevalue . '%');
        })->get();
        $option = "";
        $i = 0;
        if (!empty($findusers)) {
            foreach ($findusers as $user) {
                $showname = '<div class="d-flex bg-white shadow rounded p-2 my-2 mx-0 align-items-center"><div class="col-auto fs-35 pr-0"><i class="fad fa-user-circle"></i></div><div class="col"><div class="row"><div class="col-12 text-warning pr-0 font-weight-bold"> ' . $user->username . '  ' . $user->team . ' </div> ' . ' <div class="col-12 bg-text fs-13"> ' . $user->email . ' </div></div></div><div class="col-auto fs-30 pr-0"><i class="fal fa-plus-circle"></i><i onclick="deletediv(this,' . $user->id . ')" class="fal fa-times-circle text-danger"></i></div></div>';
                $option .= '<li class="pointer" onclick="set_item(' . $user->id . ')" id="userid' . $user->id . '">' . $showname . '</li>';
                $i++;
            }
        }
        echo $option;die;
    }
    public function pushnotifications(request $request)
    {
        if ($request->isMethod('post')) {
            $input = request()->all();
            $usertype = $input['usertype'];
            $uservalues = $input['uservalues'];
            if ($usertype == 'specific') {

                if (empty($uservalues)) {
                    return back()->with('error', 'Please select users');
                }

                if ($uservalues != "") {
                    $explodearray = explode(',', $uservalues);
                    if (!empty($explodearray)) {
                        $usersaray = array();
                        foreach ($explodearray as $earray) {
                            $finduseremail = DB::table('registerusers')->where('id', $earray)->select('id')->first();
                            if (!empty($finduseremail)) {
                                $titleget = $input['title'];
                                $msg = $input['message'];
                                $data = array();
                                $usersaray[] = $finduseremail->id;
                            }
                        }
                        $regIdChunk = array_chunk($usersaray, 500);
                        foreach ($regIdChunk as $RegId) {

                            $message_status = Helpers::sendmultiplenotification($titleget, $msg, '', $RegId);
                        }
                    }
                }
                if (!empty($input['uservaluess'])) {
                    $explodearray = $input['uservaluess'];

                    $usersaray = array();
                    foreach ($explodearray as $earray) {
                        $finduseremail = DB::table('registerusers')->where('id', $earray)->select('id')->first();

                        if (!empty($finduseremail)) {
                            $titleget = $input['title'];
                            $msg = $input['message'];
                            $data = array();
                            $usersaray[] = $finduseremail->id;
                        }
                    }

                    $regIdChunk = array_chunk($usersaray, 500);
                    foreach ($regIdChunk as $RegId) {
                        $message_status = Helpers::sendmultiplenotification($titleget, $msg, '', $RegId);

                    }
                    return redirect('my-admin/pushnotifications')->with('success', 'Notification Sent!');
                }
            } else {
                $findlallusers = DB::table('registerusers')->select('id')->get();
                $usersaray = array();
                if (!empty($findlallusers)) {
                    $titleget = $input['title'];
                    $msg = $input['message'];
                    foreach ($findlallusers as $user) {
                        $usersaray[] = $user->id;
                    }
                    $regIdChunk = array_chunk($usersaray, 500);
                    foreach ($regIdChunk as $RegId) {
                        $message_status = Helpers::sendmultiplenotification($titleget, $msg, '', $RegId);

                    }

                }
            }
            return Redirect::back()->with('success', 'Notification Sent!');
        }
        return view('notifications.sendnotifications');
    }
    public function smsnotifications(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = request()->all();
            $usertype = $input['usertype'];
            $uservalues = $input['uservalues'];
            if ($usertype == 'specific') {

                if (empty($uservalues)) {
                    return back()->with('error', 'Please select users');
                }

                $explodearray = explode(',', $uservalues);
                if (!empty($explodearray)) {
                    foreach ($explodearray as $earray) {
                        $finduseremail = DB::table('registerusers')->where('id', $earray)->select('mobile')->first();
                        if (!empty($finduseremail)) {
                            $msg = $input['message'];
                            if ($finduseremail->mobile != "") {
                                Helpers::sendTextSmsNew($msg, $finduseremail->mobile);
                            }

                        }
                    }
                }
            } else {
                $findlallusers = DB::table('registerusers')->select('mobile')->get();
                $a = $findlallusers->toArray();
                if (!empty($a)) {
                    foreach ($findlallusers as $user) {

                        $msg = $input['message'];
                        Helpers::sendTextSmsNew($msg, $user->mobile);
                    }
                }
            }
            return Redirect::back()->with('success', 'SMS Sent!');
        }
        return view('notifications.smsnotification');
    }

    public function emailnotifications(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = request()->all();
            $usertype = $input['usertype'];
            $uservalues = $input['uservalues'];
            if ($usertype == 'specific') {
                if (empty($uservalues)) {
                    return back()->with('error', 'Please select users');
                }

                if ($uservalues != "") {
                    $explodearray = explode(',', $uservalues);
                    if (!empty($explodearray)) {
                        foreach ($explodearray as $earray) {
                            $finduseremail = DB::table('registerusers')->where('id', $earray)->select('email')->first();
                            // $a = $finduseremail->toArray();
                            if (!empty($finduseremail)) {
                                $titleget = $input['title'];
                                $msg = $input['message'];
                                if (!empty($finduseremail->email)) {
                                    $msg = $content = Htmlhelpersemail::dynamic_email($msg);
                                    Helpers::mailsentFormat($finduseremail->email, $titleget, $msg);
                                }
                            }
                        }
                    }
                }
            } else {
                $findlallusers = DB::table('registerusers')->select('email')->get();
                if (!empty($findlallusers)) {
                    foreach ($findlallusers as $user) {
                        $titleget = $input['title'];
                        $msg = $input['message'];

                        if (!empty($user->email)) {
                            Helpers::mailsentFormat($user->email, $titleget, $msg);
                        }
                    }
                }
            }
            return Redirect::back()->with('success', 'Email Sent!');
        }
        return view('notifications.emailnotification');
    }
    public function import()
    {
        require_once './PHPExcel/PHPExcel/IOFactory.php';
        if (isset($_FILES["file"]["name"])) {
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);

            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $starti = 1;
                $counti = 1;
                for ($row = 2; $row <= $highestRow; $row++) {
                    $id = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $email = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $mobile = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $query = DB::table('registerusers')->select('id', 'email', 'mobile');
                    if (!empty($id)) {
                        $query->where('id', $id);
                    }
                    if (!empty($mobile)) {
                        $query->where('mobile', $mobile);
                    }
                    if (!empty($email)) {
                        $query->where('email', $email);
                    }
                    $excel_data[] = $query->get();

                }
                return view('notifications.sendnotifications')->with('excel_data', $excel_data);
            }

        }
    }
}
