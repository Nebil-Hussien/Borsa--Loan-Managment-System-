<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class KYC extends Model
{
    protected $fillable = [];
    public $table = "kyc";
    public $timestamps = false;
}