<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Book;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class BookAdmin extends AbstractAdmin
{
    public function getNewInstance(): object
    {
        return Book::create();
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            // ->add('id')
            ->add('title')
            ->add('image')
            ->add('description')
            ->add('score.value');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('title')
            ->add('image')
            ->add('description')
            ->add('score.value')
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
            ->add('id', null, ['disabled' => true])
            ->add('title')
            ->add('image')
            ->add('description')
            ->add('score.value')
            ->add('categories');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('title')
            ->add('image')
            ->add('description')
            ->add('score.value')
            ->add('categories');
    }
}
