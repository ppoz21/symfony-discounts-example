<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Discount;
use App\Messenger\Command\GenerateDiscountCodesCommand;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Messenger\MessageBusInterface;

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
        MessageBusInterface $messageBus,
    ): RedirectResponse {
        $entityFqcn = self::getEntityFqcn();

        /** @var Discount $instance */
        $instance = $context->getEntity()->getInstance();

        if (!$instance instanceof $entityFqcn) {
            throw new RuntimeException("Entity is not an instance of {$entityFqcn}");
        }

        $messageBus->dispatch(
            new GenerateDiscountCodesCommand(
                discountId: $instance->getId()
            )
        );

        return $this->redirect($this->adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
    }
}
