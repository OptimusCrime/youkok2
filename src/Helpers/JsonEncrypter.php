<?php
declare(strict_types=1);

namespace Youkok\Helpers;

use Illuminate\Contracts\Encryption\Encrypter;

class JsonEncrypter implements Encrypter
{
    public function encrypt($value, $serialize = true)
    {
        return json_encode($value);
    }

    public function decrypt($payload, $unserialize = true)
    {
        return json_decode($payload);
    }
}
