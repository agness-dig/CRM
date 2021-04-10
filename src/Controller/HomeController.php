<?php

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->redirectToRoute("app_login");
    }


    /**
     * @Route("/mention", name="mention", methods={"GET"})
     */
    public function mention(): Response
    {
        return $this->render("footer/mentions.html.twig");
    }



    /**
     * @Route("/confidentialite", name="confidentialite", methods={"GET"})
     */
    public function confidentialite(): Response
    {
        return $this->render("footer/confidentialites.html.twig");
    }

}
