<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Bucket;
use App\Document\Comment;
use Doctrine\ODM\MongoDB\DocumentManager;
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
use Cocur\Slugify\Slugify;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
/**
 * @Rest\Route("/api")
 */
final class CommentController extends AbstractController
{
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

    public function __construct(DocumentManager $dm,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator){
        $this->dm = $dm;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Rest\Post("/comment")
     * @param Request $request
     * @return JsonResponse
     */

    public function commentPost(Request $request) : JsonResponse
    {

        $user = $this->getUser();
        $pid = $request->request->get('pid');
        $comment = new Comment();
        $entityAsArray = $this->serializer->normalize($user, null, ['groups' => ['User_default']]);
        $comment->setText($request->request->get('text'))
            ->setAuthor($entityAsArray);
        $bucket = $this->dm->getRepository(Bucket::class)->findBy(['postId'=>$pid], ['comments.createdAt' => 'DESC']);
        if(!$bucket) {
            $bucket = new Bucket($pid, 0); // comments are empty
            $this->commitChanges($bucket, true);
        }
        else if($bucket[0]->getCount() >= 50) $bucket = new Bucket($pid, $bucket[0]->getPage()); // new bucket page
        else $bucket = $bucket[0]; // current bucket
        $comment->setSlug($bucket->getId() . "_" . $pid . "_" . $bucket->getCount());
        $bucket->addComments($comment);
        $this->commitChanges($bucket, true);
        $data = $this->serializer->serialize($comment, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/comment/slug={slug}")
     * @param string $slug
     * @return JsonResponse
     */
    public function commentGet(string $slug) : JsonResponse
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
    public function commentGetAll(string $pid) : JsonResponse
    {
        $buckets = $this->dm->getRepository(Bucket::class)->findBy(['postId' => $pid]);
        if(!$buckets) throw new BadRequestHttpException("Comments are empty");
        $comments = [];
        foreach ($buckets as $bucket)
            foreach ($bucket->getComments() as $comment)
                $comments[] = $comment;
        //           array_push($comments, $bucket->getComments());
        $data = $this->serializer->serialize($comments, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/comment/slug={slug}")
     * @return JsonResponse
     */
    public function commentDeleteBySlug(string $slug) : JsonResponse
    {
        $buckets = $this->dm->getRepository(Bucket::class)->findOneBy(['comments.slug' => $slug]);
        if(!$buckets) throw new BadRequestHttpException("Comments are empty");
        foreach ($buckets->getComments() as $comments)
            if($comments->getSlug()==$slug)
                $comment = $comments;
        $buckets->removeComments($comment);
        $this->commitChanges($buckets, false);
        $data = $this->serializer->serialize($buckets, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/comment/{pid}")
     * @return JsonResponse
     */
    public function commentDeleteByPostId(string $pid) : JsonResponse
    {
        $buckets = $this->dm->getRepository(Bucket::class)->findBy(['postId' => $pid]);
        if(!$buckets) throw new BadRequestHttpException("Comments are empty");
        foreach ($buckets as $bucket)
            $this->commitChanges($bucket, false);
        $data = $this->serializer->serialize($buckets, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    public function commitChanges(Bucket $bucket, bool $isPersist){
        try {
            ($isPersist) ? $this->dm->persist($bucket) : $this->dm->remove($bucket);
            $this->dm->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
