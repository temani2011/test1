<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Bucket;
use App\Document\Comment;
use App\Entity\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
/**
 * @Rest\Route("/api")
 */
final class CommentController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $em,
                                DocumentManager $dm,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator){
        $this->em = $em;
        $this->dm = $dm;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Rest\Post("/comment")
     * @param Request $request
     * @return JsonResponse
     */

    public function mongoPost(Request $request) : JsonResponse
    {
        $pid = $request->request->get('pid');
        $comment = new Comment();
        $comment->setText($request->request->get('text'))
            ->setAuthor($request->request->get('author'));
        $bucket = $this->dm->getRepository(Bucket::class)->findBy(['postId'=>$pid], ['comments.createdAt' => 'DESC']);
        if(!$bucket) $bucket = new Bucket($pid, 0); // comments are empty
        else if($bucket[0]->getCount() >= 50) $bucket = new Bucket($pid, $bucket[0]->getPage()); // new bucket page
        else $bucket = $bucket[0]; // current bucket
        $comment->setSlug($bucket->getId() . "_" . $pid . "_" . $bucket->getCount());
        $bucket->addComments($comment);
        try {
            $this->dm->persist($bucket);
            $this->dm->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
//        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $comment->getAuthor()]);
//        $comment->setAuthor($user->getLogin());
        $data = $this->serializer->serialize($comment, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/comment/slug={slug}")
     * @param string $slug
     * @return JsonResponse
     */
    public function mongoGet(string $slug) : JsonResponse
    {
        $bucket = $this->dm->getRepository(Bucket::class)->findOneBy(['comments.slug' => $slug]);
        if(!$bucket) throw new BadRequestHttpException("Comment not found");
        $fcomment = [];
        foreach ($bucket->getComments() as $comment)
            if($comment->getSlug() == $slug)
                $fcomment = $comment;
        $data = $this->serializer->serialize($fcomment, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/comment/{pid}")
     * @return JsonResponse
     */
    public function mongoGetAll(string $pid) : JsonResponse
    {
        $buckets = $this->dm->getRepository(Bucket::class)->findBy(['postId' => $pid]);
        if(!$buckets) throw new BadRequestHttpException("Comments are empty");
        $comments = [];
        $users = [];
        foreach ($buckets as $bucket)
            $comments = $bucket->getComments();
//        foreach ($comments as $comment){
//            $user = $this->em->getRepository(User::class)->findOneBy(['id' => $comment->getAuthor()]);
//            $comment->setAuthor($user->getLogin());
//        }
        $data = $this->serializer->serialize($comments, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

}
