<?php

namespace App\Controller;

use App\Entity\Scenario;
use App\Entity\QuestionReponse;
use App\Form\EditQuestionReponseType;
use App\Form\SubmitQuestionReponseType;
use App\Repository\QuestionReponseRepository;
use App\Repository\ScenarioRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/questionReponse")
 */
class QuestionReponseController extends Controller
{
    /**
     * @Route("/{id}/list-question", name="list_question_reponse")
     * @param Scenario $scenario
     * @param QuestionReponseRepository $questionReponseRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Scenario $scenario, QuestionReponseRepository $questionReponseRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $questionReponseRepository->getWithSearchQueryBuilder($q, $scenario->getId());
        //On définit le nombre de lignes (résultat) à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('question_reponse/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Méthode d'ajout des questions réponses (sur base de l'id du scénario reçu en URL)
     * @Route("/new/{id}", name="new_question_reponse")
     * @param Scenario $scenario
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function new(Scenario $scenario, Request $request, ObjectManager $manager): Response
    {
        //On créé notre formulaire (ce formulaire imbriqué contient les 2 autres formulaires nécessaires (Le formulaire ChoixQuestionType et QuestionReponseType)
        $formSubmitQuestionReponse = $this->createForm(SubmitQuestionReponseType::class, null, ['id' => $scenario->getId()]);

        //On instancie nos trois instance pour gérer l'ajout des question réponses (Oui/Non/Jenesaispas)
        $questionReponseSiOui = new QuestionReponse();
        $questionReponseSiNon = new QuestionReponse();
        $questionReponseSiJenesaispas = new QuestionReponse();

        //On indique que le formulaire doit récupérer la requête
        $formSubmitQuestionReponse->handleRequest($request);

        //Si le formulaire principal est soumis et qu'il est valide (ensemble des 3 formulaires également)
        if($formSubmitQuestionReponse->isSubmitted() && $formSubmitQuestionReponse->isValid())
        {
            $questionAnterieurOui = $formSubmitQuestionReponse->get('QuestionSiOui')->get('choixNouvelleRepOuQuestionAnt');
            $questionAnterieurNon = $formSubmitQuestionReponse->get('QuestionSiNon')->get('choixNouvelleRepOuQuestionAnt');
            $questionAnterieurJenesaispas = $formSubmitQuestionReponse->get('QuestionSiJenesaispas')->get('choixNouvelleRepOuQuestionAnt');

            //On affecte la question choisie dans le formulaire à une variable
            $questionActuelle = $formSubmitQuestionReponse->get('ChoixQuestion')->get('listeDesQuestionsExistantes')->getData();

            //On set la question pour la réponse oui
            $questionReponseSiOui->setQuestion($formSubmitQuestionReponse->get('QuestionSiOui')->get('question')->getData());
            $questionReponseSiOui->setAide($formSubmitQuestionReponse->get('QuestionSiOui')->get('aide')->getData());
            $questionReponseSiOui->setImage($formSubmitQuestionReponse->get('QuestionSiOui')->get('image')->getData());
            $questionReponseSiOui->setEstSolution($formSubmitQuestionReponse->get('QuestionSiOui')->get('estSolution')->getData());
            $questionReponseSiOui->setScenario($scenario);
            $questionReponseSiOui->setEstPremiereQuestion(false);
            $manager->persist($questionReponseSiOui);
            $questionActuelle->setIdQuestionSiOui($questionReponseSiOui);

            //On set la question pour la réponse non
            $questionReponseSiNon->setQuestion($formSubmitQuestionReponse->get('QuestionSiNon')->get('question')->getData());
            $questionReponseSiNon->setAide($formSubmitQuestionReponse->get('QuestionSiNon')->get('aide')->getData());
            $questionReponseSiNon->setImage($formSubmitQuestionReponse->get('QuestionSiNon')->get('image')->getData());
            $questionReponseSiNon->setEstSolution($formSubmitQuestionReponse->get('QuestionSiNon')->get('estSolution')->getData());
            $questionReponseSiNon->setScenario($scenario);
            $questionReponseSiNon->setEstPremiereQuestion(false);
            $manager->persist($questionReponseSiNon);
            $questionActuelle->setIdQuestionSiNon($questionReponseSiNon);

            //On récupère les informations du formulaire Jenesaispas pour voir si il existe
            $JenesaispasExist = $formSubmitQuestionReponse->get('QuestionSiJenesaispas');
            //Si il existe on set la question si je ne sais pas
            if(!isset($JenesaispasExist))
            {
                $questionReponseSiJenesaispas->setQuestion($JenesaispasExist->get('question')->getData());
                $questionReponseSiJenesaispas->setAide($JenesaispasExist->get('aide')->getData());
                $questionReponseSiJenesaispas->setImage($JenesaispasExist->get('image')->getData());
                $questionReponseSiJenesaispas->setEstSolution($JenesaispasExist->get('estSolution')->getData());
                $questionReponseSiJenesaispas->setScenario($scenario);
                $questionReponseSiJenesaispas->setEstPremiereQuestion(false);
                $manager->persist($questionReponseSiJenesaispas);
                $questionActuelle->setIdQuestionSiJenesaispas($questionReponseSiJenesaispas);
            }
            //On fait persister nos entités et on les sauvegarde en DB
            $manager->persist($questionActuelle);
            $manager->flush();

            $this->addFlash('success', 'Les questions ont bien été ajoutées');

            return $this->redirectToRoute('new_question_reponse', [
                'formSubmitQuestionReponse' => $formSubmitQuestionReponse->createView(),
                'id' => $scenario->getId()
            ]);
        }

        return $this->render('question_reponse/new.html.twig', [
            'formSubmitQuestionReponse' => $formSubmitQuestionReponse->createView(),
            'id' => $scenario->getId()
        ]);
    }

    /**
     * Méthode permettant de modifier une question
     * @Route("/edit/{id}", name="edit_question_reponse")
     * @param QuestionReponse $questionReponse
     * @param Request $request
     * @return Response
     */
    public function editQuestion(QuestionReponse $questionReponse, Request $request):Response
    {
        $form = $this->createForm(EditQuestionReponseType::class, $questionReponse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La question a bien été modifiée');

            return $this->redirectToRoute('edit_question_reponse', [
                'id' => $questionReponse->getId(),
                'questionReponse' => $questionReponse]);
        }

        return $this->render('question_reponse/edit.html.twig', [
            'id' => $questionReponse,
            'questionReponse' => $questionReponse,
            'formEditQuestionReponse' => $form->createView()
        ]);
    }
}
