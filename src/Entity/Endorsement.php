<?php

namespace App\Entity;

class Endorsement {

    private $id;
    private $body;
    private $author;

    public function __toString() {
        return $this->getBody() ? substr($this->getBody(), 0, 200) : 'New Endorsement';
    }

    public function getId() {
        return $this->id;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
        return $this;
    }
}
