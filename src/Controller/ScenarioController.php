<?php

namespace App\Controller;

use App\Entity\MotCle;
use App\Entity\QuestionReponse;
use App\Entity\Scenario;
use App\Repository\QuestionReponseRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\ScenarioType;
use App\Repository\ScenarioRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/scenario")
 */
class ScenarioController extends Controller
{
    /**
     * Méthode permettant d'afficher la liste des scénarios
     * @Route("/list", name="scenario_list")
     */
    public function index(ScenarioRepository $scenarioRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $scenarioRepository->getWithSearchQueryBuilder($q);
        //On définit le nombre de lignes à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('scenario/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Méthode pour le rendu de la première page lors de l'ajout d'un scénario
     * @Route("/add-first", name="add_first_scenario")
     */
    public function firstStepAddScenario()
    {
        return $this->render('scenario/firstStep.html.twig');
    }

    /**
     * Méthode pour l'ajout du scénario (avant d'ajouter les questions réponses liées)
     * @Route("/add-second", name="add_scenario")
     */
    public function addScenario(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        $scenario = new Scenario();

        $motCle = new MotCle();

        $questionReponse = new QuestionReponse();

        $form = $this->createForm(ScenarioType::class, $scenario);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //On set le scénario avec les données du formulaire et on met les deux booleans sur false
            $scenario->setUser($user);
            $scenario->setNom($form->get('nom')->getData());
            $scenario->setEstTermine(false);
            $scenario->setEstValide(false);
            $scenario->setCreatedAt(new \DateTime());

            //On ajoute les motsClé reçu dans le formulaire
            $motCle->setNom($form->get('motCle')->getData());

            //On set les questions réponses (vu que c'est la première false pour la solution et true pour estPremiereQuestion
            $questionReponse->setQuestion($form->get('question')->getData());
            $questionReponse->setAide($form->get('aide')->getData());
            $questionReponse->setScenario($scenario);
            $questionReponse->setEstSolution(false);
            $questionReponse->setEstPremiereQuestion(true);
            $questionReponse->setImage($form->get('image')->getData());

            //On ajoute les mots clés aux scénarios (table associative)
            $scenario->addMotCle($motCle);

            //On fait persister nos entitées
            $manager->persist($scenario);
            $manager->persist($questionReponse);
            $manager->persist($motCle);

            $manager->flush();

            $this->addFlash('success', 'Le scénario a bien été ajouté');

            $id = $scenario->getId();

            return $this->redirectToRoute('new_question_reponse', ['id' => $id]);

        }

        return $this->render('scenario/add.html.twig', [
            'formAddScenario' => $form->createView(),
        ]);
    }

    /**
     * Méthode pour jouer un scénario sur base de son id et de l'id de la question
     * @Route("/play/{id}/{id_question}", name="scenario_play")
     */
    public function playScenario(Scenario $scenario, QuestionReponse $questionReponse)
    {
        return $this->render('scenario/play.html.twig',
            ['scenario' => $scenario,
            'questionReponse' => $questionReponse]
        );
    }

    /**
     * Méthode permettant de montrer un scénario sur base de son ID reçu en URL
     * @Route("/show/{id}", name="scenario_show", methods="GET")
     */
    public function show(Scenario $scenario): Response
    {
        return $this->render('scenario/show.html.twig', ['scenario' => $scenario]);
    }

    /**
     * Méthode permettant de supprimer un scénario sur base de l'id reçu en URL
     * @Route("/delete/{id}", name="scenario_delete", methods="DELETE")
     */
    public function delete(Request $request, Scenario $scenario, QuestionReponse $questionReponse): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scenario->getId(), $request->request->get('_token')))
        {
            $em = $this->getDoctrine()->getManager();

            $em->remove($scenario->removeQuestionReponse($questionReponse));

            $em->flush();

            $this->addFlash('success', 'Le scénario a bien été supprimée');
        }

        return $this->redirectToRoute('scenario_list');
    }
}