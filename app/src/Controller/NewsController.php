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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
/**
 * @Rest\Route("/api")
 */
final class NewsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $em,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator){
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;
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
        $title = $request->request->get("title");
        $text = $request->request->get('text');
        $news = new News();
        $news->setTitle($title)
            ->setText($text)
            ->setUser($this->getUser());
        $errors = $this->validator->validate($news);
        if(count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }
        try {
            $this->em->persist($news);
            $this->em->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e);
        }
        $data = $this->serializer->serialize($news, JsonEncoder::FORMAT, ['groups' => ['News_default']]);
        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Get all news articles
     * @Rest\Get("/news", name="getAllNewsAction")
     */
    public function getAllNewsAction(): JsonResponse
    {
        $news = $this->em->getRepository(News::class)->findBy([],['created' => 'DESC']);
        $data = $this->serializer->serialize($news, JsonEncoder::FORMAT, ['groups' => ['News_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Get news article by id
     * @Rest\Get("/news/{id}", name="getNewsAction")
     * @param string $id
     * @return JsonResponse
     */
    public function getNewsAction(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid Uuid.', $id));
        }
        $news = $this->em->getRepository(News::class)->find(Uuid::fromString($id));
        if (!$news) {
            throw new BadRequestHttpException('No product found for id '.$id);
        }
        $data = $this->serializer->serialize($news, JsonEncoder::FORMAT, ['groups' => ['News_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * Update an article news
     * @Rest\Put("/news/{id}", name="putNewsAction")
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function putNewsAction(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        array_key_exists('title',$data) ? $title = $data['title'] : $title = "";
        array_key_exists('text',$data) ? $text = $data['text'] : $text = "";
        array_key_exists('coverText',$data) ? $coverText = $data['coverText'] : $coverText = "";
        array_key_exists('coverImage',$data) ? $coverImage = $data['coverImage'] : $coverImage = "";
        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid Uuid.', $id));
        }
        $news = $this->em->getRepository(News::class)->find(Uuid::fromString($id));
        if (!$news) {
            throw new BadRequestHttpException('No product found for id '.$id);
        }
        $news->setTitle($title)
            ->setText($text)
            ->setCoverText($coverText)
            ->setCoverImage($coverImage);
        $errors = $this->validator->validate($news);
        if(count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
        }
        try {
            $this->em->persist($news);
            $this->em->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e);
        }
        $data = $this->serializer->serialize($news, JsonEncoder::FORMAT, ['groups' => ['News_default']]);
        return new JsonResponse($data,Response::HTTP_OK, [], true);
    }

    /**
     * Delete an article news
     * @Rest\Delete("/news/{id}", name="deleteNewsAction")
     * @param string $id
     * @return JsonResponse
     */
    public function deleteNewsAction(string $id): JsonResponse
    {
        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid Uuid.', $id));
        }
        $news = $this->em->getRepository(News::class)->find(Uuid::fromString($id));
        if (!$news) {
            throw new BadRequestHttpException('No product found for id '.$id);
        }
        try {
            $this->em->remove($news);
            $this->em->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e);
        }
        $data = $this->serializer->serialize($news, JsonEncoder::FORMAT, ['groups' => ['News_default']]);
        return new JsonResponse($data,Response::HTTP_OK , [], true);
    }
}
