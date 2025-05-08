<?php

declare(strict_types=1);

namespace App\Messenger\Command;

final readonly class GenerateDiscountCodesCommand
{
    public function __construct(public int $discountId) {}
}
