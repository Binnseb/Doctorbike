<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\MarqueRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/marque")
 */
class MarqueController extends Controller
{
    /**
     * Méthode pour le rendu de la page d'acceuil des marques (triées par ordre croissant)
     * @Route("/list", name="marque_index", methods="GET")
     */
    public function index(MarqueRepository $marqueRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $marqueRepository->getWithSearchQueryBuilder($q);
        //On définit le nombre de lignes (résultat) à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('marque/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Méthode permettant d'ajouter une marque
     * @Route("/new", name="addMarque", methods="GET|POST")
     */
    public function addMarque(Request $request, ObjectManager $manager): Response
    {
        $marque = new Marque();

        $form = $this->createForm(MarqueType::class, $marque);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($marque);

            $manager->flush();

            $this->addFlash('success', 'La marque à bien été ajoutée');
        }

        if($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('danger', 'Une erreur est survenue');
        }

        return $this->render('marque/addMarque.html.twig', [
            'formAddMarque' => $form->createView()
        ]);
    }

    /**
     * Méthode permettant de montrer une marque sur base de son ID reçue en URL
     * @Route("/show/{id}", name="marque_show", methods="GET")
     */
    public function show(Marque $marque): Response
    {
        return $this->render('marque/show.html.twig', ['marque' => $marque]);
    }

    /**
     * Méthode permettant d'éditer une marque sur base de son ID reçue en URL
     * @Route("/edit/{id}", name="marque_edit", methods="GET|POST")
     */
    public function edit(Request $request, Marque $marque): Response
    {
        $form = $this->createForm(MarqueType::class, $marque);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La marque a bien été modifiée');

            return $this->redirectToRoute('marque_edit', ['id' => $marque->getId()]);
        }

        if($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('danger', 'Une erreur est survenue');
        }

        return $this->render('marque/edit.html.twig', [
            'marque' => $marque,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Méthode permettant de supprimer une marque sur base de son ID reçue en URL
     * @Route("/delete/{id}", name="marque_delete", methods="DELETE")
     */
    public function delete(Request $request, Marque $marque): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marque->getId(), $request->request->get('_token')))
        {
            $em = $this->getDoctrine()->getManager();

            $em->remove($marque);

            $em->flush();

            $this->addFlash('success', 'La marque a bien été supprimée');
        }

        return $this->redirectToRoute('marque_index');
    }

}
