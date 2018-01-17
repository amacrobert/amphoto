<?php

namespace AppBundle\Admin;

use AppBundle\Admin\AssetAwareAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class FreebieAdmin extends AssetAwareAdmin {

    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper
            ->with('General', ['class' => 'col-md-5'])
                ->add('name', null, ['help' => 'In addition to being used on the page and in communications, the name will be kebab-cased and used in the URL of the download page (so try not to change it).'])
                ->add('banner_uri', 'choice', ['choices' => $this->getAssets('images'), 'help' => 'The image used in the full-width page banner. Top and bottom may crop, so make sure the focus of the image is in the center.', 'required' => false])
                ->add('uri', 'choice', ['choices' => $this->getAssets('downloads'), 'help' => 'The file sent as an attachment with the email', 'required' => false])
                ->add('filename', null, ['help' => 'The attachment filename. Does not need to match filename in the uri.'])
            ->end()
            ->with('Content', ['class' => 'col-md-7'])
                ->add('body', null, ['help' => 'HTML content of the download page, below the banner and before the email input.', 'attr' => ['style' => 'min-height: 150px;']])
                ->add('email_body', null, ['help' => 'HTML email body sent to user when they request the download.', 'attr' => ['style' => 'min-height: 150px;']])
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
            ->add('id')
            ->addIdentifier('name')
            ->add('_action', null, ['actions' => [
                'show' => [],
                'edit' => [],
                'delete' => [],
            ]])
        ;
    }
}
