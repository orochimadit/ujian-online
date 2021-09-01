<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'tbl_peserta';
    protected $primaryKey= 'id_peserta';
    protected $fillable = ['id_peserta','nama','email','password','token','status'];
}
