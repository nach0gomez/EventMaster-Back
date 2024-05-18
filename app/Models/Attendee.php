<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendee extends Model
{
    
    protected $table = 'attendees';
    //nombre de la llave primaria
    protected $primaryKey = 'id_attendee';
     //Datos que se Pueden llenar o modificar
     protected $fillable = [
        'id_attendee',
        'id_event', 
        'id_user'];
}
