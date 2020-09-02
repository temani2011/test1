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
final class CatalogController extends AbstractController
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
     * @Rest\Post("/catalog")
     * @param Request $request
     * @return JsonResponse
     */

    public function catalogPost(Request $request) : JsonResponse
    {
        $user = $this->getUser();
        $parentId = $request->request->get('parentId');
        $catalog = new Catalog();
        $entityAsArray = $this->serializer->normalize($user, null, ['groups' => ['User_simple']]);
        $catalog->setName($request->request->get('name'));
        $catalog->setAuthor($entityAsArray);
        $catalog->setSlug($catalog->getName());
        if($parentId) {
            $parent = $this->dm->createQueryBuilder(Catalog::class)
                ->field('id')->equals($parentId)
                ->getQuery()
                ->getSingleResult();
            $catalog->setSlug($parent->getSlug() . '/' . $catalog->getName());
            $catalog->setParent($parent);
            $this->commitChanges($catalog, true);
            $parent->addChild($catalog);
            $this->commitChanges($parent, true);
        } else $this->commitChanges($catalog, true);
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT, ['groups' => ['Catalog_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/catalogs")
     * @return JsonResponse
     */
    public function catalogGetAll() : JsonResponse
    {
        $catalogs = $this->dm->getRepository(Catalog::class)->findBy(['parent.id'=>null]);
        if(!$catalogs) throw new BadRequestHttpException("catalogs are empty");
        $data = $this->serializer->serialize($catalogs, JsonEncoder::FORMAT, ['groups' => ['Catalog_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/catalog/{id}")
     * @param string $id
     * @return JsonResponse
     */
    public function catalogGet(string $id) : JsonResponse
    {
        $catalog = $this->dm->getRepository(Catalog::class)->findOneBy(['id' => $id]);
        if(!$catalog) throw new BadRequestHttpException("catalog not found");
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT, ['groups' => ['Catalog_default']]);
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