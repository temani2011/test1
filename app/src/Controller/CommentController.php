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
    public function mongoPost(Request $request) : JsonResponse
    {
        $comment = new Comment();
        $bucket = new Bucket();
        $comment->setText($request->request->get('text'))
            ->setAuthor($request->request->get('author'));
        $bucket->addComments($comment);

        //$dm = $this->get('doctrine_mongodb')->getManager();
        try {
            $this->dm->persist($comment);
            $this->dm->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e);
        }
        $data = $this->serializer->serialize($comment, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/comment")
     * @return JsonResponse
     */
    public function mongoGet() : JsonResponse
    {
        $comment = $this->dm->getRepository(Bucket::class)->findBy([]);
        $data = $this->serializer->serialize($comment, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

}
