<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class HomeController extends Controller
{
    /**
     * Méthode pour le rendu de la page d'acceuil du site
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * Méthoe pour le rendu de la page Foire aux questions
     * @Route("/faq", name="faq")
     */
    public function FAQ()
    {
        return $this->render('home/faq.html.twig');
    }

}
