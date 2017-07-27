<?php

namespace BackBundle\Controller;


use BackBundle\Entity\MSMusique;
use BackBundle\Form\MSCreationMusiqueType;
use BackBundle\Form\MusiqueType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;


class MSController extends Controller
{

    /**
     * @Route("/admin/ms", name="admin_ms_dashboard")
     */
    public function dashboardAction()
    {
        $musiques = $this->getDoctrine()->getRepository('BackBundle:MSMusique')->findBy([], ['id' => 'DESC']);
        
        return $this->render('back/ms/dashboard.html.twig', [
            'musiques' => $musiques
        ]);
    }
    

    /**
     * @Route("/dashboard/editMusique/{id}", name="dashboard_editMusique")
     * @ParamConverter("musique", class="BackBundle:MSMusique")
     */
    public function editMusiqueAction(MSMusique $musique, Request $request)
    {

        /*try {
            $musique->setFile(
                new File($this->getParameter('kernel.project_dir') . '/web/upload/photos/ms/' . $musique->getFile())
            );
        } catch ( \Exception $e) {

        }*/

        $form = $this->get('form.factory')->create(MusiqueType::class, $musique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('notice', 'La musique a bien été modifié !');

            return $this->redirectToRoute('admin_ms_dashboard', array(
                'id' => $musique->getId()
            ));
        }

        return $this->render('back/ms/editMusique.html.twig', array(
            'form' => $form->createView(),
            'musique' => $musique
        ));
    }

    /**
     * @Route("/admin/ms/creation/musique", name="admin_ms_creation_musique")
     */
    public function creationMusiqueAction(Request $request)
    {
        $musique = new MSMusique();
        $musique->setDate(new \DateTime());

        $form = $this->createForm(MSCreationMusiqueType::class, $musique);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /*$file = $musique->getFile();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('kernel.project_dir') . '/web/upload/photos/ms/',
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $musique->setFile($fileName);*/


            $em = $this->getDoctrine()->getManager();
            $em->persist($musique);
            $em->flush();

            $this->addFlash('notice', "La musique " . $musique->getTitle() . " a bien été créée");

            return $this->redirectToRoute('admin_ms_dashboard');
        }

        return $this->render('back/ms/creationMusique.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/admin/ms/musique/{id}/supprimer", name="admin_ms_supprimer_musique")
     * @ParamConverter("musique", class="BackBundle:MSMusique")
     */
    public function supprimerMusiqueAction(MSMusique $musique)
    {
        $idMusique = $musique->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($musique);
        $em->flush();

        $this->addFlash('notice', "L'élément a bien été supprimé !");

        return $this->redirectToRoute('admin_ms_dashboard', ['id' => $idMusique]);
    }
}