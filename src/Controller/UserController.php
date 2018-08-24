<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdateUserType;
use App\Form\RegistrationType;
use App\Form\AdminUpdateUserType;
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
     * @Route("/gestionDuCompte", name="gestionDuCompte")
     */
    public function updateUser(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        $form = $this->createForm(UpdateUserType::class, $user);

        $form->handleRequest($request);

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
            'formUpdateUser' => $form->createView()
        ]);
    }

     /**
      * Méthode permettant d'afficher tous les utilisateurs (admin)
      * Ces derniers sont soit triés par l'utilisateur ou afficher par défaut selon leur pseudo par ordre croissant
     * @Route("/gestionDesUsers", name="gestionDesUsers")
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
     */
    public function showUser(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Méthode permettant à un administrateur de supprimer un user sur base de son ID en URL
     * @Route("/delete/{id}", name="user_delete", methods="GET|DELETE")
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
}
