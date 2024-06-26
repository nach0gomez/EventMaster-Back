<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;

    protected $table = 'users';
    // nombre de la llave primaria
    protected $primaryKey = 'id_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'email_verificate',
        'email_verificate_confirm',
        'password', 
        'username'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verificate',
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'attendees', 'id_user', 'id_event');
    }
         //es un identificador que se almacena en una parte del JSON web token
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
        //contiene cualquier reclamo personalizado que desee incluir en su token JWT
    public function getJWTCustomClaims()
    {
        return [];
    }
}
