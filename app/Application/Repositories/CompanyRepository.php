<?php

namespace App\Application\Repositories\Company;

use App\Domain\Company\Company;

interface CompanyRepository
{
    public function findById(string $id): Company;
}
