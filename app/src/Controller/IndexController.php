<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Safe\Exceptions\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use function Safe\json_encode;

final class IndexController extends AbstractController
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    /**
     * @throws JsonException
     *
     * @Route("/info", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).*"}, name="info")
     */
    public function infoAction(): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();
        return new Response(
            '<html><body>'.$phpinfo.'</body></html>');
    }
    
    /**
     * @throws JsonException
     *
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).*"}, name="index")
     */
    public function indexAction(): Response
    {
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var User|null $user */
        $user = $this->getUser();
        $data = null;
        if (!empty($user)) {
            $userClone = clone $user;
            $userClone->setPassword('');
            $data = $this->serializer->serialize($userClone, JsonEncoder::FORMAT, ['groups' => ['User_default']]);
        }

        return $this->render('base.html.twig', [
            'isAuthenticated' => json_encode(! empty($user)),
            'user' => $data ?? json_encode($data),
        ]);
    }
}