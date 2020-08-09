<?php

declare(strict_types=1);

namespace App\Controller;

//use App\Document\Bucket;
//use App\Document\Comment;
use App\Entity\User;
//use Doctrine\ODM\MongoDB\DocumentManager;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
/**
 * @Rest\Route("/api")
 */
final class UserController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private $em;

//    /**
//     * @var DocumentManager
//     */
//    private $dm;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(EntityManagerInterface $em,
                                //DocumentManager $dm,
                                SerializerInterface $serializer,
                                ValidatorInterface $validator){
        $this->em = $em;
        //$this->dm = $dm;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Rest\Post("/user")
     * @param Request $request
     * @return JsonResponse
     */

    public function addUser(Request $request) : JsonResponse
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $request->request->get('login') ]);
        if($user) throw new BadRequestHttpException('user with that login already exist');
        $user = new User();
        $user->setLogin($request->request->get('login'));
        $user->setPassword($request->request->get('password'));
        //$user->setPlainPassword($request->request->get('password'));
        $user->setRoles($request->request->get('roles'));
//        $errors = $this->validator->validate($user);
//        if(count($errors) > 0) {
//            throw new BadRequestHttpException((string) $errors);
//        }
        try {
            $this->em->persist($user);
            $this->em->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
        $data = $this->serializer->serialize($user, JsonEncoder::FORMAT, ['groups' => ['User_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/user/{id}")
     * @param string $id
     * @return JsonResponse
     */
    public function getUserById(string $id) : JsonResponse
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id ]);
        if(!$user) throw new BadRequestHttpException("user doesn't exist");
        $data = $this->serializer->serialize($user, JsonEncoder::FORMAT, ['groups' => ['User_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Get("/users")
     * @return JsonResponse
     */
    public function getAllUsers() : JsonResponse
    {
        $users = $this->em->getRepository(User::class)->findAll();
        if(!$users) throw new BadRequestHttpException("users doesn't exist");
        $data = $this->serializer->serialize($users, JsonEncoder::FORMAT, ['groups' => ['User_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Put("/user/{id}")
     * @param string $id
     * @param Request $request
     * @return JsonResponse
     */
    public function editUser(string $id, Request $request) : JsonResponse
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id ]);
        if(!$user) throw new BadRequestHttpException("user doesn't exist");
        $curpass = $request->request->get('current_password');
        if(isset($curpass) && $curpass !== "")
            if($curpass === $user->getPassword())
                $user->setPassword($request->request->get('new_password'));
            else throw new BadRequestHttpException("current password not match");
        $user->setRoles($request->request->get('roles'));
        try {
            $this->em->persist($user);
            $this->em->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
        $data = $this->serializer->serialize($user, JsonEncoder::FORMAT, ['groups' => ['User_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Rest\Delete("/user/{id}")
     * @param string $id
     * @return JsonResponse
     */
    public function deleteUser(string $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $id ]);
        if(!$user) throw new BadRequestHttpException("user doesn't exist");
        try {
            $this->em->remove($user);
            $this->em->flush();
        }
        catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
        $data = $this->serializer->serialize($user, JsonEncoder::FORMAT, ['groups' => ['User_default']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
