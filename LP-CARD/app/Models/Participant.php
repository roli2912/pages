<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'creative_answer',
        'newsletter_subscription',
        'week_identifier'
    ];

    protected $casts = [
        'newsletter_subscription' => 'boolean',
    ];

    public static function getCurrentWeekIdentifier()
    {
        return date('Y-W');
    }

    public static function canRegister($email)
    {
        return !self::where('email', $email)
            ->where('week_identifier', self::getCurrentWeekIdentifier())
            ->exists();
    }
}
