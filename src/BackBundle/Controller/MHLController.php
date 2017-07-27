<?php

namespace BackBundle\Controller;


use BackBundle\Entity\MHLCategorie;
use BackBundle\Entity\MHLElement;
use BackBundle\Form\MHLCreationCategorieType;
use BackBundle\Form\MHLCreationElementPhotoType;
use BackBundle\Form\MHLEditCategorieType;
use BackBundle\Form\MHLEditElementType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class MHLController extends Controller
{

    /**
     * @Route("/admin/mhl", name="admin_mhl_dashboard")
     */
    public function dashboardAction()
    {
        $categories = $this->getDoctrine()->getRepository('BackBundle:MHLCategorie')->findBy([], ['title' => 'DESC']);
        
        return $this->render('back/mhl/dashboard.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/mhl/category/{id}", name="admin_mhl_liste_elements_par_categorie")
     * @ParamConverter("categorie", class="BackBundle:MHLCategorie")
     */
    public function listeElementsDansCategorieAction(MHLCategorie $categorie)
    {

        $elements = $this->getDoctrine()->getRepository('BackBundle:MHLElement')->findBy(array('categorie' => $categorie));

        return $this->render('back/mhl/liste_elements.html.twig', [
            'elements' => $elements,
            'categorie' => $categorie
        ]);
    }

    /**
     * @Route("/admin/mhl/creation/categorie", name="admin_mhl_creation_categorie")
     */
    public function creationCategorieAction(Request $request)
    {
        $categorie = new MHLCategorie();

        $form = $this->createForm(MHLCreationCategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $categorie->getFile();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('kernel.project_dir') . '/web/upload/photos/mhl/',
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $categorie->setFile($fileName);


            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('notice', "La catégorie " . $categorie->getTitle()->format('d-m-Y') . " a bien été créée");

            return $this->redirectToRoute('admin_mhl_dashboard');
        }

        return $this->render('back/mhl/creationCategorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/mhl/categorie/{id}", name="admin_mhl_edit_categorie")
     * @ParamConverter("categorie", class="BackBundle:MHLCategorie")
     */
    public function editerCategorieAction(MHLCategorie $categorie, Request $request)
    {
        $categorieFile = $categorie->getFile(); // On garde l'ancien fichier pour plus tard

        try {
            $categorie->setFile(
                new File($this->getParameter('kernel.project_dir') . '/web/upload/photos/mhl/' . $categorie->getFile())
            );
        } catch ( \Exception $e) {

        }

        $form = $this->createForm(MHLEditCategorieType::class, $categorie);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $categorie->getFile();
            if ($file !== null) { // Si un nouveau fichier a été soumis
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/web/upload/photos/mhl/',
                    $fileName
                );

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $categorie->setFile($fileName);
            } else {
                $categorie->setFile($categorieFile);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', "L'élément " . $categorie->getTitle()->format('d-m-Y') . " a bien été modifié");

            return $this->redirectToRoute('admin_mhl_liste_elements_par_categorie', ['id' => $categorie->getId()]);
        }

        return $this->render('back/mhl/edit_categorie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/mhl/supprimerCategorie/{id}", name="admin_mhl_supprimer_categorie")
     * @ParamConverter("categorie", class="BackBundle:MHLCategorie")
     */
    public function supprimerCategorieAction(MHLCategorie $categorie)
    {
        $em = $this->getDoctrine()->getManager();

        $elements = $this->getDoctrine()->getRepository('BackBundle:MHLElement')->findBy(['categorie' => $categorie]);
        foreach ($elements as $element) {
            $em->remove($element);
        }
        $em->flush();

        $em->remove($categorie);
        $em->flush();

        $this->addFlash('notice', "La catégorie a bien été supprimée !");

        return $this->redirectToRoute('admin_mhl_dashboard');
    }

    /**
     * @Route("/admin/mhl/categorie/{id}/creation/photo", name="admin_mhl_creation_element_photo")
     * @ParamConverter("categorie", class="BackBundle:MHLCategorie")
     */
    public function creationElementPhotoAction(MHLCategorie $categorie, Request $request)
    {
        $element = new MHLElement();
        $element->setCategorie($categorie);

        $form = $this->createForm(MHLCreationElementPhotoType::class, $element);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $element->getFile();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('kernel.project_dir') . '/web/upload/photos/mhl/',
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $element->setFile($fileName);


            $em = $this->getDoctrine()->getManager();
            $em->persist($element);
            $em->flush();

            $this->addFlash('notice', "L'élément " . $element->getTitle() . " a bien été créé");

            return $this->redirectToRoute('admin_mhl_liste_elements_par_categorie', ['id' => $categorie->getId()]);
        }

        return $this->render('back/mhl/creationElementPhoto.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie
        ]);
    }
    
    /**
     * @Route("/admin/mhl/element/{id}", name="admin_mhl_edit_element")
     * @ParamConverter("element", class="BackBundle:MHLElement")
     */
    public function editerElementAction(MHLElement $element, Request $request)
    {

        $elementFile = $element->getFile(); // On garde l'ancien fichier pour plus tard

        $element->setFile(
            new File($this->getParameter('kernel.project_dir') . '/web/upload/photos/mhl/' . $element->getFile())
        );

        $form = $this->createForm(MHLEditElementType::class, $element);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $element->getFile();
            if ($file !== null) { // Si un nouveau fichier a été soumis
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/web/upload/photos/mhl/',
                    $fileName
                );

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $element->setFile($fileName);
            } else {
                $element->setFile($elementFile);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', "L'élément " . $element->getTitle() . " a bien été modifié");

            return $this->redirectToRoute('admin_mhl_liste_elements_par_categorie', ['id' => $element->getCategorie()->getId()]);
        }

        return $this->render('back/mhl/edit_element.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/mhl/element/{id}/supprimer", name="admin_mhl_supprimer_element")
     * @ParamConverter("element", class="BackBundle:MHLElement")
     */
    public function supprimerElementAction(MHLElement $element)
    {
        $idCategorie = $element->getCategorie()->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($element);
        $em->flush();

        $this->addFlash('notice', "L'élément a bien été supprimé !");

        return $this->redirectToRoute('admin_mhl_liste_elements_par_categorie', ['id' => $idCategorie]);
    }

   
}