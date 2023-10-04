<?php 
namespace Erahma\FutureFramework\Models;
use Illuminate\Database\Eloquent\Model;


class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_image',
        'api_key',
    ];

    function messages() {
        return $this->hasMany(Message::class, 'user_id');
    }
    function incomingMessages() {
        return $this->hasMany(Message::class, 'recipient_id');
    }
}
