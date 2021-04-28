<?php

namespace App\Models;

use App\Facades\InvitationCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['invitation'];

    protected $dates = ['birthdate'];

    public function getEmailAttribute()
    {
        if (empty($this->user)) {
            return $this->invitation->email;
        }

        return $this->user->email;
    }

    public function getFormattedBirthdateAttribute()
    {
        return $this->birthdate->format('d/m/Y');
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

    public static function formatBirthdateFromArray(array $birthdate)
    {
        return "{$birthdate['year']}-{$birthdate['month']}-{$birthdate['day']}";
    }

    public function isForNovice()
    {
        return $this->role->name == Role::NOVICE;
    }

    public function isForInstructor()
    {
        return $this->role->name == Role::INSTRUCTOR;
    }

    public function isForEmployer()
    {
        return $this->role->name == Role::EMPLOYER;
    }

    public function isForCoordinator()
    {
        return $this->role->name == Role::COORDINATOR;
    }

    public static function scopeWhereCoordinator($query)
    {
        return $query->whereHas('role', function (Builder $query) {
            $query->where('name', Role::COORDINATOR);
        });
    }

    public static function scopeWhereInstructor($query)
    {
        return $query->whereHas('role', function (Builder $query) {
            $query->where('name', Role::INSTRUCTOR);
        });
    }

    public static function scopeWhereEmployer($query, int $employer_id)
    {
        return $query->where('employer_id', $employer_id);
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

    public function employer()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class);
    }
}
