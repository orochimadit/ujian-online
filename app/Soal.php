<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'tbl_soal';
    protected $primaryKey= 'id_soal';
    protected $fillable = ['id_soal','pertanyaan','opsi1','opsi2','opsi3','opsi4','jawaban'];

}
