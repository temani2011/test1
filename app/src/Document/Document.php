<?php

namespace App\Document;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Date;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
/**
 * @MongoDB\HasLifecycleCallbacks
 */
class Document
{
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
    protected $path;

    /**
     * @MongoDb\Field(type="string")
     */
    protected $filename;

    public function __construct()
    {
        $this->author = [];
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
        return $this->createdAt;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    public function getFileName()
    {
        return $this->filename;
    }

    public function setFileName($fileName)
    {
        $this->filename = $fileName;
        return $this;
    }

}