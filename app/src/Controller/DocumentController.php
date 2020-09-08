<?php

declare(strict_types=1);

namespace App\Controller;

use App\Document\Catalog;
use App\Document\Document;
use App\Service\FileUploader;
use Doctrine\ODM\MongoDB\DocumentManager;
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
     * @Rest\Post("/document")
     * @param Request $request
     * @return JsonResponse
     */

    public function documentPost(Request $request) : JsonResponse
    {
        $user = $this->getUser();
        $catalogId = $request->request->get('catalogId');
        $entityAsArray = $this->serializer->normalize($user, null, ['groups' => ['User_simple']]);
        $catalog = $this->dm->getRepository(Catalog::class)->findOneBy(['id'=>$catalogId]);
        if(!$catalog) throw new BadRequestHttpException('catalog not found');
        $documents = [];
        $files = $request->files;
        if($files) {
            $this->fileUploader->setTargetDirectory($this->fileUploader->getTargetDirectory()
                . $user->getLogin() . '/documents');
            foreach($files as $file) {
                try {
                    $fileName = $this->fileUploader->upload($file);
                    $fullPath = $this->fileUploader->getTargetDirectory() . '/' . $fileName;
                    $document = new Document();
                    $documents[] = $document;
                    $document->setFileName($fileName)
                        ->setPath($fullPath)
                        ->setAuthor($entityAsArray);
                    $catalog->addDocument($document);
                } catch (Exception $e) { throw new BadRequestHttpException($e->getMessage());}
            }
        }
        $this->commitChanges($catalog, true);
        $data = $this->serializer->serialize([ 'catalogId' => $catalog->getId(), 'documents'=> $documents], JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/document/name={name}")
     * @param string $name
     * @return JsonResponse
     */
    public function documentGetByName(string $name) : JsonResponse
    {
        $catalog = $this->dm->getRepository(Catalog::class)->findOneBy(['documents.filename' => $name]);
        if(!$catalog) throw new BadRequestHttpException("document not found");
        $fdocument = null;
        foreach ($catalog->getDocuments() as $document)
            if($document->getFileName() == $name)
                $fdocument = $document;
        $data = $this->serializer->serialize([ 'catalogId' => $catalog->getId(), 'documents'=> $fdocument], JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/document/id={id}")
     * @param string $id
     * @return JsonResponse
     */
    public function documentGetById(string $id) : JsonResponse
    {
        $catalog = $this->dm->getRepository(Catalog::class)->findOneBy(['documents.id' => $id]);
        if(!$catalog) throw new BadRequestHttpException("document not found");
        $fdocument = null;
        foreach ($catalog->getDocuments() as $document)
            if($document->getId() == $id)
                $fdocument = $document;
        $data = $this->serializer->serialize([ 'catalogId' => $catalog->getId(), 'documents'=> $fdocument], JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/document/catalogId={id}")
     * @return JsonResponse
     */
    public function documentGetAllFromCatalog(string $id) : JsonResponse
    {
        $catalog = $this->dm->getRepository(Catalog::class)->findOneBy(['id' => $id]);
        if(!$catalog) throw new BadRequestHttpException("catalogs are empty");
        $data = $this->serializer->serialize($catalog->getDocuments(), JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Put("/document/{id}")
     * @param string $id
     * @return JsonResponse
     */
    public function documentUpdate(Request $request, string $id) : JsonResponse
    {
        $catalog = $this->dm->getRepository(Catalog::class)->findOneBy(['documents.id' => $id]);
        if(!$catalog) throw new BadRequestHttpException("document not found");
        $fdocument = null;
        $newCatalog = null;
        foreach ($catalog->getDocuments() as $document)
            if($document->getId() == $id) {
                $document->setFileName($request->request->get('fileName'));
                $fdocument = $document;
            }
        $newCatalogId = $request->request->get('newCatalogId');
        if($newCatalogId) {
            $newCatalog = $this->dm->createQueryBuilder(Catalog::class)
                ->field('childs')->prime(true)
                ->field('id')->equals($newCatalogId)
                ->getQuery()
                ->getSingleResult();
            if(!$newCatalog) throw new BadRequestHttpException('new catalog not found');
            $catalog->removeDocument($fdocument);
            $this->commitChanges($catalog, true);

            $newCatalog->addDocument($fdocument);
            $this->commitChanges($newCatalog, true);
        } else $this->commitChanges($catalog, true);
        $data = $this->serializer->serialize([ 'oldCatalogId' => $catalog->getId(),
            'newCatalogId' => $newCatalog->getId(), 'documents' => $fdocument], JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/document/{id}")
     * @param string $id
     * @return JsonResponse
     */
    public function documentDeleteById(string $id) : JsonResponse
    {
        $catalog = $this->dm->getRepository(Catalog::class)->findOneBy(['documents.id' => $id]);
        if(!$catalog) throw new BadRequestHttpException("document not found");
        $fdocument = null;
        foreach ($catalog->getDocuments() as $document)
            if($document->getId() == $id)
                $fdocument = $document;
        $curDocPath = $fdocument->getPath();
        if(!$curDocPath) throw new BadRequestHttpException(
            'Wrong path or record in catalog ' . $catalog->getSlug() . ' with id ' . $catalog->getId());
        $this->fileUploader->delete($curDocPath);
        $catalog->removeDocument($fdocument);
        $this->commitChanges($catalog, true);
        $data = $this->serializer->serialize([ 'catalogId' => $catalog->getId(), 'documents' => $fdocument], JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    function getCatalog(Catalog $catalog, int $level) {
        $parent = null;
        $childs = null;
        $documents = null;
        if($catalog->getChildsCount() > 0 && $level < 2) {
            foreach ($catalog->getChilds() as $child)
                $childs[] = $this->getCatalog($child, $level);
        }
        if($catalog->getParent() && $level < 2) {
            $parent = $this->getCatalog($catalog->getParent(), ++$level);
        }
        if($catalog->getDocumentsCount() > 0) {
            try {
                foreach ($catalog->getDocuments() as $document) {
                    $documentItem = [
                        'id' => $document->getId(),
                        'createdAt' => $document->getCreatedAt(),
                        'author' => $document->getAuthor(),
                        'path' => $document->getPath(),
                        'fileName' => $document->getFileName(),
                    ];
                    $documents[] = $documentItem;
                }
            } catch (\Throwable $e) {
                throw new BadRequestHttpException($e->getMessage() .' | '. $document );
            }
        }
        try {
            $arr = [
                'id' => $catalog->getId(),
                'author' => $catalog->getAuthor(),
                'name' => $catalog->getName(),
                'createdAt' => $catalog->getCreatedAt(),
                'slug' => $catalog->getSlug(),
                'level' => $catalog->getLevel(),
                'childsCount' => $catalog->getChildsCount(),
                'documentsCount' => $catalog->getDocumentsCount(),
                'parent' => $parent,
                'childs' => $childs,
                'documents' => $documents
            ];
        } catch (\Throwable $e){
            throw new BadRequestHttpException($e->getMessage() .' | '. $catalog );
        }
        return $arr;
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
