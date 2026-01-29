<?php

namespace App\Domain\Company;

final class Company
{
    private string $id;
    private string $tradeName;

    public function __construct(string $id, string $tradeName)
    {
        $this->id = $id;
        $this->tradeName = $tradeName;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function tradeName(): string
    {
        return $this->tradeName;
    }
}
