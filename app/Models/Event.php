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
        'event_type',
        'restriction_minors_allowed', 
        'max_attendees'];


    public function attendees()
    {
        return $this->belongsToMany(User::class, 'attendees', 'id_event', 'id_user');
    }
}
