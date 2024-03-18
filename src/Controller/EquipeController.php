<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Equipe;
use App\Entity\Image;
use App\Entity\Joueur;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\EquipeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;




class EquipeController extends AbstractController
{
    #[Route('/equipe', name: 'app_team')]

    public function index(Request $request): Response
    {
        
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipe);
            $entityManager->flush();
            return $this->redirectToRoute('equipe_details', ['id' => $equipe->getId()]);
        }
        return $this->render('equipe/ajouterEquipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
/**
* @Route("/voir/{id}", name="equipe_details")
*/
public function show($id)
{
 $equipe = $this->getDoctrine()
 ->getRepository(Equipe::class)
 ->find($id);
 $em=$this->getDoctrine()->getManager();
 $listJoueurs=$em->getRepository(Joueur::class)
 ->findBy(['equipe'=>$equipe]);
 if (!$equipe) {
 throw $this->createNotFoundException(
 'No equipe found for id '.$id
 );
 }

return $this->render('equipe/voir.html.twig', [
'listJoueurs'=> $listJoueurs,
 'equipe' =>$equipe
 ]);
 }
/**
 * @Route("/Ajouter", name="Ajouter")
 */
public function ajouter(Request $request)
{
    $joueur = new Joueur();

    $form = $this->createFormBuilder($joueur)
        ->add('NomJoueur', TextType::class)
        ->add('nationalite', TextType::class, ["label" => "nationalite"])
        ->add('poste', TextType::class)
        ->add('salaire', TextType::class)
        ->add('picFile', FileType::class, [ // Utilisez FileType pour le champ de fichier
            'label' => 'Pic du joueur',
            'required' => false,
        ])
        ->add('equipe', EntityType::class, [
            'class' => Equipe::class,
            'choice_label' => 'nomEquipe',
        ])
        ->add('valider', SubmitType::class)
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();

        $em->persist($joueur);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    return $this->render('equipe/ajouter.html.twig', ['f' => $form->createView()]);
}
  /**
* @Route("/add", name="ajouter_equipe")
*/
public function ajouter2(Request $request){
    $equipe = new Equipe();
    $form = $this->createForm("App\Form\EquipeType",$equipe);
    $form -> handleRequest($request);
    if ($form->isSubmitted())
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($equipe);
        $em->flush();
        return $this->redirectToRoute('app_equipe');
    }
    return $this->render('equipe/ajouter.html.twig',
    ['f'=>$form->createView()]);
}
  /**
* @Route("/home", name="home")
*/
public function home()
{
    $em = $this->getDoctrine()->getManager();
    $repo = $em->getRepository(Joueur::class);
    $lesJoueurs = $repo->findAll();
    return $this->render('equipe/home2.html.twig',['lesJoueurs'=>$lesJoueurs]);
}
 /**
* @Route("/supp/{id}", name="joueur_delete")
*/
public function delete(Request $request,$id):Response
{
    $c = $this->getDoctrine()
        ->getRepository(Joueur::class)
        ->find($id);
    if (!$c){
        throw $this->createNotFoundException(
            "aucun joueur trouvé pour l'id".$id
        );
    }
    $entityManager=$this ->getDoctrine()->getManager();
    $entityManager->remove($c);
    $entityManager->flush();
    return $this->redirectToRoute('home');
}
/**
* @Route("/editU/{id}", name="edit")
* Method({"GET","POST"})
*/
public function edit(Request $request, $id)
{ $joueur = new Joueur();
$joueur = $this->getDoctrine()
->getRepository(Joueur::class)
->find($id);
if (!$joueur) {
throw $this->createNotFoundException(
"aucun joueur trouvé pour l'id".$id
);
}
$fb = $this->createFormBuilder($joueur)
->add('NomJoueur',TextType::class)
->add('nationalite',TextType::class,array("label"=>"nationalite"))
->add('poste',TextType::class)
->add('salaire',TextType::class)
->add('equipe',EntityType::class, [
'class' => Equipe::class,
'choice_label'=>'nomEquipe',
])
->add('Valider', SubmitType::class);
$form = $fb->getForm();
$form->handleRequest($request);
if ($form->isSubmitted()) {
$entityManager = $this->getDoctrine()->getManager();
$entityManager->flush();
return $this->redirectToRoute('home');
}
return $this->render('equipe/ajouter.html.twig',
['f' => $form->createView()] );
}

/**
 * @Route("/voirEquipe", name="voir_equipes")
 */
public function voir_equipes()
    {
        $equipes = $this->getDoctrine()->getRepository(Equipe::class)->findAll();

        return $this->render('equipe/voirEquipes.html.twig', [
            'equipes' => $equipes,
        ]);
    }
    /**
 * @Route("/voir_joueur/{id}", name="voir_joueur")
 */
public function voir_joueur($id)
{
    $joueur = $this->getDoctrine()
        ->getRepository(Joueur::class)
        ->find($id);

    if (!$joueur) {
        throw $this->createNotFoundException(
            'Aucun joueur trouvé pour l\'id ' . $id
        );
    }

    return $this->render('equipe/voirJoueur.html.twig', [
        'joueur' => $joueur,
    ]);
}
/**
 * @Route("/AjouterJoueur/{equipeId}", name="AjouterJoueur")
 */
public function ajouterJoueur(Request $request, $equipeId)
{
    $joueur = new Joueur();

    $form = $this->createFormBuilder($joueur)
        ->add('NomJoueur', TextType::class)
        ->add('nationalite', TextType::class, ['label' => 'Nationalité'])
        ->add('poste', TextType::class)
        ->add('salaire', TextType::class)
        ->add('picFile', FileType::class, [ // Utilisez FileType pour le champ de fichier
            'label' => 'Pic du joueur',
            'required' => false,
        ])
        ->add('valider', SubmitType::class)
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $equipe = $em->getRepository(Equipe::class)->find($equipeId);
        $joueur->setEquipe($equipe);

        $em->persist($joueur);
        $em->flush();

        return $this->redirectToRoute('home');
    }

    return $this->render('equipe/ajouterJoueur.html.twig', ['form' => $form->createView()]);
}
/**
 * @Route("/modif/{id}", name="modifier_equipe")
 * Method({"GET", "POST"})
 */
public function modifier(Request $request, $id): Response
{
    $equipe = $this->getDoctrine()->getRepository(Equipe::class)->find($id);
    if (!$equipe) {
        throw $this->createNotFoundException('Équipe non trouvée pour l\'ID ' . $id);
    }
    $form = $this->createForm(EquipeType::class, $equipe);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $logoFile = $form->get('logoFile')->getData();
    
        if ($logoFile !== null) {
            $equipe->setLogo($logoFile->getFilename());
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('equipe_details', ['id' => $equipe->getId()]);
    }
    return $this->render('equipe/modifierEquipe.html.twig', [
        'form' => $form->createView(),
    ]);
}
/**
* @Route("/equipes2", name="voir_Equipes2")
*/
public function artiste_Equipes2()
{
    $em = $this->getDoctrine()->getManager();
    $repo = $em->getRepository(Equipe::class);
    $lesEquipes = $repo->findAll();
    return $this->render('equipe/voirEquipes2.html.twig',['lesEquipes'=>$lesEquipes]);
}
   /**
* @Route("/joueurs2", name="voir_Joueurs2")
*/
public function voir_Joueurs2()
{
    $em = $this->getDoctrine()->getManager();
    $repo = $em->getRepository(Joueur::class);
    $listJoueurs = $repo->findAll();
    return $this->render('equipe/voirJoueurs2.html.twig',['listJoueurs'=>$listJoueurs]);
}
 /**
 * @Route("/", name="hello")
 */
public function hello()
{
return $this->render('equipe/hello.html.twig');
} 

   
}
