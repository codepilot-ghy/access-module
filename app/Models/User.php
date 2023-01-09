<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{
    
    /*
    --------------------------------------------------------------
    READ
    --------------------------------------------------------------
    */

    public function getUser(int $userId)
    {
        return DB::table('user as u')
            ->where('id', $userId)
            ->first();
    }


}
