<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetData extends Model
{
    use HasFactory;

    protected $table = 'budget_data';

    protected $fillable = [
        'userID',
        'categoryID',
        'budgetLimit',
        'budgetNotes',
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