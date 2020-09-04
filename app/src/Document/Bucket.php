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
     * @MongoDB\EmbedMany(targetDocument="App\Document\Comment")
     */
    protected $comments = [];

    public function __construct($id, $page)
    {
        $this->count = 0;
        $this->page = ++$page;
        $this->postId = $id;
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
     * @return postId
     */
    public function getPostId()
    {
        return $this->postId;
    }

    public function setPostId($postId)
    {
        $this->postId = $postId;
    }

    /**
     * @return page
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
     * @return count
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
     * @param Comment $comment
     * @return bool
     */
    public function addComments(Comment $comment) : bool
    {
        $this->comments[] = $comment;
        $this->count = $this->comments->count();
        return true;
    }

    /**
     * @param Comment $comment
     */
    public function removeComments(Comment $comment)
    {
        $this->comments->removeElement($comment);
        $this->count = $this->comments->count();
    }

    /**
     * @return ArrayCollection $comments
     */
    public function getComments()
    {
        return $this->comments;
    }
}

