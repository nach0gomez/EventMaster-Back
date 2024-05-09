<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
    //nombre de la llave primaria
    protected $primaryKey = 'document';

    //Datos que se Pueden llenar o modificar
    protected $fillable = [
            'first_name', 
            'middle_name', 
            'last_name', 
            'second_last_name', 
            'email', 
            'is_admin', 
            'is_eplanner', 
            'is_eattendee', 
            'status', 
            'document', 
            'password', 
            'username'];
    protected $hidden = [
           'password'
            ];

} 
