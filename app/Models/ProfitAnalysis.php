<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'total_sales',
        'total_cost',
        'gross_profit',
        'total_expenses',
        'net_profit',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'gross_profit' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];

    public static function getCurrentMonth()
    {
        return static::where('year', now()->year)
                     ->where('month', now()->month)
                     ->first();
    }

    public static function getYearlyData($year = null)
    {
        $year = $year ?? now()->year;
        return static::where('year', $year)
                     ->orderBy('month')
                     ->get();
    }
}