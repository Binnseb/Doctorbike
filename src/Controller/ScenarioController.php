<?php

namespace App\Controller;

use App\Entity\Historique;
use App\Entity\QuestionReponse;
use App\Entity\Scenario;
use App\Form\VoteNonType;
use App\Form\VoteOuiType;
use App\Form\VoteType;
use App\Repository\HistoriqueRepository;
use App\Repository\MotCleRepository;
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
     * @param ScenarioRepository $scenarioRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(ScenarioRepository $scenarioRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        //q représente le texte inséré dans la barre de recherche du formulaire
        $q = $request->query->get('q');
        $queryBuilder = $scenarioRepository->getWithSearchQueryBuilder($q);
        //On définit le nombre de lignes à afficher par page
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('scenario/index.html.twig', [
            'pagination' => $pagination,
            'user' => $user
        ]);
    }

    /**
     * Méthode pour le rendu de la première page lors de l'ajout d'un scénario
     * @Route("/add-first", name="add_first_scenario")
     * @return Response
     */
    public function firstStepAddScenario()
    {
        return $this->render('scenario/firstStep.html.twig');
    }

    /**
     * Méthode pour l'ajout du scénario (avant d'ajouter les questions réponses liées)
     * @Route("/add-second", name="add_scenario")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addScenario(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        $scenario = new Scenario();

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

            // Liste mots-clé
            $motscle = $form->get('motCle')->getData();

            //On set les questions réponses (vu que c'est la première false pour la solution et true pour estPremiereQuestion
            $questionReponse->setQuestion($form->get('question')->getData());
            $questionReponse->setAide($form->get('aide')->getData());
            $questionReponse->setScenario($scenario);
            $questionReponse->setEstSolution(false);
            $questionReponse->setEstPremiereQuestion(true);
            $questionReponse->setImage($form->get('image')->getData());

            //On ajoute les mots clés aux scénarios (table associative)

            foreach ($motscle as $element)
            {
                $scenario->addMotCle($element);
                $manager->persist($element);
            }

            //On fait persister nos entitées
            $manager->persist($scenario);
            $manager->persist($questionReponse);

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
     * Méthode pour jouer un scénario sur base de son id et de l'id de la question (contient aussi le vote positif ou négatif)
     * @Route("/play/{id}/{id_question}", name="scenario_play")
     * @ParamConverter("questionReponse", class="App\Entity\QuestionReponse", options={"id" = "id_question"})
     * @param Scenario $scenario
     * @param QuestionReponse $questionReponse
     * @return Response
     */
    public function playScenario(Scenario $scenario, QuestionReponse $questionReponse, Request $request, ObjectManager $manager, HistoriqueRepository $historiqueRepository)
    {
        $user = $this->getUser();

        $historique = new Historique();

        $formVoteOui = $this->createForm(VoteOuiType::class, $historique, ['id' => $user->getId()]);
        $formVoteNon = $this->createForm(VoteNonType::class, $historique, ['id' => $user->getId()]);

        $formVoteOui->handleRequest($request);
        $formVoteNon->handleRequest($request);

        //On vérifie si l'utilisateur à déjà voté pour cette solution avec la combinaison User/Moto/Scénario/Solution
        $voteExist = $historiqueRepository->findVoteByUserMotoScenarioSolution($user, $user->getMotoActive(), $scenario, $questionReponse);
        //Si il existe un vote on indique à Symfony de sélectionner la première occurence trouvée dans le tableau
        if(sizeof($voteExist) > 0)
            $voteExist = $voteExist[0];
        // Si l'utilisateur submit oui ou non
        if(
            ($formVoteOui->isSubmitted() && $formVoteOui->isValid()) || // oui
            ($formVoteNon->isSubmitted() && $formVoteNon->isValid()) // non
        )
        {
            //Si un vote existe déjà
            if ($voteExist != null)
            {
                //On vérifie si le formulaire oui est soumis pour setter la bonne valeur (0 si non 1 si oui)
                if ($formVoteOui->isSubmitted())
                {
                    $voteExist->setVoteReponse(true);
                    $historique->setCreatedAt(new \Datetime);
                }
                //Si ce n'est pas le formulaire oui on set à false (non) et on reset la date
                else
                {
                    $voteExist->setVoteReponse(false);
                    $historique->setCreatedAt(new \Datetime);
                }

                $manager->persist($voteExist);
            }
            //Si un vote n'existe pas pour la combinaison User/Moto/Scénario/Solution
            else
            {
                $historique->setScenario($scenario);
                $historique->setSolution($questionReponse);
                $historique->setUser($user);
                $historique->setCreatedAt(new \Datetime);
                $historique->setMoto($user->getMotoActive());

                //On vérifie quel formulaire est soumis. Si c'est le oui on set la valeur à true sinon à false
                if ($formVoteOui->isSubmitted()) // Si c'est bien le form oui
                    $historique->setVoteReponse(true);
                else // Si c'est le form non
                    $historique->setVoteReponse(false);

                $manager->persist($historique);
            }

            $manager->flush();

            $this->addFlash('success', 'Votre vote a bien été enregistré');

            return $this->redirectToRoute('scenario_list');
        }

        return $this->render('scenario/play.html.twig', [
            'scenario' => $scenario,
            'questionReponse' => $questionReponse,
            'formVoteOui' => $formVoteOui->createView(),
            'formVoteNon' => $formVoteNon->createView(),
            'user' => $user
            ]);
    }

    /**
     * Méthode permettant de montrer un scénario sur base de son ID reçu en URL
     * @Route("/show/{id}", name="scenario_show", methods="GET")
     * @param Scenario $scenario
     * @return Response
     */
    public function show(Scenario $scenario): Response
    {
        return $this->render('scenario/show.html.twig', ['scenario' => $scenario]);
    }

    /**
     * Méthode permettant de supprimer un scénario sur base de l'id reçu en URL
     * @Route("/delete/{id}", name="scenario_delete", methods="DELETE")
     * @param Scenario $scenario
     * @param QuestionReponseRepository $questionReponseRepository
     * @param MotCleRepository $motCleRepository
     * @param Request $request
     * @return Response
     */
    public function delete(Scenario $scenario, QuestionReponseRepository $questionReponseRepository, MotCleRepository $motCleRepository, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scenario->getId(), $request->request->get('_token')))
        {
            $em = $this->getDoctrine()->getManager();

            $questionReponses = $questionReponseRepository->findAllQuestions($scenario->getId());

            $motCles = $motCleRepository->findBy(['id' => $scenario->getId()]);

            foreach ($questionReponses as $element)
            {
                $em->remove($scenario->removeQuestionReponse($element));
            }

            foreach ($motCles as $element)
            {
                $scenario->removeMotCle($element);
            }

            $em->flush();

            $this->addFlash('success', 'Le scénario a bien été supprimée');
        }

        return $this->redirectToRoute('scenario_list');
    }
}
