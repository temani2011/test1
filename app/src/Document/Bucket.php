<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Date;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Id;

/**
 * @MongoDB\Document(collection="buckets")
 * @MongoDB\HasLifecycleCallbacks
 */
class Bucket
{
    /**
     * @MongoDB\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @MongoDb\Field(type="string")
     */
    protected $postId;

    /**
     * @MongoDb\Field(type="int")
     */
    protected $page;

    /**
     * @MongoDb\Field(type="int")
     */
    protected $count;

    /**
     * @@MongoDB\EmbedMany(targetDocument="App\Entity\Comment")
     */
    private $comments = [];

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return page $id
     */
    public function getPage()
    {
        return $this->page;
    }
    public function setPage($page)
    {
        $this->page = $page;
    }
    /**
     * @return count $id
     */
    public function getCount()
    {
        return $this->count;
    }
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @param Comment $comments
     */
    public function addComments(Comment $comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * @param Comment $comments
     */
    public function removeComments(Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * @return Collection $comments
     */
    public function getComments()
    {
        return $this->comments;
    }
}

