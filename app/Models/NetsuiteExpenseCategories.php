<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetsuiteExpenseCategories extends Model
{
    protected $table = 'netsuite_expense_categories';
    // protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'external_id',
        'name',
        'description',
        'account',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
