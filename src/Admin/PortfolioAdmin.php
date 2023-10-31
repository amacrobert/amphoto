<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PortfolioAdmin extends AssetAwareAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Configuration', ['class' => 'col-md-8'])
            ->add('name')
            ->add('machine_name')
            ->add('description')
            ->add('banner', ChoiceType::class, ['choices' => $this->getAssets('images'), 'required' => false])
            ->add('endorsements')
            ->end()
            ->with('Menu', ['class' => 'col-md-4'])
            ->add('listed', null, ['required' => false])
            ->add('ordinal')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('id')
            ->add('name');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('listed')
            ->addIdentifier('name')
            ->add('machine_name')
            ->add('_actions', null, ['actions' => [
                'show' => [],
                'edit' => [],
                'delete' => [],
            ]]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('name');
    }
}
