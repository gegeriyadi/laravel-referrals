<?php

namespace Pdazcom\Referrals\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Class ReferralLink
 * @package Pdazcom\Referrals\Models
 */
class ReferralLink extends Model
{
    protected $fillable = ['user_id', 'referral_program_id'];

    protected static function boot()
    {
        static::creating(function (ReferralLink $model) {
            $model->generateCode();
        });
    }

    private function generateCode()
    {
        $this->code = (string) Uuid::uuid1();
    }

    public static function getReferral($user, $program)
    {
        return static::where([
            'user_id' => $user->id,
            'referral_program_id' => $program->id
        ])->first();
    }

    public function getLinkAttribute()
    {
        return url($this->program->uri) . '?ref=' . $this->code;
    }

    public function user()
    {
        $usersModel = config('auth.providers.users.model');
        return $this->belongsTo($usersModel);
    }

    public function program()
    {
        return $this->belongsTo(ReferralProgram::class, 'referral_program_id');
    }

    public function relationships()
    {
        return $this->hasMany(ReferralRelationship::class);
    }

}