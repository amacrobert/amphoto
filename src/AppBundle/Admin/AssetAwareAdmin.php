<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

abstract class AssetAwareAdmin extends AbstractAdmin {

    protected function getAssets($web_dir = '') {

        $results = $this->getDirContents(__DIR__ . '/../../../web/' . $web_dir);

        foreach ($results as $index => $result) {
            $results[$index] = substr($result, strpos($result, '/web/') + 4);
        }

        return array_combine($results, $results);
    }

    private function getDirContents($dir, &$results = []){
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                $this->getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

}
