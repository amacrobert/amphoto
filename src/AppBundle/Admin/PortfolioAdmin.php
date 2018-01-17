<?php

namespace AppBundle\Admin;

use AppBundle\Admin\AssetAwareAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PortfolioAdmin extends AssetAwareAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
            ->add('ordinal')
            ->add('name')
            ->add('machine_name')
            ->add('description')
            ->add('banner', 'choice', ['choices' => $this->getAssets('images'), 'required' => false])
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
            ->add('id')
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
