<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Konten extends Model
{
    protected $table = 'tbl_konten';
    protected $primaryKey= 'id_konten';
    protected $fillable = ['id_konten','judul','keterangan','link_thumbnail','link_video','status','view'];
}
