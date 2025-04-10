<?php

namespace Modules\COOP\Entities;

use Illuminate\Database\Eloquent\Model;

class CoopUser extends Model
{
    protected $table = "coop_users";
    public $timestamps = false;

    public function coop()
    {
        return $this->hasOne(Coop::class, 'id', 'coop-id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
