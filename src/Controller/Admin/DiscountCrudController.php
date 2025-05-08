<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Discount;
use App\Entity\DiscountCode;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class DiscountCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {}

    public static function getEntityFqcn(): string
    {
        return Discount::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $generateCodesAction = Action::new(
            name: 'generate-codes',
            label: 'Generate codes',
            icon: 'fas fa-random',
        )
            ->linkToCrudAction('generateCodes')
        ;

        return $actions
            ->add(Action::INDEX, $generateCodesAction)
        ;
    }

    #[AdminAction(routePath: '{entityId}/generate-codes', routeName: 'generate_codes')]
    public function generateCodes(
        AdminContext $context,
        EntityManagerInterface $em,
    ): RedirectResponse {
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $entityFqcn = self::getEntityFqcn();

        /** @var Discount $instance */
        $instance = $context->getEntity()->getInstance();

        if (!$instance instanceof $entityFqcn) {
            throw new RuntimeException("Entity is not an instance of {$entityFqcn}");
        }

        for ($i = 0; $i < $instance->getNumberOfCodes(); ++$i) {
            do {
                $code = self::generateSingleCode($instance->getCodePrefix());
            } while ($em->getRepository(DiscountCode::class)->findOneBy(['code' => $code]));

            $codeObj = (new DiscountCode())
                ->setCode($code)
                ->setDiscount($instance)
            ;

            $em->persist($codeObj);
            $em->flush();
        }

        return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
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
