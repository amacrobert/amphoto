<?php

namespace AppBundle\Entity;

class Freebie {

    private $id;
    private $name;
    private $banner_uri;
    private $body;
    private $uri;
    private $filename;
    private $email_body;

    public function __toString() {
        return $this->getName() ?: 'New Freebie';
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getBannerUri() {
        return $this->banner_uri;
    }

    public function setBannerUri($banner_uri) {
        $this->banner_uri = $banner_uri;
        return $this;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function getUri() {
        return $this->uri;
    }

    public function setUri($uri) {
        $this->uri = $uri;
        return $this;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function setFilename($filename) {
        $this->filename = $filename;
        return $this;
    }

    public function getEmailBody() {
        return $this->email_body;
    }

    public function setEmailBody($email_body) {
        $this->email_body = $email_body;
        return $this;
    }
}
