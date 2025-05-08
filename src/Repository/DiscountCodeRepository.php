<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Discount;
use App\Entity\DiscountCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DiscountCode>
 */
class DiscountCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiscountCode::class);
    }

    public function insertInBatch(int $discountId, array $codes): void
    {
        $values = implode(
            separator: ',',
            array: array_map(
                callback: static fn (string $code) => sprintf(
                    "(nextval('discount_code_id_seq'), '%s', '%s', false)",
                    $discountId,
                    $code
                ),
                array: $codes
            )
        );

        $this->getEntityManager()->getConnection()->executeQuery(
            "INSERT INTO discount_code (id, discount_id, code, used) VALUES {$values} ON CONFLICT DO NOTHING"
        );
    }

    public function countByDiscount(Discount $discount): int
    {
        return $this->createQueryBuilder('dc')
            ->select('COUNT(dc.id)')
            ->andWhere('dc.discount = :discount')
            ->setParameter('discount', $discount)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
