<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MotoActiveType;
use App\Form\UpdateUserType;
use App\Form\RegistrationType;
use App\Repository\MotoRepository;
use App\Form\AdminUpdateUserType;
use App\Repository\HistoriqueRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * Méthode permettant d'inscrire un utilisateur
     * @Route("/inscription", name="inscription", methods="GET|POST")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordencoder (gère le cryptage du mot de passe)
     * @param \Swift_Mailer $mailer (pour l'envoie de mail)
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function addUser(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordencoder, \Swift_Mailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            //On crée le token pour le champ ConfirmationToken
            $token = md5(random_bytes(60));

            //On hash le password reçu dans le formulaire
            $hash = $passwordencoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $user->setConfirmationToken($token);
            //Par défaut tous les utilisateurs ont le rôle user
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);

            $manager->flush();
            //On prépare le mail à envoyer à l'utilisateur grâce au service SwiftMailer
            $message = (new \Swift_Message('Confimation d\'inscription'))
                ->setFrom('Doctorbike@auto.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/registration.html.twig',
                        array('user' => $user)
                    )
                );
            //On envoie le mail
            $mailer->send($message);

            $this->addFlash(
                'success',
                'Un email de confirmation vous a été envoyé'
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/inscription.html.twig', [
            'formUser' => $form->createView()
        ]);
    }

    /**
     * Méthode permettant de mettre à jour les informations de l'utilisateur en cours
     * @Route("/gestionDuCompte", name="gestionDuCompte", requirements={"fragment": "headingThree"})
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function gestionDuCompte(Request $request, ObjectManager $manager, HistoriqueRepository $historiqueRepository, PaginatorInterface $paginator)
    {
        $user = $this->getUser();

        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $historiqueRepository->getWithSearchQueryBuilder($q, $user->getId());
        //On définit le nombre de lignes (résultat) à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        $form = $this->createForm(UpdateUserType::class, $user);

        $form->handleRequest($request);

        if($q)
        {
            $this->redirectToRoute('gestionDuCompte',
                [
                    'pagination' => $pagination,
                    'user' => $user,
                    'formUpdateUser' => $form->createView()
                ]);
        }

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);

            $manager->flush();

            $this->addFlash('success', 'Votre compte a bien été modifié');
        }

        if($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('danger', 'Une erreur est survenue');
        }

        return $this->render('user/gestionDuCompte.html.twig', [
            'user' => $user,
            'formUpdateUser' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * Méthode permettant d'afficher tous les utilisateurs (admin)
     * Ces derniers sont soit triés par l'utilisateur ou afficher par défaut selon leur pseudo par ordre croissant
     * @Route("/gestionDesUsers", name="gestionDesUsers")
     * @param UserRepository $userRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function gestionDesUsers(UserRepository $userRepository, Request $request, PaginatorInterface $paginator)
    {
        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $userRepository->getWithSearchQueryBuilder($q);
        //On définit le nombre de lignes (résultat) à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/gestionDesUsers.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Méthode permettant à un administrateur de modifier un compte (sauf l'image car personnel) sur base de son ID en URL
     * @Route("/update/{id}", name="gestionDesUsersUpdate")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function adminEditUser(Request $request, User $user): Response
    {
        $form = $this->createForm(AdminUpdateUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été modifié');

            return $this->redirectToRoute('gestionDesUsersUpdate', ['id' => $user->getId()]);
        }

        return $this->render('user/gestionDesUsersUpdate.html.twig', [
            'user' => $user,
            'formAdminUpdateUser' => $form->createView(),
        ]);
    }

    /**
     * Méthode permettant à un administrateur de voir un utilisateur (et le supprimer) sur base de son ID en URL
     * @Route("/show/{id}", name="user_show")
     * @param User $user
     * @return Response
     */
    public function showUser(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Méthode permettant à un administrateur de supprimer un user sur base de son ID en URL
     * @Route("/delete/{id}", name="user_delete", methods="GET|DELETE")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function deleteUser(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token')))
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été supprimé');
        }

        return $this->redirectToRoute('gestionDesUsers');
    }

    /**
     * Méthode permettant de supprimer un historique de la liste d'un utilisateur sur base de son ID reçue en URL
     * @Route("/delete/user-historique/{id}", name="user_delete_historique", methods="GET|DELETE")
     * @param ObjectManager $manager
     * @param $id
     * @return Response
     */
    public function userDeleteHistorique(ObjectManager $manager, HistoriqueRepository $repo, $id): Response
    {
        //On récupère l'utilisateur courant
        $user = $this->getUser();
        //On récupère l'historique grâce à l'id de l'URL
        $historique = $repo->findOneBy(['id' => $id]);
        //On récupère les utilisateurs liés à cet historique
        $historiques = $user->getHistoriques();
        //On instancie un tableau pour stocker les IDS des historiques trouvés
        $idsHistoriques = [];

        //Pour chaque historique dans le tableau historique, on l'ajoute dans le tableau des ids
        for($i = 0; $i < sizeof($historiques); $i++)
        {
            array_push($idsHistoriques, $historiques[$i]->getId());
        }
        //Si on trouve un historique correspondant et que l'historique existe dans la liste de l'utilisateur on la supprime
        if($historique != null && in_array($historique->getId(), $idsHistoriques))
        {
            $user->removeHistorique($historique);

            $manager->persist($user);

            $manager->flush();

            $this->addFlash('success', 'L\'historique a bien été supprimé');

            return $this->redirectToRoute('gestionDuCompte');
        }else
        {
            $this->addFlash('danger', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('gestionDuCompte');
    }

    /**
     * @Route("/active-moto/{id}", name="user_active_moto", methods="GET|DELETE")
     * @param ObjectManager $manager
     * @param MotoRepository $repo
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function userActiveMoto(ObjectManager $manager, MotoRepository $repo, $id)
    {
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
            $user->setMotoActive($moto);

            $manager->persist($user);

            $manager->flush();

            $this->addFlash('success', 'La moto a bien été activée');

            return $this->redirectToRoute('gestionDuCompte');
        }else
        {
            $this->addFlash('danger', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('gestionDuCompte');
    }
}
