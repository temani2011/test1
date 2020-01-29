<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\News;
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
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
/**
 * @Rest\Route("/api")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
final class NewsController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SerializerInterface */
    private $serializer;

    private $psh;

    private $ab;

    private $SessionInterface, $PdoSessionHandler;

    public function __construct(SessionInterface $SessionInterface,
                                PdoSessionHandler $PdoSessionHandler,
                                EntityManagerInterface $em,
                                SerializerInterface $serializer)
    {

        $this->SessionInterface = $SessionInterface;
        $this->PdoSessionHandler = $PdoSessionHandler;
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * Create news article
     * @throws BadRequestHttpException
     * @Rest\Post("/news", name="createNews")
     * @IsGranted("ROLE_FOO")
     * @param Request $request
     * @return JsonResponse
     */
    public function postNewsAction(Request $request): JsonResponse
    {
        $title = $request->get('title');
        $text = $request->get('text');
        if (empty($title)) {
            throw new BadRequestHttpException('title cannot be empty');
        }
        if (empty($text)) {
            throw new BadRequestHttpException('text cannot be empty');
        }
        $news = new News();
        $news->setTitle($title);
        $news->setText($text);
        $news->setUser($this->getUser());
        $this->em->persist($news);
        $this->em->flush();
        $data = $this->serializer->serialize($news, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Get all news articles
     * @Rest\Get("/news", name="getAllNewsAction")
     */
    public function getAllNewsAction(): JsonResponse
    {
        $posts = $this->em->getRepository(News::class)->findBy([],['created' => 'DESC']);
        $data = $this->serializer->serialize($posts, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Get news article by id
     * @Rest\Get("/news/{id}", name="getNewsAction")
     * @param UuidInterface $request
     * @return JsonResponse
     */
    public function getNewsAction(UuidInterface $id): JsonResponse
    {
        $posts = $this->em->getRepository(News::class)->findById(['id' => $id],['created' => 'DESC']);
        $data = $this->serializer->serialize($posts, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_FOUND, [], true);
    }

    /**
     *  Upadte an article news
     * @Rest\Put("/news/{id}", name="putNewsAction")
     * @param UuidInterface $id
     * @param Request $request
     * @return JsonResponse
     */
    public function putNewsAction(UuidInterface $id, Request $request): JsonResponse
    {
        $title = $request->get('title');
        $text = $request->get('text');
        if (empty($title)) {
            throw new BadRequestHttpException('title cannot be empty');
        }
        if (empty($text)) {
            throw new BadRequestHttpException('text cannot be empty');
        }

        return new JsonResponse(Response::HTTP_FOUND, [], true);
    }
}
