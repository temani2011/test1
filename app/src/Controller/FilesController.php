<?php

namespace App\Controller;
use App\Service\FileUploader;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
/**
 * @Rest\Route("/api")
 */
final class FilesController extends AbstractController
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer, FileUploader $fileUploader){
        $this->fileUploader = $fileUploader;
        $this->serializer = $serializer;
    }

    /**
     * Upload file
     * @throws BadRequestHttpException
     * @Rest\Post("/file", name="uploadFile")
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadFileAction(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $this->fileUploader->setTargetDirectory($this->fileUploader->getTargetDirectory()
        . $user->getLogin() .'/'. $request->request->get('componentName'));
        $fileName = $this->fileUploader->upload($request->files->get('file'));
        $fullPath = $this->fileUploader->getTargetDirectory()  .'/'. $fileName;
        $data = $this->serializer->serialize($fullPath, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    /**
     * Delete file
     * @throws BadRequestHttpException
     * @Rest\Delete("/file", name="deleteFile")
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteFileAction(Request $request): JsonResponse
    {
        $user = $this->getUser();
//        $this->fileUploader->setTargetDirectory($this->fileUploader->getTargetDirectory()
//            . $user->getLogin() .'/'. $request->request->get('componentName'));
        $fileName = $this->fileUploader->delete($request->request->get('fileName'));
        $data = $this->serializer->serialize($request, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
