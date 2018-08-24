<?php

namespace App\Controller;

use App\Entity\Scenario;
use App\Entity\QuestionReponse;
use App\Form\SubmitQuestionReponseType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/questionReponse")
 */
class QuestionReponseController extends Controller
{
    /**
     * @Route("/index", name="question_reponse")
     */
    public function index()
    {
        return $this->render('question_reponse/index.html.twig', [
            'controller_name' => 'QuestionReponseController',
        ]);
    }

    /**
     * Méthode d'ajout des questions réponses (sur base de l'id du scénario reçu en URL)
     * @Route("/new/{id}", name="new_question_reponse")
     */
    public function new($id, Request $request, ObjectManager $manager)
    {
        //On créé notre formulaire (ce formulaire imbriqué contient les 2 autres formulaires nécessaires (Le formulaire ChoixQuestionType et QuestionReponseType)
        $formSubmitQuestionReponse = $this->createForm(SubmitQuestionReponseType::class, null, ['id' => $id]);

        //On instancie nos trois instance pour gérer l'ajout des question réponses (Oui/Non/Jenesaispas)
        $QuestionReponseSiOui = new QuestionReponse();
        $QuestionReponseSiNon = new QuestionReponse();
        $QuestionReponseSiJenesaispas = new QuestionReponse();

        //On récupère le repository de la classe scénario
        $em = $this->getDoctrine()->getRepository(Scenario::class);

        //On indique que le formulaire doit récupérer la requête
        $formSubmitQuestionReponse->handleRequest($request);

        //Si le formulaire principal est soumis et qu'il est valide (ensemble des 3 formulaires également)
        if($formSubmitQuestionReponse->isSubmitted() && $formSubmitQuestionReponse->isValid())
        {
            $QuestionAnterieurOui = $formSubmitQuestionReponse->get('QuestionSiOui')->get('choixNouvelleRepOuQuestionAnt')->getData();
            $QuestionAnterieurNon = $formSubmitQuestionReponse->get('QuestionSiNon')->get('choixNouvelleRepOuQuestionAnt')->getData();
            $QuestionAnterieurJenesaispas = $formSubmitQuestionReponse->get('QuestionSiJenesaispas')->get('choixNouvelleRepOuQuestionAnt')->getData();

            //On récupère le scénario sur base de l'ID reçue en URL
            $setScenario = $em->findOneBy(['id' => $id]);
            //On affecte la question choisie dans le formulaire à une variable
            $QuestionActuelle = $formSubmitQuestionReponse->get('ChoixQuestion')->get('listeDesQuestionsExistantes')->getData();

            if ($QuestionAnterieurOui->isEmpty())
            {
                //On set la question pour la réponse oui
                $QuestionReponseSiOui->setQuestion($formSubmitQuestionReponse->get('QuestionSiOui')->get('question')->getData());
                $QuestionReponseSiOui->setAide($formSubmitQuestionReponse->get('QuestionSiOui')->get('aide')->getData());
                $QuestionReponseSiOui->setImage($formSubmitQuestionReponse->get('QuestionSiOui')->get('image')->getData());
                $QuestionReponseSiOui->setEstSolution($formSubmitQuestionReponse->get('QuestionSiOui')->get('estSolution')->getData());
                $QuestionReponseSiOui->setScenario($setScenario);
                $QuestionReponseSiOui->setEstPremiereQuestion(false);
                $manager->persist($QuestionReponseSiOui);
                $QuestionActuelle->setIdQuestionSiOui($QuestionReponseSiOui);
            }
            else
            {
                $QuestionActuelle->setIdQuestionSiOui($QuestionAnterieurOui);
            }

            if ($QuestionAnterieurNon->isEmpty())
            {
                //On set la question pour la réponse non
                $QuestionReponseSiNon->setQuestion($formSubmitQuestionReponse->get('QuestionSiNon')->get('question')->getData());
                $QuestionReponseSiNon->setAide($formSubmitQuestionReponse->get('QuestionSiNon')->get('aide')->getData());
                $QuestionReponseSiNon->setImage($formSubmitQuestionReponse->get('QuestionSiNon')->get('image')->getData());
                $QuestionReponseSiNon->setEstSolution($formSubmitQuestionReponse->get('QuestionSiNon')->get('estSolution')->getData());
                $QuestionReponseSiNon->setScenario($setScenario);
                $QuestionReponseSiNon->setEstPremiereQuestion(false);
                $manager->persist($QuestionReponseSiNon);
                $QuestionActuelle->setIdQuestionSiNon($QuestionReponseSiNon);
            }
            else
            {
                $QuestionActuelle->setIdQuestionSiNon($QuestionAnterieurNon);
            }
            //On récupère les informations du formulaire Jenesaispas pour voir si il existe
            $JenesaispasExist = $formSubmitQuestionReponse->get('QuestionSiJenesaispas');
            //Si il est existe on set la question si je ne sais pas
            if($JenesaispasExist->get('question')->getData() != null && $JenesaispasExist->get('aide')->getData() != null && $JenesaispasExist->get('image')->getData() != null && $JenesaispasExist->get('estSolution')->getData() != null)
            {
                if($QuestionAnterieurJenesaispas->isEmpty())
                {
                    $QuestionReponseSiJenesaispas->setQuestion($JenesaispasExist->get('question')->getData());
                    $QuestionReponseSiJenesaispas->setAide($JenesaispasExist->get('aide')->getData());
                    $QuestionReponseSiJenesaispas->setImage($JenesaispasExist->get('image')->getData());
                    $QuestionReponseSiJenesaispas->setEstSolution($JenesaispasExist->get('estSolution')->getData());
                    $QuestionReponseSiJenesaispas->setScenario($setScenario);
                    $QuestionReponseSiJenesaispas->setEstPremiereQuestion(false);
                }
                else
                {
                    $QuestionActuelle->setIdQuestionSiJenesaispas($QuestionAnterieurJenesaispas);
                }

                $manager->persist($QuestionReponseSiJenesaispas);
                $QuestionActuelle->setIdQuestionSiJenesaispas($QuestionReponseSiJenesaispas);
            }
            //On fait persister nos entités et on les sauvegarde en DB
            $manager->persist($QuestionActuelle);
            $manager->flush();

            $this->addFlash('success', 'Les questions ont bien été ajoutées');

            return $this->redirectToRoute('new_question_reponse', [
                'formSubmitQuestionReponse' => $formSubmitQuestionReponse->createView(),
            ]);
        }

        return $this->render('question_reponse/new.html.twig', [
            'formSubmitQuestionReponse' => $formSubmitQuestionReponse->createView(),
        ]);
    }
}
