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
use App\Service\FileUploader;
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

    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(EntityManagerInterface $em,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator, FileUploader $fileUploader){
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->fileUploader = $fileUploader;
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

        $user = $this->getUser();
        $title = $request->request->get("title");
        $text = $request->request->get('text');
        $coverText = $request->request->get('coverText');
        $coverImage = $request->request->get('coverImage');

        if($request->files->get('file')) {
            $this->fileUploader->setTargetDirectory($this->fileUploader->getTargetDirectory()
                . $user->getLogin() . '/news');
            $fileName = $this->fileUploader->upload($request->files->get('file'));
            $coverImage = $this->fileUploader->getTargetDirectory() . '/' . $fileName;
        }

        $news = new News();
        $news->setTitle($title)
            ->setText($text)
            ->setCoverImage($coverImage)
            ->setCoverText($coverText)
            ->setUser($user);

        //bad errors handling
//      $errors = $this->validator->validate($news);
//      if(count($errors) > 0) {
//          throw new BadRequestHttpException((string) $errors);
//      }

        try {
            $this->em->persist($news);
            $this->em->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
        $data = $this->serializer->serialize($news, JsonEncoder::FORMAT, ['groups' => ['News_default']]);
//        $data = $this->serializer->serialize($request, JsonEncoder::FORMAT);
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
        $news = $this->em->getRepository(News::class)->find(Uuid::fromString($id));
        $user = $this->getUser();

 //     $data = json_decode($request->getContent(), true);
        $title = $request->request->get("title");
        $text = $request->request->get('text');
        $coverText = $request->request->get('coverText');
        $coverImage = $request->request->get('coverImage');

        $curCoverImage = $news->getCoverImage();
        if($curCoverImage != $coverImage){
            $this->fileUploader->delete($curCoverImage);
            if($request->files->get('file')) {
                $this->fileUploader->setTargetDirectory($this->fileUploader->getTargetDirectory()
                    . $user->getLogin() . '/news');
                $fileName = $this->fileUploader->upload($request->files->get('file'));
                $coverImage = $this->fileUploader->getTargetDirectory() . '/' . $fileName;
            }
        }

        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid Uuid.', $id));
        }

        if (!$news) {
            throw new BadRequestHttpException('No product found for id '.$id);
        }
        $news->setTitle($title)
            ->setText($text)
            ->setCoverText($coverText)
            ->setCoverImage($coverImage);
//        $errors = $this->validator->validate($news);
//        if(count($errors) > 0) {
//            throw new BadRequestHttpException((string) $errors);
//        }
        try {
            $this->em->persist($news);
            $this->em->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e);
        }
        $data = $this->serializer->serialize($news, JsonEncoder::FORMAT, ['groups' => ['News_default']]);
//        $data = $this->serializer->serialize($request, JsonEncoder::FORMAT);
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
        $news = $this->em->getRepository(News::class)->find(Uuid::fromString($id));

        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid Uuid.', $id));
        }

        if (!$news) {
            throw new BadRequestHttpException('No news found for id '.$id);
        }

        $curCoverImage = $news->getCoverImage();
        if($curCoverImage) $this->fileUploader->delete($curCoverImage);

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
