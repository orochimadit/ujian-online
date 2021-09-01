<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Firebase\JWT\JWT;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Peserta;
class pesertaController extends Controller
{
    

    public function registrasi(Request $request){
        $validator = Validator::make($request->all(),[
            'nama' =>'required',
            'email' =>'required | unique:tbl_peserta',
            'password' => 'required | confirmed',
            'password_confirmation'     => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 'gagal',
                'message' => $validator->messages()
            ]);
        }
        if(Peserta::create(
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
      
    }

    public function loginPeserta(Request $request){
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
        $cek =Peserta::where('email',$request->email)->count();
        $peserta = Peserta::where('email',$request->email)->get();
        if($cek>0){
            foreach($peserta as $pst){
                if($request->password ==decrypt($pst->password)){
                    $key = env('APP_KEY');
                    $data = array(
                        "extime"=> time()+(60*120),
                        "id_peserta"=> $pst->id_peserta
                    );
                    $jwt= JWT::encode($data,$key);
                    Peserta::where('id_peserta',$pst->id_peserta)->update([
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
}
