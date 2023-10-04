<?php 
namespace Erahma\FutureFramework\Models;
use Illuminate\Database\Eloquent\Model;


class Message extends Model
{
    protected $fillable = [
        'user_id',
        'recipient_id',
        'recipient_email',
        'message',
        'is_read',
    ];
}
