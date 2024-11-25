<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'categoryIcon',
        'categoryName',
        'categoryDescription',
    ];

    public function settings()
    {
        return $this->hasMany(Setting::class, 'categoryID', 'categoryID');
    }

    public function expenses()
    {
        return $this->hasMany(ExpenseData::class, 'categoryID', 'categoryID');
    }

    public function budgets()
    {
        return $this->hasMany(BudgetData::class, 'categoryID', 'categoryID');
    }
}

