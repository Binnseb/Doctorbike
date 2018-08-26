<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\ResetPasswordType;
use App\Form\ForgetPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/security")
 */
class SecurityController extends Controller
{
    /**
     * Méthode permettant de confirmer l'inscription d'un utilisateur en se basant sur son id et son token reçu dans l'URL
     * @Route("/confirmEmail/{id}/{token}", name= "confirmMail")
     * @param UserRepository $repo
     * @param ObjectManager $manager
     * @param $id
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function validRegistration(UserRepository $repo, ObjectManager $manager, $id, $token)
    {
        $user = $repo->findOneBy([
            'id' => $id, 
            'confirmationToken' => $token
        ]);

        if($user) 
        {
            $user->setConfirmationToken(null);

            $user->setConfirmedAt(new \Datetime);

            $manager->persist($user);

            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été créé, vous pouvez à présent vous connecter'
            );

            return $this->redirectToRoute('security_login');
        }

        $this->addFlash(
            'danger',
            'Ce token n\'est pas valide ou a déjà été utilisé'
        );

        return $this->render('home/index.html.twig');
    }

    /**
     * Méthode permettant la connexion d'un utilisateur sur base de son email/mdp et que son compte soit bien validé
     * @Route("/login", name="security_login", methods="GET|POST")
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $lastUsername = $authenticationUtils->getLastUsername();

        $error = $authenticationUtils->getLastAuthenticationError();

        if($error)
        {
            $this->addFlash('danger', 'Vos identifiants ne sont pas corrects ou vous n\'avez pas validez votre mail');
        }

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
        ));
    }

    /**
     * Méthode permettant la déconnexion de l'utilisateur
     * @Route("/logout", name="security_logout")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout()
    {
        $this->addFlash(
            'success',
            'Vous êtes déconnecté'
        );

        return $this->redirectToRoute('home');
    }

    /**
     * Méthode permettant d'envoyer un email à l'utilisateur pour réinitialiser son mot de passe
     * @Route("/forgetPassword", name="forgetPassword")
     * @param UserRepository $repo
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function forgetPassword(UserRepository $repo, Request $request, \Swift_Mailer $mailer, ObjectManager $manager)
    {
        $form = $this->createForm(ForgetPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $email = $form->getData();

            $user = $repo->findOneBy([
                'email' => $email
            ]);

            if($user)
            {
                $token = md5(random_bytes(60));

                $user->setResetToken($token);

                $manager->persist($user);

                $manager->flush();

                $message = (new \Swift_Message('Recupération du mot de passe'))
                    ->setFrom('Doctorbike@auto.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/forgetPassword.html.twig',
                            array('user' => $user)
                        )
                    );

                $mailer->send($message);

                $this->addFlash(
                    'success',
                    'Un email vous a été envoyé afin de réinitialiser votre mot de passe'
                );

                return $this->redirectToRoute('home');
            }
            else
            {
                $this->addFlash(
                    'danger',
                    'Aucun compte ne correspond à l\'adresse mail indiquée'
                );
            }
        }

        return $this->render('security/forgetPassword.html.twig', [
            'formForgetPassword' => $form->createView()
        ]);
    }

    /**
     * Méthode permettant de réinitialiser le mdp de l'utilisateur sur base de l'id et du token reçu dans l'URL
     * @Route("/resetPassword/{id}/{token}", name="resetPassword")
     * @param UserRepository $repo
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordencoder
     * @param $id
     * @param $token
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function resetPassword(UserRepository $repo, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordencoder, $id, $token, User $user)
    {

        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user = $repo->findOneBy([
                'id' => $id,
                'resetToken' => $token
            ]);

            if($user)
            {
                $hash = $passwordencoder->encodePassword($user, $user->getPassword());

                $user->setPassword($hash);

                $user->setResetToken(null);

                $user->setResetAt(new \Datetime);

                $manager->persist($user);

                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a été modifié'
                );

                return $this->redirectToRoute('security_login');
            }
            else
            {
                $this->addFlash('danger', 'Votre token n\'est pas valide');

                return $this->render('security/resetPassword.html.twig', [
                    'formResetPassword' => $form->createView()
                ]);
            }
        }

        return $this->render('security/resetPassword.html.twig', [
            'formResetPassword' => $form->createView()
        ]);
    }
}
