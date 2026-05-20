<?php

namespace App\Services\Xendit;

interface XenditServiceInterface
{
    public function sessionPayment(array $payload);
}
