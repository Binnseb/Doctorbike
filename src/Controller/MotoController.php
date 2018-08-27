<?php

namespace App\Controller;

use App\Entity\Modele;
use App\Entity\Moto;
use Doctrine\Common\Persistence\ObjectManager;
use App\Form\MotoType;
use App\Repository\MotoRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moto")
 */
class MotoController extends Controller
{
    /**
     * Méthode permettant d'afficher la liste des motos (10 maximum par page)
     * (triées selon la recherche de l'utilisateur (par défaut par ordre croissant sur marque, modele, cylindree))
     * @Route("/list", name="moto_list", methods="GET")
     * @param MotoRepository $motoRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(MotoRepository $motoRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $motoRepository->getWithSearchQueryBuilder($q);
        //On définit le nombre de lignes à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('moto/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Méthode permettant d'ajouter une moto à un user (si elle n'existe pas encore dans la liste des motos et/ou des modèles on l'ajoute également)
     * @Route("/new", name="moto_new")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function userAddMoto(Request $request, ObjectManager $manager)
    {
        //On instancie une nouvelle moto
        $moto = new Moto();
        //On récupère l'utilisateur courant
        $user = $this->getUser();
        //On instancie notre formulaire et on lui indique qu'il attend une requête
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);
        //On récupère les deux repository nécessaire à la vérification pour la moto et le modèle
        $motoRepository = $this->getDoctrine()->getRepository(Moto::class);
        $modeleRepository = $this->getDoctrine()->getRepository(Modele::class);

        if ($form->isSubmitted() && $form->isValid())
        {
            //On récupère les données du formulaire
            $marque = $form->get('marque')->getData();
            $modele = $form->get('modele')->getData();
            $cylindree = $form->get('cylindree')->getData();
            $annee = $form->get('annee')->getData();

            //On créé un nouveau modele et on lui affecte les valeurs du formulaire (dans le cas où il n'existe pas encore)
            $newModele = new Modele();
            $newModele->setNom($modele->getNom());
            $newModele->setMarque($marque);
            $newModele->setCylindree($cylindree);

            //On vérifie si la moto et/ou le modèle existe déjà grâce aux méthodes du Repository
            $motoExist = $motoRepository->findIfExist($newModele, $annee);
            $modeleExist = $modeleRepository->findIfExist($newModele);

            //Si le modèle n'existe pas (0) on la fait persister
            if(sizeof($modeleExist) < 1)
            {
                $manager->persist($newModele);
            }
            //Sinon on affecte le modele à 0 pour ne pas l'ajouter lors du flush
            else {
                $newModele = $modeleExist[0];
            }

            //On définit les champs pour la moto
            $moto->setAnnee($annee);
            $moto->setModele($newModele);

            //Si la moto n'existe pas (0) on la fait persister
            if(sizeof($motoExist) < 1)
            {
                $manager->persist($moto);
            }
            //Sinon on affecte la moto à 0 pour ne pas l'ajouter lors du flush
            else {
                $moto = $motoExist[0];
            }
            //Si la moto n'est pas déjà liée à l'utilisateur on la lui affecte
            if(!$user->checkIfUserMotoExists($moto))
            {
                $user->addMoto($moto);
                $user->setMotoActive($moto);
                $manager->persist($user);
                $this->addFlash('success', 'La moto a bien été ajoutée à votre profil');
            }
            else {
               $this->addFlash('danger', 'Cette moto est déjà liée à votre profil');
               return $this->redirectToRoute('moto_new');
            }

            //On ajoute la (ou les) entitée(s) persistée(s) en DB
            $manager->flush();
            return $this->redirectToRoute('gestionDuCompte');
        }

        return $this->render('moto/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Méthode permettant de supprimer une moto d'un utilisateur sur base de son ID reçue en URL
     * @Route("/delete/user-moto/{id}", name="user_delete_moto", methods="GET|DELETE")
     * @param ObjectManager $manager
     * @param MotoRepository $repo
     * @param $id
     * @return Response
     */
    public function userDeleteMoto(ObjectManager $manager, MotoRepository $repo, $id): Response
    {
        //On récupère l'utilisateur courant
        $user = $this->getUser();
        //On récupère la moto grâce à l'id de l'URL
        $moto = $repo->findOneBy(['id' => $id]);
        //On récupère les utilisateurs liés à cette moto
        $motos = $user->getMotos();
        //On instancie un tableau pour stocker les IDS des motos trouvées
        $idsMoto = [];

        //Pour chaque moto dans le tableau moto, on l'ajoute dans le tableau des ids
        for($i = 0; $i < sizeof($motos); $i++)
        {
            array_push($idsMoto, $motos[$i]->getId());
        }
        //Si on trouve une moto correspondante et que la moto existe dans la liste de l'utilisateur on la supprime
        if($moto != null && in_array($moto->getId(), $idsMoto))
        {
            $user->removeMoto($moto);

            $manager->persist($user);

            $manager->flush();

            $this->addFlash('success', 'La moto a bien été supprimée');

            return $this->redirectToRoute('gestionDuCompte');
        }else
            {
                $this->addFlash('danger', 'Une erreur est survenue');
            }

        return $this->redirectToRoute('gestionDuCompte');
    }

    /**
     * Méthode permettant de montrer une moto (permettant la suppression) sur base de son ID reçue en URL
     * @Route("/show/{id}", name="moto_show", methods="GET")
     * @param Moto $moto
     * @return Response
     */
    public function show(Moto $moto): Response
    {
        return $this->render('moto/show.html.twig', ['moto' => $moto]);
    }

    /**
     * Méthode permettant d'éditer la moto sur base de son ID reçue en URL
     * @Route("/edit/{id}", name="moto_edit", methods="GET|POST")
     * @param Request $request
     * @param Moto $moto
     * @return Response
     */
    public function edit(Request $request, Moto $moto): Response
    {
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moto_edit', ['id' => $moto->getId()]);
        }

        return $this->render('moto/edit.html.twig', [
            'moto' => $moto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Méthode permettant de supprimer une moto sur base de son ID reçue en URL
     * @Route("/delete/{id}", name="moto_delete", methods="GET|DELETE")
     * @param Request $request
     * @param Moto $moto
     * @return Response
     */
    public function delete(Request $request, Moto $moto): Response
    {
        if ($this->isCsrfTokenValid('delete'.$moto->getId(), $request->request->get('_token')))
        {
            $em = $this->getDoctrine()->getManager();

            $em->remove($moto);

            $em->flush();

            $this->addFlash('success', 'La moto a bien été supprimée');
        }

        return $this->redirectToRoute('moto_list');
    }
}
