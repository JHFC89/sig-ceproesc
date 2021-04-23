<?php

namespace App\Models;

use App\Mail\InvitationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function findByCode(string $code)
    {
        return self::where('code', $code)->firstOrFail();
    }

    public function hasBeenUsed()
    {
        return $this->user_id !== null;
    }

    public function createUserFromArray(array $data)
    {
        $user = User::create([
            'email'     => $data['email'],
            'password'  => bcrypt($data['password']),
        ]);

        $this->user()->associate($user)->save();

        return $user;
    }

    public function send()
    {
        Mail::to($this->email)->send(new InvitationEmail($this));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
