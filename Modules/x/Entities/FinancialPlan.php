<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class FinancialPlan extends Model
{
    protected $fillable = [];
    public $table = "financial_plan";
    public $timestamps = false;
}