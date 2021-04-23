<?php

namespace App\Models;

use App\Facades\InvitationCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['invitation'];

    public function getEmailAttribute()
    {
        if (empty($this->user)) {
            return $this->invitation->email;
        }

        return $this->user->email;
    }

    public function attachUser(User $user)
    {
        $this->user()->associate($user)->save();

        $user->roles()->attach($this->role_id);

        return $user->refresh();
    }

    public function sendInvitationEmail(string $email)
    {
        $invitation = $this->invitation()->save(new Invitation([
            'email' => $email,
            'code' => InvitationCode::generate(),
        ]));

        $invitation->send();

    }

    public static function employersForCompany(Company $company)
    {
        return self::query()->with('invitation')
                            ->where('company_id', $company->id)
                            ->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
