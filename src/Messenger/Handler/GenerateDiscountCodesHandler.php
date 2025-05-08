<?php

declare(strict_types=1);

namespace App\Messenger\Handler;

use App\Entity\DiscountCode;
use App\Messenger\Command\GenerateDiscountCodesCommand;
use App\Repository\DiscountCodeRepository;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateDiscountCodesHandler
{
    public function __construct(
        private readonly DiscountRepository $discountRepository,
        private readonly DiscountCodeRepository $discountCodeRepository,
        private readonly EntityManagerInterface $em
    ) {}

    public function __invoke(GenerateDiscountCodesCommand $command): void
    {
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $discount = $this->discountRepository->find($command->discountId);

        if (!$discount) {
            return;
        }

        for ($i = 0; $i < $discount->getNumberOfCodes(); ++$i) {
            do {
                $code = self::generateSingleCode($discount->getCodePrefix());
            } while ($this->discountCodeRepository->findOneBy(['code' => $code]));

            $codeObj = (new DiscountCode())
                ->setCode($code)
                ->setDiscount($discount)
            ;

            $this->em->persist($codeObj);
            $this->em->flush();
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
}
