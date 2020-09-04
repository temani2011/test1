<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Catalog;
use App\Document\Document;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Iterator\PrimingIterator;
use FOS\RestBundle\Controller\Annotations as Rest;
use mysql_xdevapi\Exception;
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
use App\Service\FileUploader;
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

    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(DocumentManager $dm,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator,
                                FileUploader $fileUploader){
        $this->dm = $dm;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->fileUploader = $fileUploader;
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
                ->field('childs')->prime(true)
                ->field('id')->equals($parentId)
                ->getQuery()
                ->getSingleResult();
            $catalog->setSlug($parent->getSlug() . '/' . $catalog->getName());
            $catalog->setParent($parent);
            $this->commitChanges($catalog, true);
            $parent->addChild($catalog);
            $this->commitChanges($parent, true);
        } else $this->commitChanges($catalog, true);
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT,
            ['groups' => ['Catalog_default', 'Catalog_childs']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/catalogs")
     * @return JsonResponse
     */
    public function catalogGetAll() : JsonResponse
    {
        $catalogs = $this->dm->createQueryBuilder(Catalog::class)
            ->field('childs')->prime(true)
            ->field('parent')->equals(null)
            ->getQuery()
            ->execute();
        if(!$catalogs) throw new BadRequestHttpException("there is no catalogs");
        $data = $this->serializer->serialize($catalogs, JsonEncoder::FORMAT,
            ['groups' => ['Catalog_default', 'Catalog_childs' ]]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/catalog/{id}")
     * @param string $id
     * @return JsonResponse
     */
    public function catalogGet(string $id) : JsonResponse
    {
        $catalog = $this->dm->createQueryBuilder(Catalog::class)
            ->field('childs')->prime(true)
            ->field('_id')->equals($id)
            ->getQuery()
            ->execute();
        if(!$catalog) throw new BadRequestHttpException("catalog not found");
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT,
            ['groups' => ['Catalog_default', 'Catalog_childs' ]]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/catalog/{slug}")
     * @param string $slug
     * @return JsonResponse
     */
    public function catalogGetBySlug(string $slug) : JsonResponse
    {
        $catalog = $this->dm->createQueryBuilder(Catalog::class)
            ->field('childs')->prime(true)
            ->field('slug')->equals($slug)
            ->getQuery()
            ->execute();
        if(!$catalog) throw new BadRequestHttpException("catalog not found");
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT,
            ['groups' => ['Catalog_default', 'Catalog_childs' ]]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Put("/catalog/{id}")
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */

    public function catalogUpdate(Request $request, string $id) : JsonResponse
    {
        $catalog = $this->dm->createQueryBuilder(Catalog::class)
            ->field('childs')->prime(true)
            ->field('_id')->equals($id)
            ->getQuery()
            ->getSingleResult();
        $newParentId = $request->request->get('newCatalogId');
        $catalog->setName($request->request->get('name'));
        if($newParentId) {
            $parent = $catalog->getParent();
            $newParent = $this->dm->createQueryBuilder(Catalog::class)
                ->field('childs')->prime(true)
                ->field('_id')->equals($newParentId)
                ->getQuery()
                ->getSingleResult();

            $parent->removeChild($catalog);
            $this->commitChanges($parent, true);

            $catalog->setSlug($newParent->getSlug() . '/' . $catalog->getName());
            $newlevel = $newParent->getLevel();
            $catalog->setLevel(++$newlevel);
            $this->commitChanges($catalog, true);

            $newParent->addChild($catalog);
            $this->commitChanges($newParent, true);
        } else $this->commitChanges($catalog, true);
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT,
            ['groups' => ['Catalog_default', 'Catalog_childs']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/catalog/{id}")
     * @return JsonResponse
     */
    public function catalogDeleteById(string $id) : JsonResponse
    {
        $catalog = $this->dm->createQueryBuilder(Catalog::class)
            ->field('childs')->prime(true)
            ->field('_id')->equals($id)
            ->getQuery()
            ->getSingleResult();
        if(!$catalog) throw new BadRequestHttpException("catalog not found");
        $this->deleteDocumentsFromCatalog($catalog);
        $data = $this->serializer->serialize($catalog, JsonEncoder::FORMAT,
            ['groups' => ['Catalog_default', 'Catalog_childs']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

     function deleteDocumentsFromCatalog($catalog) {
         if($catalog->getChildsCount() > 0) {
             foreach ($catalog->getChilds() as $child)
                 $this->deleteDocumentsFromCatalog($child);
         }
         foreach ($catalog->getDocuments() as $document){
             $curDocPath = $document->getPath();
             if(!$curDocPath) throw new BadRequestHttpException(
                 'Wrong path or record in catalog ' . $catalog->getSlug() . ' with id ' . $catalog->getId());
             $this->fileUploader->delete($curDocPath);
             $catalog->removeDocument($document);
         }
         $this->commitChanges($catalog, false);
    }

     function commitChanges(Catalog $catalog, bool $isPersist){
        try {
            ($isPersist) ? $this->dm->persist($catalog) : $this->dm->remove($catalog);
            $this->dm->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
