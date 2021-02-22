<?php
namespace App\Listeners;

use App\Entity\User;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

class MyEventSubscriber
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $comment = $eventArgs->getDocument();

        if (!$comment instanceof Comment) {
            return;
        }

        $dm = $eventArgs->getDocumentManager();
        $commentReflProp = $dm->getClassMetadata(Comment::class)
            ->reflClass->getProperty('author');
        $commentReflProp->setAccessible(true);
        $commentReflProp->setValue(
            $comment, $this->em->getReference(User::class, $comment->getAuthor())
        );
    }
}