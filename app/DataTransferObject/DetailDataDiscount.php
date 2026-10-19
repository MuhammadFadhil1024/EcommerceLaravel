<?php

final readonly class DetailDataDiscount
{
    public function __construct(
        public string $name,
        public string $description,
        public string $code,
        public string $level,
        public string $valueType,
        public string $value,
        public string $maxDiscountAmount,
        public string $minPurchaseAmount,
    ) {

    }
}