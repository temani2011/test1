<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Catalog;
use App\Document\Document;
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
final class DocumentController extends AbstractController
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
     * @Rest\Post("/document")
     * @param Request $request
     * @return JsonResponse
     */

    public function documentPost(Request $request) : JsonResponse
    {
        $user = $this->getUser();
        $catalogId = $request->request->get('catalogId');
        $document = new Catalog();
        $entityAsArray = $this->serializer->normalize($user, null, ['groups' => ['User_default']]);

        $files = $request->files->get('file');
        $fullPaths = [];
        if($request->files->get('file')) {
            $this->fileUploader->setTargetDirectory($this->fileUploader->getTargetDirectory()
                . $user->getLogin() . '/news');
            foreach($files as $file) {
                $fileName = $this->fileUploader->upload($file);
                $fullPaths[] = $this->fileUploader->getTargetDirectory() . '/' . $fileName;
            }
        }

        $document->setFileName($request->request->get('fileName'))
            ->setPath($fullPaths)
            ->setAuthor($entityAsArray);
        $catalog = $this->dm->getRepository(Catalog::class)->findBy(['id'=>$catalogId]);
        $catalog->addDocument($document);
        $this->commitChanges($catalog, true);
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/catalog/{slug}")
     * @param string $slug
     * @return JsonResponse
     */
    public function catalogGet(string $slug) : JsonResponse
    {
        $catalog = $this->dm->getRepository(Catalog::class)->findOneBy(['slug' => $slug]);
        if(!$catalog) throw new BadRequestHttpException("catalog not found");
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/catalog/all")
     * @return JsonResponse
     */
    public function catalogGetAll() : JsonResponse
    {
        $catalogs = $this->dm->getRepository(Bucket::class);
        if(!$catalogs) throw new BadRequestHttpException("catalogs are empty");
        $data = $this->serializer->serialize($catalogs, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/comment/slug={slug}")
     * @return JsonResponse
     */
    public function commentDeleteBySlug(string $slug) : JsonResponse
    {
//        $buckets = $this->dm->getRepository(Bucket::class)->findOneBy(['comments.slug' => $slug]);
//        if(!$buckets) throw new BadRequestHttpException("Comments are empty");
//        foreach ($buckets->getComments() as $comments)
//            if($comments->getSlug()==$slug)
//                $comment = $comments;
//        $buckets->removeComments($comment);
//        $this->commitChanges($buckets, true);
//        $data = $this->serializer->serialize($buckets, JsonEncoder::FORMAT);
//        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/comment/{pid}")
     * @return JsonResponse
     */
    public function commentDeleteByPostId(string $pid) : JsonResponse
    {
//        $buckets = $this->dm->getRepository(Bucket::class)->findBy(['postId' => $pid]);
//        if(!$buckets) throw new BadRequestHttpException("Comments are empty");
//        foreach ($buckets as $bucket)
//            $this->commitChanges($bucket, false);
//        $data = $this->serializer->serialize($buckets, JsonEncoder::FORMAT);
//        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    public function commitChanges(Catalog $catalog, bool $isPersist){
        try {
            ($isPersist) ? $this->dm->persist($catalog) : $this->dm->remove($catalog);
            $this->dm->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
