<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Endorsement
 *
 * @ORM\Table(name="endorsement")
 * @ORM\Entity
 */
class Endorsement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string|null
     *
     * @ORM\Column(name="author", type="string", nullable=true)
     */
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
