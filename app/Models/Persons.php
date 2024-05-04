<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class Persons extends Model
{
    //nombre de la llave primaria
    protected $primaryKey = 'id_person';

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
            'id_user', 
            'id_person', 
            'password', 
            'username'];
    protected $hidden = [
           'password'
            ];

} 
