<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 18/07/2017
 * Time: 16:03
 */

namespace BackBundle\Controller;


use BackBundle\Entity\JMDCategorie;
use BackBundle\Entity\JMDElement;
use BackBundle\Form\CreationCategorieType;
use BackBundle\Form\CreationElementPhotoType;
use BackBundle\Form\CreationElementVideoType;
use BackBundle\Form\EditCategorieType;
use BackBundle\Form\EditElementType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class JMDController extends Controller
{

    /**
     * @Route("/admin/jmd", name="admin_jmd_dashboard")
     */
    public function dashboardAction()
    {
        $categories = $this->getDoctrine()->getRepository('BackBundle:JMDCategorie')->findBy([], ['id' => 'DESC']);

        return $this->render('back/jmd/dashboard.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/jmd/category/{id}", name="admin_jmd_liste_elements_par_categorie")
     * @ParamConverter("categorie", class="BackBundle:JMDCategorie")
     */
    public function listeElementsDansCategorieAction(JMDCategorie $categorie)
    {

        $elements = $this->getDoctrine()->getRepository('BackBundle:JMDElement')->findBy(['categorie' => $categorie], ['id' => 'DESC']);

        return $this->render('back/jmd/liste_elements.html.twig', [
            'elements' => $elements,
            'categorie' => $categorie
        ]);
    }

    /**
     * @Route("/admin/jmd/creation/categorie", name="admin_jmd_creation_categorie")
     */
    public function creationCategorieAction(Request $request)
    {
        $categorie = new JMDCategorie();

        $form = $this->createForm(CreationCategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $categorie->getFile();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('kernel.project_dir') . '/web/upload/photos/jmd/',
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $categorie->setFile($fileName);


            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('notice', "La catégorie " . $categorie->getTitle() . " a bien été créée");

            return $this->redirectToRoute('admin_jmd_dashboard');
        }

        return $this->render('back/jmd/creationCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/jmd/categorie/{id}", name="admin_jmd_edit_categorie")
     * @ParamConverter("categorie", class="BackBundle:JMDCategorie")
     */
    public function editerCategorieAction(JMDCategorie $categorie, Request $request)
    {
        $categorieFile = $categorie->getFile(); // On garde l'ancien fichier pour plus tard
        try {
            $categorie->setFile(
                new File($this->getParameter('kernel.project_dir') . '/web/upload/photos/jmd/' . $categorie->getFile())
            );
        } catch (\Exception $e) {

        }

        $form = $this->createForm(EditCategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $categorie->getFile();
            if ($file !== null) { // Si un nouveau fichier a été soumis
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/web/upload/photos/jmd/',
                    $fileName
                );

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $categorie->setFile($fileName);
            } else {
                $categorie->setFile($categorieFile);
            }
            
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', "L'élément " . $categorie->getTitle() . " a bien été modifié");

            return $this->redirectToRoute('admin_jmd_liste_elements_par_categorie', ['id' => $categorie->getId()]);
        }

        return $this->render('back/jmd/edit_categorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/jmd/supprimerCategorie/{id}", name="admin_jmd_supprimer_categorie")
     * @ParamConverter("categorie", class="BackBundle:JMDCategorie")
     */
    public function supprimerCategorieAction(JMDCategorie $categorie)
    {
        $em = $this->getDoctrine()->getManager();

        $elements = $this->getDoctrine()->getRepository('BackBundle:JMDElement')->findBy(['categorie' => $categorie]);
        foreach ($elements as $element) {
            $em->remove($element);
        }
        $em->flush();

        $em->remove($categorie);
        $em->flush();

        $this->addFlash('notice', "La catégorie a bien été supprimée !");

        return $this->redirectToRoute('admin_jmd_dashboard');
    }

    /**
     * @Route("/admin/jmd/categorie/{id}/creation/photo", name="admin_jmd_creation_element_photo")
     * @ParamConverter("categorie", class="BackBundle:JMDCategorie")
     */
    public function creationElementPhotoAction(JMDCategorie $categorie, Request $request)
    {
        $element = new JMDElement();
        $element->setType('photo');
        $element->setCategorie($categorie);

        $form = $this->createForm(CreationElementPhotoType::class, $element);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $element->getFile();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('kernel.project_dir') . '/web/upload/photos/jmd/',
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $element->setFile($fileName);


            $em = $this->getDoctrine()->getManager();
            $em->persist($element);
            $em->flush();

            $this->addFlash('notice', "L'élément " . $element->getTitle() . " a bien été créé");

            return $this->redirectToRoute('admin_jmd_liste_elements_par_categorie', ['id' => $categorie->getId()]);
        }

        return $this->render('back/jmd/creationElementPhoto.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie
        ]);
    }

    /**
     * @Route("/admin/jmd/categorie/{id}/creation/video", name="admin_jmd_creation_element_video")
     * @ParamConverter("categorie", class="BackBundle:JMDCategorie")
     */
    public function creationElementVideoAction(JMDCategorie $categorie, Request $request)
    {
        $element = new JMDElement();
        $element->setType('video');
        $element->setCategorie($categorie);

        $form = $this->createForm(CreationElementVideoType::class, $element);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($element);
            $em->flush();

            $this->addFlash('notice', "L'élément " . $element->getTitle() . " a bien été créé");

            return $this->redirectToRoute('admin_jmd_liste_elements_par_categorie', ['id' => $categorie->getId()]);
        }

        return $this->render('back/jmd/creationElementVideo.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie
        ]);
    }

    /**
     * @Route("/admin/jmd/element/{id}", name="admin_jmd_edit_element")
     * @ParamConverter("element", class="BackBundle:JMDElement")
     */
    public function editerElementAction(JMDElement $element, Request $request)
    {
        $elementFile = $element->getFile(); // On garde l'ancien fichier pour plus tard

        if ($element->getType() == 'photo') {
            $element->setFile(
                new File($this->getParameter('kernel.project_dir') . '/web/upload/photos/jmd/' . $elementFile)
            );
        }

        $form = $this->createForm(EditElementType::class, $element);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $element->getFile();
            if ($file !== null) { // Si un nouveau fichier a été soumis
                // Il faut remplacer l'ancien par le nouveau

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/web/upload/photos/jmd/',
                    $fileName
                );

                $element->setFile($fileName);
            } else {
                // Sinon, on conserve l'ancien fichier
                $element->setFile($elementFile);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', "L'élément " . $element->getTitle() . " a bien été modifié");

            return $this->redirectToRoute('admin_jmd_liste_elements_par_categorie', ['id' => $element->getCategorie()->getId()]);
        }

        return $this->render('back/jmd/edit_element.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/jmd/element/{id}/supprimer", name="admin_jmd_supprimer_element")
     * @ParamConverter("element", class="BackBundle:JMDElement")
     */
    public function supprimerElementAction(JMDElement $element)
    {
        $idCategorie = $element->getCategorie()->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($element);
        $em->flush();

        $this->addFlash('notice', "L'élément a bien été supprimé !");

        return $this->redirectToRoute('admin_jmd_liste_elements_par_categorie', ['id' => $idCategorie]);
    }
}