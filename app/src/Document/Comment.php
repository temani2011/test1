<?php

namespace App\Document;

use DateTime;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Date;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
/**
 * @MongoDB\EmbeddedDocument
 * @MongoDB\HasLifecycleCallbacks
 */
class Comment
{
    /**
     * @MongoDb\Field(type="string")
     */
    protected $slug;
    /**
     * @MongoDb\Field(type="date")
     */
    protected $createdAt;
    /**
     * @MongoDb\Field(type="hash")
     */
    protected $author;
    /**
     * @MongoDb\Field(type="string")
     */
    protected $text;

    public function __construct()
    {
        $this->author = [];
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        //return $this;
    }

    /**
     * @MongoDB\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new DateTime('NOW');
    }

    public function getCreatedAt()
    {
        return $this->createdAt->format('d.m.Y');
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }

}