<?php

namespace App\Controller;

use App\Entity\Modele;
use App\Form\ModeleType;
use App\Form\AddModeleType;
use App\Repository\ModeleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @Route("/modele")
 */
class ModeleController extends Controller
{
    /**
     * Méthode permettant d'afficher la liste des modèles (10 maximum par page)
     * (triées selon la recherche de l'utilisateur (par défaut par ordre croissant sur marque, modele, cylindree))
     * @Route("/list", name="modele_index", methods="GET")
     * @param ModeleRepository $modeleRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(ModeleRepository $modeleRepository, Request $request, PaginatorInterface $paginator): Response
    {

        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $modeleRepository->getWithSearchQueryBuilder($q);
        //On définit le nombre de lignes (résultat) à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('modele/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Méthode permettant d'ajouter un modèle
     * @Route("/new", name="modele_new", methods="GET|POST")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function new(Request $request, ObjectManager $manager): Response
    {
        $modele = new Modele();

        $form = $this->createForm(AddModeleType::class, $modele);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $modele->setMarque($form->get('marque')->getData());

            $modele->setCylindree($form->get('cylindree')->getData());

            $modele->setNom($form->get('nom')->getData());

            $manager->persist($modele);

            $manager->flush();

            $this->addFlash('success', 'Le modèle a bien été ajouté');

            return $this->redirectToRoute('modele_index');
        }

        return $this->render('modele/new.html.twig', [
            'modele' => $modele,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Méthode permettant de monter un modèle sur base de son ID reçue en URL
     * @Route("/show/{id}", name="modele_show", methods="GET")
     * @param Modele $modele
     * @return Response
     */
    public function show(Modele $modele): Response
    {
        return $this->render('modele/show.html.twig', ['modele' => $modele]);
    }

    /**
     * Méthode permettant d'éditer un modèle sur base de son ID reçue en URl
     * @Route("/edit/{id}", name="modele_edit", methods="GET|POST")
     * @param Request $request
     * @param Modele $modele
     * @return Response
     */
    public function edit(Request $request, Modele $modele): Response
    {
        $form = $this->createForm(ModeleType::class, $modele);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Le modele a bien été modifié');

            return $this->redirectToRoute('modele_edit', ['id' => $modele->getId()]);
        }

        return $this->render('modele/edit.html.twig', [
            'modele' => $modele,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Méthode permettant de supprimer un modèle sur base de son ID reçue en URL
     * @Route("/delete/{id}", name="modele_delete", methods="DELETE")
     * @param Request $request
     * @param Modele $modele
     * @return Response
     */
    public function delete(Request $request, Modele $modele): Response
    {
        if ($this->isCsrfTokenValid('delete'.$modele->getId(), $request->request->get('_token')))
        {
            $em = $this->getDoctrine()->getManager();

            $em->remove($modele);

            $em->flush();

            $this->addFlash('success', 'Le modele a bien été supprimé');
        }

        return $this->redirectToRoute('modele_index');
    }
}
