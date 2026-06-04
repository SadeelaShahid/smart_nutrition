<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    protected $fillable = [
        'user_id',
        'age',
        'height',
        'weight',
        'bmi',
        'bmi_category',
        'meal_plan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}