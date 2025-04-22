<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'amount', 'currency', 'employee_id', 'campaign_id', 'status'
    ];
}
