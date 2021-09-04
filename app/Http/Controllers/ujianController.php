<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Firebase\JWT\JWT;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Peserta;
use App\Admin;
use App\Soal;
use App\Jawaban;
use App\Skor;


class ujianController extends Controller
{
	public function listSoal(Request $request){
		$token = $request->token;
		$tokenDb = Peserta::where('token',$token)->count();
		if ($tokenDb > 0){
			$key =env('APP_KEY');
			$decoded = JWT::decode($token, $key, array('HS256'));
			$decoded_array = (array) $decoded;
			if($decoded_array['extime']>time()){
			  
				$cal_skor = Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->count();
				$id_s = "";
				if($cal_skor > 0){
				   
					$id_s = Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();
					
				}else{
					Skor::create([
						'id_peserta' => $decoded_array['id_peserta']
					]);
					$id_s = Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();;
				}
				$skor = Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();
				$jawaban = Jawaban ::where('id_peserta',$decoded_array['id_peserta'])->first();
				$jum_jawaban = Jawaban::where('id_peserta',$decoded_array['id_peserta'])->where('id_skor',$skor->id_skor)->count();
				$jumlah_soal = Soal::count();
				$max_rand = $jumlah_soal - 10;
				$mulai = rand(0, $max_rand);
				$soal = Soal::skip($mulai)->take(10)->get();
				$data = array();
				foreach($soal as $p){
					$data[] = array(
						'id_soal' => $p->id_soal,
						'pertanyaan'=> $p->pertanyaan,
						'opsi1'=>$p->opsi1,
						'opsi2'=> $p->opsi2,
						'opsi3'=> $p->opsi3,
						'opsi4'=> $p->opsi4,
						'jumlah_jawaban'=> $jum_jawaban
					);
				}
				return response()->json([
					'status' =>'berhasil',
					'message' => 'Data berhasil diambil',
					'id_skor' => $id_s->id_skor,
					'data'  =>$data

				]);
			}
		}else{
			return response()->json([
				'status'=> 'gagal',
				'message'=> 'Token Kadaluarsa'
			]);
		}
	}

	public function jawab(Request $request){
		$token = $request->token;
		$tokenDb = Peserta::where('token',$token)->count();
		if ($tokenDb > 0){
			$key =env('APP_KEY');
			$decoded = JWT::decode($token, $key, array('HS256'));
			$decoded_array = (array) $decoded;
			if($decoded_array['extime']>time()){
				$soal = Soal::where('id_soal',$request->id_soal)->get();
				foreach($soal as $s ){
					if($request->jawaban == $s->jawaban){
						if(Jawaban::create([
							'id_peserta'    => $decoded_array['id_peserta'],
							'id_soal'       => $s->id_soal,
							'jawaban'       => $request->jawaban,
							'id_skor'       => $request->id_skor,
							'status_jawaban'   =>'1'
						])){
							return response()->json([
								'status'=> 'berhasil',
								'message'=> 'Data berhasil disimpan'
							]);
						}else{
							return response()->json([
								'status'=> 'gagal',
								'message'=> 'Data Gagal Disimpian'
							]);
						}
					}else{
						if(Jawaban::create([
							'id_peserta'    => $decoded_array['id_peserta'],
							'id_soal'       => $s->id_soal,
							'jawaban'       => $request->jawaban,
							'id_skor'       => $request->id_skor,
							'status_jawaban'   =>'0'
						])){
							return response()->json([
								'status'=> 'berhasil',
								'message'=> 'Data berhasil disimpan'
							]);
						}else{
							return response()->json([
								'status'=> 'gagal',
								'message'=> 'Data Gagal Disimpian'
							]);
						}
					}
				}
			} else{
				return response()->json([
					'status'=> 'gagal',
					'message'=> 'Token Kadaluarsa'
				]);
			}
	
	}else{
		return response()->json([
			'status'=> 'gagal',
			'message'=> 'Token Tidak Valid'
		]);
	}
}

public function hitungSkor(Request $request){
	$token = $request->token;
	$tokenDb = Peserta::where('token',$token)->count();
	if ($tokenDb > 0){
		$key =env('APP_KEY');
		$decoded = JWT::decode($token, $key, array('HS256'));
		$decoded_array = (array) $decoded;
		if($decoded_array['extime']>time()){
			$id_s = Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();
			$jawaban = Jawaban::where('status_jawaban','1')->where('id_skor',$id_s->id_skor)->count();
			return response()->json([
				'status'=>'berhasil',
				'skor'	=>$jawaban
			]);
		}else{
			return response()->json([
				'status'=> 'gagal',
				'message'	=>'Token Kadaluarsa'
			]);
		}
		}else{
		return response()->json([
			'status'=> 'gagal',
			'message'=> 'Token Tidak Valid'
		]);
	}
}

public function selesaiUjian(Request $request){
	$token = $request->token;
	$tokenDb = Peserta::where('token',$token)->count();
	if ($tokenDb > 0){
		$key =env('APP_KEY');
		$decoded = JWT::decode($token, $key, array('HS256'));
		$decoded_array = (array) $decoded;
		if($decoded_array['extime']>time()){
			$id_s = Skor::where('id_peserta',$decoded_array['id_peserta'])->where('status','1')->first();
			if(Skor::where('id_skor',$id_s->id_skor)->update([
				'status'=>'0'
			])){
				return response()->json([
					'status'=>'berhasil',
					'message'	=>'data berhasil diubah'
				]);
			}else{
				return response()->json([
					'status'=>'berhasil',
					'message'	=>'data gagal diubah'
				]);
			}
		}else{
			return response()->json([
				'status'=> 'gagal',
				'message'	=>'Token Kadaluarsa'
			]);
		}
	}else{
		return response()->json([
			'status'=> 'gagal',
			'message'=> 'Token Tidak Valid'
		]);
	}		
}
}
