<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'birthdate',
        'age',
        'age_type',
    ];

    public static function createPatient(string $firstName, string $lastName, $birthdate): static
    {
        $age = self::calculateAge($birthdate);
        $ageType = self::defineAgeType($birthdate);

        $patient = new static();
        $patient->first_name = $firstName;
        $patient->last_name = $lastName;
        $patient->birthdate = $birthdate;
        $patient->age = $age;
        $patient->age_type = $ageType;
        $patient->save();

        return $patient;
    }

    /**
     * @param Carbon $birthdate
     * @return float|int
     */
    public static function calculateAge(Carbon $birthdate): int|float
    {
        $now = Carbon::now();
        return $birthdate->diffInDays($now) < 30 ? $birthdate->diffInDays($now) : ($birthdate->diffInMonths($now) < 12 ? $birthdate->diffInMonths($now) : $birthdate->diffInYears($now));
    }

    /**
     * @param Carbon $birthdate
     * @return string
     */
    public static function defineAgeType(Carbon $birthdate): string
    {
        $now = Carbon::now();
        return $birthdate->diffInDays($now) < 30 ? 'день' : ($birthdate->diffInMonths($now) < 12 ? 'месяц' : 'год');
    }
}
