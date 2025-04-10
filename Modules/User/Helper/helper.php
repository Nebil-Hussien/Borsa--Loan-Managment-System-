<?php

namespace Modules\User\Helper;

use Request;

class Helper
{
    public function generate_loan_user_id($prefix)
    {
        return $prefix . mt_rand(100000, 999999);
    }
}