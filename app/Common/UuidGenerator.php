<?php

namespace App\Common;

use App\Common\IdGenerator;
use Illuminate\Support\Str;

final class UuidGenerator implements IdGenerator
{
    public function generate(): string
    {
        return (string) Str::uuid();
    }
}
