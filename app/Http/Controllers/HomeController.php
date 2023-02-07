<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use DB;
use Session;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Storage;
class HomeController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
     public function __construct(){
         
        $this->middleware('auth');
    } 

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
     public function admin_profie()
    {
        $id = Auth::user()->id;
         $profile=DB::table('users')->select('*')->where('id',$id)->first();
         
         return view('admin/admin_profile',compact('profile'));
    }
    public function update_profile(Request  $request)
    {
         $input=$request->all();
         
         $id = Auth::user()->id;
         $d = DB::table('users')->get();
         $exist = DB::table('users')->where('masterpassword',$input['masterpassword'])->first();
         
         if(!empty($exist)){
            unset($input['_token'] );
               if($request->file('image')){
                $image = $request->file('image');
                $destination = public_path(); 
                $filename = 'profile-'.time();
                $input['image'] = Helpers::imageSingleUpload($image,$destination,$filename);
                if($input['image']==''){
                    return redirect()->back()->with('danger','Invalid extension of file you uploaded. You can only upload image.');
                }
                $oldimage = DB::table('users')->where('id', $id)->first();
                $filename= $oldimage->image;          
                $filenamep = public_path().'/'.$filename;
                @unlink($filenamep);
             }
             unset($input['oldmasterpassword'] );
            DB::connection('mysql2')->table('users')->where('id',$id)->update($input);
             return redirect()->back()->with('success','Successfully update profile');
        }else{
             return redirect()->back()->with('danger','Master password was incorrect');
        }
    }
    public function admin_change_password(Request  $request)
    {
        $input=$request->all();
        if(!empty($input)){
            $item = auth()->attempt([
            'email'    => Auth::user()->email,
            'password' => $request->current_password
        ]);
        if(empty($item)){
            return redirect()->back()->with('danger','Please enter right current password');
        }
        if($input['new_password'] != $input['confirm_password'])
        {
            return redirect()->back()->with('danger','Not match confirm password and new password');
        }else{
            $id=Auth::user()->id;
            if(!empty($id)){
                $password['password'] = bcrypt($input['new_password']);
                DB::connection('mysql2')->table('users')->where('id',$id)->update($password);
                return redirect()->back()->with('success','Successfully change your password');
            }else{
                return redirect()->back()->with('danger','Not your email address.');
            }           
        }
         }else{
            return view('admin/change_password');
         }
    }
    
    public function change_masterpassword(Request  $request)
    {
        $input=$request->all();
        if(!empty($input)){

            $item = DB::table('users')
                        ->where('email', auth()->user()->email)
                        ->where('masterpassword', $request->current_masterpassword)
                        ->first();

        if(empty($item)){
            return redirect()->back()->with('danger','Please enter right current masterpassword');
        }
        if($input['new_masterpassword'] != $input['confirm_masterpassword'])
        {
            return redirect()->back()->with('danger','Not match confirm masterpassword and new masterpassword');
        }else{
            $id=Auth::user()->id;
            if(!empty($id)){
                $password['masterpassword'] = $input['new_masterpassword'];
                DB::connection('mysql2')->table('users')->where('id',$id)->update($password);
                return redirect()->back()->with('success','Successfully change your masterpassword');
            }else{
                return redirect()->back()->with('danger','Not your email address.');
            }           
        }
         }else{
            return view('admin/change_masterpassword');
         }
    }

}
