<?php

declare(strict_types=1);

namespace App\Messenger\Handler;

use App\Entity\Discount;
use App\Messenger\Command\GenerateDiscountCodesCommand;
use App\Repository\DiscountCodeRepository;
use App\Repository\DiscountRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateDiscountCodesHandler
{
    private const int BATCH_INSERT_SIZE = 50;

    public function __construct(
        private readonly DiscountRepository $discountRepository,
        private readonly DiscountCodeRepository $discountCodeRepository,
    ) {}

    public function __invoke(GenerateDiscountCodesCommand $command): void
    {
        $discount = $this->discountRepository->find($command->discountId);

        if (!$discount) {
            return;
        }

        $codesNeeded = $this->codesNeeded($discount);
        while ($codesNeeded > 0) {
            $codes = [];

            for ($i = 0; $i < $codesNeeded && $i < self::BATCH_INSERT_SIZE; ++$i) {
                $codes[] = self::generateSingleCode(prefix: $discount->getCodePrefix());
            }

            $this->discountCodeRepository->insertInBatch(
                discountId: $discount->getId(),
                codes: $codes
            );

            $codesNeeded = $this->codesNeeded($discount);
        }
    }

    private static function generateSingleCode(string $prefix): string
    {
        $chars = array_flip(
            array_merge(range(0, 9), range('A', 'Z'))
        );

        $randomString = '';

        while (strlen($randomString) < 10) {
            $randomString .= array_rand($chars);
        }

        return (str_ends_with($prefix, '_') ? $prefix : ($prefix.'_')).$randomString;
    }

    private function codesNeeded(Discount $discount): int
    {
        // forcing reload of entity from db
        $discount = $this->discountRepository->find($discount->getId());

        $codesCount = $this->discountCodeRepository->countByDiscount($discount);

        return $discount->getNumberOfCodes() - $codesCount;
    }
}
