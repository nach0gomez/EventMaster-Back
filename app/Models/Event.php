<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    //nombre de la llave primaria
    protected $primaryKey = 'id_event';
     //Datos que se Pueden llenar o modificar
     protected $fillable = [
        'id_event', 
        'title', 
        'description', 
        'date', 
        'time', 
        'location', 
        'duration', 
        'status',
        'id_user',
        'restriction_minors_allowed', 
        'max_attendees'];
}
