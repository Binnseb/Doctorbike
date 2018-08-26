<?php

namespace App\Controller;

use App\Entity\Cylindree;
use App\Form\CylindreeType;
use App\Repository\CylindreeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @Route("/cylindree")
 */
class CylindreeController extends Controller
{
    /**
     * Méthode permettant d'afficher les cylindrées (triées par ordre croissant)
     * @Route("/list", name="cylindree_list", methods="GET")
     * @param CylindreeRepository $cylindreeRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(CylindreeRepository $cylindreeRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $cylindreeRepository->getWithSearchQueryBuilder($q);
        //On définit le nombre de lignes (résultat) à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('cylindree/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Méthode permettant d'ajouter une cylindrée
     * @Route("/new", name="cylindree_new", methods="GET|POST")
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function addCylindree(Request $request, ObjectManager $manager): Response
    {
        $cylindree = new Cylindree();

        $form = $this->createForm(CylindreeType::class, $cylindree);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($cylindree);

            $manager->flush();

            $this->addFlash('success', 'La cylindrée à bien été ajoutée');

            return $this->redirectToRoute('cylindree_list', [
                'formAddCylindree' => $form->createView()
            ]);
        }

        if($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('danger', 'Une erreur est survenue');
        }

        return $this->render('cylindree/addCylindree.html.twig', [
            'formAddCylindree' => $form->createView()
        ]);
    }

    /**
     * Méthode permettant de montrer une cylindrée sur base de son ID en URL
     * @Route("/show/{id}", name="cylindree_show", methods="GET")
     * @param Cylindree $cylindree
     * @return Response
     */
    public function show(Cylindree $cylindree): Response
    {
        return $this->render('cylindree/show.html.twig', ['cylindree' => $cylindree]);
    }

    /**
     * * Méthode permettant de supprimer une cylindrée sur base de son ID en URL
     * @Route("/{id}", name="cylindree_delete", methods="DELETE")
     * @param Request $request
     * @param Cylindree $cylindree
     * @return Response
     */
    public function delete(Request $request, Cylindree $cylindree): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cylindree->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cylindree);
            $em->flush();

            $this->addFlash('danger', 'La cylindrée à bien été supprimée');
        }

        return $this->redirectToRoute('cylindree_list');
    }
}
