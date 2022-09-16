<?php

namespace App\Controller\Admin;

use App\Entity\Returns;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ReturnsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Returns::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $user = $this->getUser();
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('sender')
                ->setQueryBuilder(
                    fn (QueryBuilder $qb) => $qb
                        ->andWhere('entity.upstream = :userOrg')
                        ->andWhere('entity.type != 3')
                        ->setParameter('userOrg', $user->getOrg())
                ),
            AssociationField::new('recipient')
                ->setQueryBuilder(
                    fn (QueryBuilder $qb) => $qb->andWhere('entity.id = :id')->setParameter('id', $user->getOrg())
                ),
            CollectionField::new('returnItems')
                ->OnlyOnForms()
                ->setFormTypeOptions(['required' => 'required'])
                ->useEntryCrudForm(),
            MoneyField::new('amount')
                ->setCurrency('CNY')
                ->onlyOnIndex(),
            MoneyField::new('voucher')
                ->setCurrency('CNY')
                ->onlyOnIndex(),
            ChoiceField::new('status')->setChoices(['Pending' => 0, 'Success' => 5]),
            DateTimeField::new('date')->HideOnForm(),
            TextareaField::new('note'),
        ];
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $userOrg = $this->getUser()->getOrg()->getId();
        $response = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response->andWhere("entity.sender = $userOrg")->orWhere("entity.recipient = $userOrg");
        return $response;
    }
}
