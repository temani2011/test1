<?php

namespace App\Document;

use DateTime;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Date;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @MongoDB\Document(collection="catalogs")
 * @MongoDB\HasLifecycleCallbacks
 */
class Catalog
{
    /**
     * @MongoDB\Id(strategy="UUID")
     * @Groups({"Catalog_default"})
     */
    protected $id;

    /**
     * @MongoDb\Field(type="hash")
     * @Groups({"Catalog_default"})
     */
    protected $author;

    /**
     * @MongoDb\Field(type="string")
     * @Groups({"Catalog_default"})
     */
    protected $name;

    /**
     * @MongoDb\Field(type="date")
     * @Groups({"Catalog_default"})
     */
    protected $createdAt;

    /**
     * @MongoDb\Field(type="string")
     * @Groups({"Catalog_default"})
     */
    protected $slug;

    /**
     * @MongoDb\Field(type="int")
     * @Groups({"Catalog_default"})
     */
    protected $level;

    /**
     * @MongoDb\Field(type="int")
     * @Groups({"Catalog_default"})
     */
    protected $childsCount;

    /**
     * @MongoDb\Field(type="int")
     * @Groups({"Catalog_default"})
     */
    protected $documentsCount;

    // @MongoDB\ReferenceOne(targetDocument="App\Document\Catalog")
    // @MongoDb\EmbedOne(targetDocument="App\Document\Catalog")

    /**
     * @MaxDepth(1)
     * @MongoDb\ReferenceOne(targetDocument=Catalog::class)
     * @MongoDb\Index
     * @Groups({"Catalog_parent"})
     */
    protected $parent;

    // @MongoDB\ReferenceMany(targetDocument="App\Document\Catalog")
    // @MongoDb\EmbedMany(targetDocument="App\Document\Catalog")
    /**
     * @MaxDepth(5)
     * @MongoDB\ReferenceMany(targetDocument=Catalog::class)
     * @MongoDb\Index
     * @Groups({"Catalog_childs"})
     */
    protected $childs = [];

    /**
     * @MongoDb\EmbedMany(targetDocument="App\Document\Document")
     * @MongoDb\Index
     * @Groups({"Catalog_default"})
     */
    protected $documents = [];

    public function __construct()
    {
        $this->documentsCount = 0;
        $this->childsCount = 0;
        $this->author = [];
        $this->documents = new ArrayCollection();
        $this->childs = new ArrayCollection();
    }

    /**
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return name
     */
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return slug
     */
    public function getSlug()
    {
        return $this->slug;
    }
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return level
     */
    public function getLevel()
    {
        return $this->level;
    }
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @MongoDB\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new DateTime('NOW');
        $this->level = count(explode('/', $this->getSlug()));
    }


    public function getCreatedAt()
    {
        return $this->createdAt->format('d.m.Y');
    }

    public function getChildsCount()
    {
        return $this->childsCount;
    }
    public function setChildsCount($childsCount)
    {
        $this->childsCount = $childsCount;
    }

    /**
     * @return documentsCount
     */
    public function getDocumentsCount()
    {
        return $this->documentsCount;
    }
    public function setDocumentsCount($documentsCount)
    {
        $this->documentsCount = $documentsCount;
    }

    /**
     * @return Catalog parent
     */
    public function getParent()
    {
        return $this->parent;
    }
    public function setParent(Catalog $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param Catalog $catalog
     * @return bool
     */
    public function addChild(Catalog $catalog) : bool
    {
        $this->childs[] = $catalog;
        $this->childsCount = $this->childs->count();
        return true;
    }

    /**
     * @param Catalog $catalog
     */
    public function removeCatalog(Catalog $catalog)
    {
        $this->childs->removeElement($catalog);
        $this->childsCount = $this->childs->count();
    }

    /**
     * @return ArrayCollection childs
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * @param Document $document
     * @return bool
     */
    public function addDocument(Document $document) : bool
    {
        $this->documents[] = $document;
        $this->documentsCount = $this->documents->count();
        return true;
    }

    /**
     * @param Document $document
     */
    public function removeDocument(Document $document)
    {
        $this->documents->removeElement($document);
        $this->documentsCount = $this->documents->count();
    }

    /**
     * @return ArrayCollection documents
     */
    public function getDocuments()
    {
        return $this->documents;
    }
}

