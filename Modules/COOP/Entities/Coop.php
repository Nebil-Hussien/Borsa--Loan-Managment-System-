<?php

namespace Modules\COOP\Entities;

use Illuminate\Database\Eloquent\Model;

class Coop extends Model
{
     protected $table = "coop";

    public function users()
    {
        return $this->hasMany(CoopUser::class, 'coop_id', 'id');
    }
}
