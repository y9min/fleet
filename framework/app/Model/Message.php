<?php

/*
@copyright

Fleet Manager v7.1.2

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

*/

namespace App\Model;

use App\Model\BaseUuidModel;

class Message extends BaseUuidModel
{
    protected $table = "messages";
    protected $fillable = ['from_user', 'to_user', 'content', 'read_at'];

    public function fromUser()
    {
        return $this->hasOne("App\Model\User", "id", "from_user")->withTrashed();
    }

    public function toUser()
    {
        return $this->hasOne("App\Model\User", "id", "to_user")->withTrashed();
    }

}
