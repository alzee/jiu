<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Org;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use App\Entity\Choice;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class BuyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $instance = $this->getContext()->getEntity()->getInstance();
        $user = $this->getUser();
        yield IdField::new('id')->onlyOnIndex();
        yield AssociationField::new('seller')
            ->hideWhenUpdating()
            ->setQueryBuilder(
                fn (QueryBuilder $qb) => $qb
                    ->andWhere('entity.id = :id')
                    ->setParameter('id', $user->getOrg())
            );
        yield AssociationField::new('seller')
            ->onlyWhenUpdating()
            ->setFormTypeOptions(['disabled' => 'disabled']);
        yield AssociationField::new('buyer')
            ->hideWhenUpdating()
            ->setQueryBuilder(
                fn (QueryBuilder $qb) => $qb
                    ->andWhere('entity.upstream = :userOrg')
                    ->setParameter('userOrg', $user->getOrg())
            );
        yield AssociationField::new('buyer')
            ->onlyWhenUpdating()
            ->setFormTypeOptions(['disabled' => 'disabled']);
        yield TextField::new('FirstProduct')
            ->onlyOnIndex()
            ;
        yield IntegerField::new('FirstProductQuantity')
            ->onlyOnIndex()
            ;
        yield CollectionField::new('orderItems')
            ->onlyWhenCreating()
            ->setFormTypeOptions(['required' => 'required'])
            ->useEntryCrudForm();
        yield CollectionField::new('orderItems')
            ->OnlyWhenUpdating()
            ->allowAdd(false)
            ->allowDelete(false)
            ->useEntryCrudForm();
        yield MoneyField::new('amount')
            ->setCurrency('CNY')
            ->onlyOnIndex();
        yield MoneyField::new('voucher')
            ->setCurrency('CNY')
            ->onlyOnIndex();
        // if (!is_null($instance)) {
        //     if ($instance->getStatus() > 3 || $instance->getSeller() != $user->getOrg()) {
        //         yield ChoiceField::new('status')
        //             ->setChoices(Choice::ORDER_STATUSES)
        //             ->hideWhenCreating()
        //             ->setFormTypeOptions(['disabled' => 'disabled']);
        //     } else {
        //         yield ChoiceField::new('status')
        //             ->setChoices(Choice::ORDER_STATUSES)
        //             ->hideWhenCreating();
        //     }
        // }
        // yield ChoiceField::new('status')
        //     ->setChoices(Choice::ORDER_STATUSES)
        //     ->onlyOnIndex();
        yield DateTimeField::new('date')->HideOnForm();
        yield TextareaField::new('note');
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $userOrg = $this->getUser()->getOrg()->getId();
        $response = $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $response
            ->andWhere("entity.buyer = $userOrg")
        ;
        return $response;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::EDIT)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $helpIndex = '?????????<b>?????????</b>?????????';
        $helpNew = '?????????<b>?????????</b>?????????<br/><b>?????????</b>????????????????????????<b>?????????</b>?????????????????????';
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setHelp('index', $helpIndex)
            ->setHelp('new', $helpNew)
            ->setPageTitle('index', 'Buy')
            ->setSearchFields(['seller.name', 'orderItems.product.name'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('date'))
        ;
    }
}
