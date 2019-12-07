<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }
        /**
         * @Route("/about", name="index2")
         */
        public function about()
        {
                    return new Response(
                        '<html><body>Gay</body></html>'
                    );
        }
}
