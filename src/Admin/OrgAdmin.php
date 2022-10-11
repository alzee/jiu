<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class OrgAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('name')
            ->add('contact')
            ->add('phone')
            ->add('address')
            ->add('district')
            ->add('type')
            ->add('voucher')
            ->add('discount')
            ->add('withdrawable')
            ->add('longitude')
            ->add('latitude')
            ->add('img')
            ->add('updatedAt')
            ->add('withdrawing')
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('name')
            ->add('contact')
            ->add('phone')
            ->add('address')
            ->add('district')
            ->add('type')
            ->add('voucher')
            ->add('discount')
            ->add('withdrawable')
            ->add('longitude')
            ->add('latitude')
            ->add('img')
            ->add('updatedAt')
            ->add('withdrawing')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id')
            ->add('name')
            ->add('contact')
            ->add('phone')
            ->add('address')
            ->add('district')
            ->add('type')
            ->add('voucher')
            ->add('discount')
            ->add('withdrawable')
            ->add('longitude')
            ->add('latitude')
            ->add('img')
            ->add('updatedAt')
            ->add('withdrawing')
            ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('name')
            ->add('contact')
            ->add('phone')
            ->add('address')
            ->add('district')
            ->add('type')
            ->add('voucher')
            ->add('discount')
            ->add('withdrawable')
            ->add('longitude')
            ->add('latitude')
            ->add('img')
            ->add('updatedAt')
            ->add('withdrawing')
            ;
    }
}
