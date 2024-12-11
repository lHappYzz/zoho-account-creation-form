<?php

namespace App\Classes\DTO;

use Illuminate\Contracts\Support\Arrayable;

class ZohoCRMAccountDTO implements Arrayable
{
    /**
     * @param string $accountName
     * @param string $accountWebsite
     * @param string $accountPhone
     */
    public function __construct(
        public string $accountName,
        public string $accountWebsite,
        public string $accountPhone,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Account_Name' => $this->accountName,
            'Website' => $this->accountWebsite,
            'Phone' => $this->accountPhone,
        ];
    }
}
