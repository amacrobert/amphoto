<?php

namespace AppBundle\Admin;

use AppBundle\Admin\AssetAwareAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PortfolioAdmin extends AssetAwareAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
            ->with('Configuration', ['class' => 'col-md-8'])
                ->add('name')
                ->add('machine_name')
                ->add('description')
                ->add('banner', 'choice', ['choices' => $this->getAssets('images'), 'required' => false])
            ->end()
            ->with('Menu', ['class' => 'col-md-4'])
                ->add('listed', 'checkbox', ['required' => false])
                ->add('ordinal')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
            ->add('id')
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
            ->add('listed')
            ->addIdentifier('name')
            ->add('machine_name')
            ->add('_action', null, ['actions' => [
                'show' => [],
                'edit' => [],
                'delete' => [],
            ]])
        ;
    }
}
