<?php

namespace App\Classes\DTO;

use Illuminate\Contracts\Support\Arrayable;

class ZohoCRMDealDTO implements Arrayable
{
    /**
     * @param string $dealName
     * @param string $dealStage
     */
    public function __construct(
        public string $dealName,
        public string $dealStage,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Deal_Name' => $this->dealName,
            'Stage' => $this->dealStage,
        ];
    }
}
