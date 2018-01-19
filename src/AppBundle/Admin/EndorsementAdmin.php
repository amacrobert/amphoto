<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class EndorsementAdmin extends AbstractAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
            ->add('body')
            ->add('author')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $datagridMapper
            ->add('id')
            ->add('body')
            ->add('author')
        ;
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper
            ->add('body')
            ->add('author')
            ->add('_action', null, ['actions' => [
                'show' => [],
                'edit' => [],
                'delete' => [],
            ]])
        ;
    }
}
