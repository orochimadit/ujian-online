<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'tbl_user';
    protected $primaryKey= 'id_user';
    protected $fillable = ['id_user','nama','email','password','token'];
}
