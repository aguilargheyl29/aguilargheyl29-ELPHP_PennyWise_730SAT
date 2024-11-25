<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseData extends Model
{
    use HasFactory;

    protected $table = 'expense_data';

    protected $fillable = [
        'userID',
        'categoryID',
        'expenseName',
        'expenseAmount',
        'expenseDescription',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryID', 'categoryID');
    }
}