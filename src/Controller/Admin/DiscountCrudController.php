<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Discount;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class DiscountCrudController extends AbstractCrudController
{
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

    public function generateCodes(
        AdminContext $adminContext
    ): RedirectResponse {
        $entityFqcn = self::getEntityFqcn();

        /** @var Discount $instance */
        $instance = $adminContext->getEntity()->getInstance();

        if (!$instance instanceof $entityFqcn) {
            throw new RuntimeException("Entity is not an instance of {$entityFqcn}");
        }

        return $this->redirect($adminContext->getReferrer());
    }
}
