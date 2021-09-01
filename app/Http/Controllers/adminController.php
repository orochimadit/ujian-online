<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Firebase\JWT\JWT;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Admin;

class adminController extends Controller
{
    //

    public function tambahAdmin(Request $request){
        $validator = Validator::make($request->all(),[
            'nama' =>'required',
            'email' =>'required | unique:tbl_user',
            'password' => 'required',
            'token'     => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
        $token = $request->token;
        $tokenDb = Admin::where('token',$token)->count();
        if($tokenDb> 0){
            $key =env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            if($decoded_array['extime']> time()){
                if(Admin::create(
                    [
                        'nama' => $request->nama,
                        'email' => $request->email,
                        'password' => encrypt($request ->password)
                    ]
                )){
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Data berhasil disimpan'
                    ]);
                }else{
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Data Gagal Disimpan'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token Kadaluarsa'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token Tidak Valid'
            ]);
        }
    }
    public function loginAdmin(Request $request){
        $validator = Validator::make($request->all(),[
            
            'email' =>'required',
            'password' =>'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
        $cek =Admin::where('email',$request->email)->count();
        $admin = Admin::where('email',$request->email)->get();
        if($cek>0){
            foreach($admin as $adm){
                if($request->password ==decrypt($adm->password)){
                    $key = env('APP_KEY');
                    $data = array(
                        "extime"=> time()+(60*120),
                        "id_admin"=> $adm->id_user
                    );
                    $jwt= JWT::encode($data,$key);
                    Admin::where('id_user',$adm->id_user)->update([
                        'token' => $jwt
                    ]);
                    return response()->json([
                        'status'=>'Berhasil',
                        'message'=>'Berhasil login',
                        'token'=> $jwt
                    ]);
                }else{
                    return response()->json([
                        'status'=>'gagal',
                        'message'=>'Password salah'
                    ]);
                }
                
            }
        }else{
            return response()->json([
                'status'=>'gagal',
                'message'=>'Email tidak terdaftar'
            ]);
        }
    }

    public function hapusAdmin(Request $request){
        $validator = Validator::make($request->all(),[
            'id_user' =>'required',
            'token'     => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
     
        $token = $request->token;
        $tokenDb = Admin::where('token',$token)->count();
        if($tokenDb> 0){
            $key =env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            if($decoded_array['extime']> time()){
                if(Admin::where('id_user',$request->id_user)->delete()){
                    return response()->json([
                        'status' => 'berhasil',
                        'message' => 'Data berhasil dihapus'
                    ]);
                }else{
                    return response()->json([
                        'status' => 'gagal',
                        'message' => 'Data Gagal Dihapus'
                    ]);
                }
            }else{
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token Kadaluarsa'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token Tidak Valid'
            ]);
        }
    }

    public function listAdmin(Request $request){
        $validator = Validator::make($request->all(),[
            // 'id_user' =>'required',
            'token'     => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
     
        $token = $request->token;
        $tokenDb = Admin::where('token',$token)->count();
        if($tokenDb> 0){
            $key =env('APP_KEY');
            $decoded = JWT::decode($token, $key, array('HS256'));
            $decoded_array = (array) $decoded;
            if($decoded_array['extime']> time()){
               $admin = Admin::get();
               $data = array();

               foreach($admin as $adm){
                   $data[] = array(
                       'nama' => $adm->nama,
                       'email' =>$adm->email,
                       'id_user'=>$adm->id_user
                   );
               }
               return response()->json([
                'status'=> 'berhasil',
                'message'=> 'Data berhasil diambil',
                'data'=> $data
            ]);
            }else{
                return response()->json([
                    'status' => 'gagal',
                    'message' => 'Token Kadaluarsa'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'gagal',
                'message' => 'Token Tidak Valid'
            ]);
        }
    }
}
