<?php

namespace App\Document;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Date;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @MongoDB\EmbeddedDocument
 * @MongoDB\HasLifecycleCallbacks
 */
class Document
{
    /**
     * @MongoDB\Id(strategy="UUID")
     * @Groups({"Catalog_default"})
     */
    protected $id;

    /**
     * @MongoDb\Field(type="date")
     * @Groups({"Catalog_default"})
     */
    protected $createdAt;
    /**
     * @MongoDb\Field(type="hash")
     * @Groups({"Catalog_default"})
     */
    protected $author;

    /**
     * @MongoDb\Field(type="string")
     * @Groups({"Catalog_default"})
     */
    protected $path;

    /**
     * @MongoDb\Field(type="string")
     * @Groups({"Catalog_default"})
     */
    protected $filename;

    public function __construct()
    {
        $this->author = [];
    }

    /**
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
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