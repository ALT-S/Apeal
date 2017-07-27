<?php

namespace BackBundle\Controller;

use AppBundle\Entity\ArticleBlog;
use AppBundle\Form\ArticleBlogType;
use AppBundle\Form\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class BackController extends Controller
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function dashboardAction()
    {
        return $this->render('back/dashboard.html.twig');
    }

    /**
     * @Route("/admin/blog", name="admin_blog_dashboard")
     */
    public function blogDashboardAction()
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:ArticleBlog')->findBy([], ['id' => 'DESC']);

        return $this->render('back/evenementBlog/dashboard.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/dashboard/editArticle/{id}", name="dashboard_editArticle")
     * @ParamConverter("articleblog", class="AppBundle:ArticleBlog")
     */
    public function editArticleAction(ArticleBlog $articleblog, Request $request)
    {
        $articleblogFile = $articleblog->getFile(); // On garde l'ancien fichier pour plus tard

        if ($articleblog->getFile() !== null) {
            $articleblog->setFile(
                new File($this->getParameter('kernel.project_dir') . '/web/upload/blog/' . $articleblogFile)
            );
        }
        $form = $this->get('form.factory')->create(ArticleBlogType::class, $articleblog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $articleblog->getFile();
            if ($file !== null) {// Si un nouveau fichier a été soumis
                // Il faut remplacer l'ancien par le nouveau

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('kernel.project_dir') . '/web/upload/blog/',
                    $fileName
                );
                $articleblog->setFile($fileName);
            }  else {
            // Sinon, on conserve l'ancien fichier
                $articleblog->setFile($articleblogFile);
            }

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('notice', 'L\'évènement a bien été modifié !');

            return $this->redirectToRoute('admin_blog_dashboard', array(
                'id' => $articleblog->getId()
            ));
        }

        return $this->render('back/evenementBlog/editArticleBlog.html.twig', array(
            'form' => $form->createView(),
            'articleBlog' => $articleblog
        ));
    }

    /**
     * @Route("/dashboard/rediger/actualite", name="dashboard_redigerActualite")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function redigerActualiteAction(Request $request)
    {
        $article = new ArticleBlog();
        $article
            ->setDate(new \Datetime())
            ->setPublished(new \Datetime())
            ->setAuthor($this->getUser());

        $form = $this->get('form.factory')->create(ArticleBlogType::class, $article, array(
            'validation_groups' => array('default', 'ajout')
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $article->getFile();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('kernel.project_dir') . '/web/upload/blog/',
                $fileName
            );

            $article->setFile($fileName);

            // On enregistre en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('notice', "L'actualité a bien été enregistrée !");

            return $this->redirectToRoute('admin_blog_dashboard');
        }

        return $this->render(':back/evenementBlog:redigerActualite.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    

    /**
     * @Route("/admin/article/{id}/supprimer", name="admin_supprimer_article")
     * @ParamConverter("article", class="AppBundle:ArticleBlog")
     */
    public function supprimerMusiqueAction(ArticleBlog $article)
    {
        $idArticle = $article->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash('notice', "L'actualité a bien été supprimée !");

        return $this->redirectToRoute('admin_blog_dashboard', ['id' => $idArticle]);
    }
}
