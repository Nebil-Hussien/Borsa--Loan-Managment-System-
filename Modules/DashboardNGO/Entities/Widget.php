<?php

namespace Modules\DashboardNGO\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Widget extends Model
{
    use HasFactory;

    protected $fillable = [];
    public $table = "widgets";
    
    protected static function newFactory()
    {
        return \Modules\DashboardNGO\Database\factories\WidgetFactory::new();
    }
}
