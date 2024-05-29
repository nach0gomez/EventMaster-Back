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
        'username',
        'document',
        'email',
        'password',
        'status',
        
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
