<?php

namespace App\Controller\Admin;

use App\Entity\Node;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
// use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Vich\UploaderBundle\Form\Type\VichImageType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Admin\Field\CKEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use Symfony\Component\Form\FormInterface;

class NodeCrudController extends AbstractCrudController
{
    private $tags = ['轮播图' => 0, '推荐' => 1, '企业简介' => 2];

    public static function getEntityFqcn(): string
    {
        return Node::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        yield TextField::new('title');
        yield ImageField::new('img')
            ->onlyOnIndex()
            ->setBasePath('img/node/')
            ->setUploadDir('public/img/node/');
        yield TextField::new('imageFile')
            ->hideOnIndex()
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions(['allow_delete' => false])
            ;
        yield ChoiceField::new('tag')
            ->setChoices($this->tags)
            // ->allowMultipleChoices()
        ;
        yield CKEditorField::new('body')
            ->hideOnIndex()
            // ->setFormType(CKEditorType::class)
          ;
        // yield TextEditorField::new('body')
        //     ->hideOnIndex()
        //     ->setTrixEditorConfig([
        //         'attachments' => [
        //             'preview' => [
        //                 'presentation' => "gallery",
        //                 'caption' => [
        //                     'name' => true,
        //                     'size' => true,
        //                 ]
        //             ],
        //             'file' => [
        //                 'caption' => [
        //                     'size' => true,
        //                 ]
        //             ],
        //         ],
        //         'css' => [
        //             'attachment' => 'admin_file_field_attachment',
        //         ],
        //     ])
        // ;
        yield DateTimeField::new('date')
            ->onlyOnIndex();
    }

    public function configureActions(Actions $actions): Actions
    {
        if ($this->isGranted('ROLE_HEAD')) {
            return $actions
                ;
        } else {
            return $actions
                ->disable(Action::DELETE, Action::NEW, Action::EDIT, Action::DETAIL, Action::INDEX)
            ;
        }
    }

    public function createNewForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        $b = $this->createNewFormBuilder($entityDto, $formOptions, $context);
        // $b->add('body', CKEditorType::class);
        $f = $b->getForm();
        return $f;
    }
}
