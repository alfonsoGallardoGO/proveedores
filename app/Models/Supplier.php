<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'external_id',
        'uid',
        'name',
        'company_name',
        'legal_name_company',
        'is_individual',
        'type',
        'phone',
        'category',
        'tax',
        'email',
        'currency',
        'default_accounts_payable',
        'payment_terms',
        'balance',
        'inactive',
        'main_subsidiary',
        'address',
        'prepaid_balance',
        'unbilled_orders',
        'credit_limit',
        'filepath'
    ];
}
