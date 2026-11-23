<?php

namespace App\Services;

use Aliziodev\Biteship\Facades\Biteship; // atau gunakan SDK resmi

class BiteshipService
{
    public function getRates(array $payload): array
    {
        return Biteship::rates($payload);
    }

    public function createOrder(array $data)
    {
        return Biteship::order($data);
    }
}