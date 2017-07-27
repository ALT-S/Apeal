<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ArticleBlog;
use AppBundle\Form\ContactType;
use AppBundle\Form\Model\Contact;
use BackBundle\Entity\JMDCategorie;
use BackBundle\Entity\MHLCategorie;
use BackBundle\Entity\MSMusique;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FrontController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function accueilAction()
    {
        $derniereBlog = $this->getDoctrine()->getRepository('AppBundle:ArticleBlog')->findBy([], ['id' => 'DESC'], 1);
        $derniereJMD = $this->getDoctrine()->getRepository('BackBundle:JMDElement')->findBy([], ['id' => 'DESC'], 1);
        $derniereMHL = $this->getDoctrine()->getRepository('BackBundle:MHLElement')->findBy([], ['id' => 'DESC'], 1);
        $derniereMS = $this->getDoctrine()->getRepository('BackBundle:MSMusique')->findBy([], ['id' => 'DESC'], 1);


        return $this->render('front/accueil.html.twig', [
            'derniereJMD' => reset($derniereJMD),
            'derniereBlog' => reset($derniereBlog),
            'derniereMHL' => reset($derniereMHL),
            'derniereMS' => reset($derniereMS)
        ]);
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function aboutAction()
    {
        return $this->render('front/about.html.twig');
    }

    /**
     * @Route("/jean-marc-durrieu", name="page_JM")
     */
    public function page_JMAction()
    {
        $categories = $this->getDoctrine()->getRepository('BackBundle:JMDCategorie')->findBy([], ['title' => 'ASC']);

        $maxPhotos = 3;
        $dernieresPhotos = $this->getDoctrine()->getRepository('BackBundle:JMDElement')->findBy(['type' => 'photo'], ['id' => 'DESC'], $maxPhotos);

        $maxVideos = 3;
        $dernieresVideos = $this->getDoctrine()->getRepository('BackBundle:JMDElement')->findBy(['type' => 'video'], ['id' => 'DESC'], $maxVideos);

        return $this->render('front/jm.html.twig', [
            'categories' => $categories,
            'dernieresPhotos' => $dernieresPhotos,
            'dernieresVideos' => $dernieresVideos,
        ]);
    }

    /**
     * @Route("/jean-marc-durrieu/categorie/{id}", name="page_JM_categorie")
     * @ParamConverter("categorie", class="BackBundle:JMDCategorie")
     */
    public function page_JM_categorieAction(JMDCategorie $categorie)
    {
        $elements = $this->getDoctrine()->getRepository('BackBundle:JMDElement')->findBy(['categorie' => $categorie->getId()]);

        return $this->render('front/jm_elements.html.twig', [
            'elements' => $elements,
            'categorie' => $categorie
        ]);
    }

    /**
     * @Route("/marie-helene-lavergne", name="page_MH")
     */
    public function page_MHAction()
    {
        $categories = $this->getDoctrine()->getRepository('BackBundle:MHLCategorie')->findBy([], ['title' => 'ASC']);
        
        return $this->render('front/mh.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/marie-helene-lavergne/categorie/{id}", name="page_MH_categorie")
     * @ParamConverter("categorie", class="BackBundle:MHLCategorie")
     */
    public function page_MH_categorieAction(MHLCategorie $categorie)
    {
        $elements = $this->getDoctrine()->getRepository('BackBundle:MHLElement')->findBy(['categorie' => $categorie->getId()]);

        return $this->render('front/mh_elements.html.twig', [
            'elements' => $elements,
            'categorie' => $categorie
        ]);
    }
    
    /**
     * @Route("/monsieur-serge/{page}", requirements={"page" = "\d+"}, defaults={"page" = 1}, name="page_serge")
     */
    public function page_sergeAction($page)
    {
        $nbMusiqueParPage = $this->container->getParameter('front_nb_musique_par_page');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('BackBundle:MSMusique');

        $listMusiques = $repository->findAllPagineEtTrie($page, $nbMusiqueParPage);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($listMusiques) / $nbMusiqueParPage),
            'nomRoute' => 'page_serge',
            'paramsRoute' => array()
        );

        return $this->render('front/serge.html.twig', array(
            'listMusiques' => $listMusiques,
            'pagination' => $pagination
        ));
    }


    /**
     * @Route("/actualites/{page}", requirements={"page" = "\d+"}, defaults={"page" = 1}, name="actualites")
     */
    public function actualitesAction($page)
    {
        $nbNewsParPage = $this->container->getParameter('front_nb_news_par_page');
        
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:ArticleBlog');

        $listArticles = $repository->findAllPagineEtTrie($page, $nbNewsParPage);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($listArticles) / $nbNewsParPage),
            'nomRoute' => 'actualites',
            'paramsRoute' => array()
        );
        
        return $this->render('front/blog.html.twig', array(
            'listArticles' => $listArticles,
            'pagination' => $pagination
        ));
    }

    /**
     * @Route("/actualite/{id}", name="actualite")
     * @ParamConverter("article", class="AppBundle:ArticleBlog")
     */
    public function voirActualiteAction(ArticleBlog $article)
    {
        $em = $this->getDoctrine()->getManager();
        $listArticles = $em->getRepository('AppBundle:ArticleBlog')->findBy([], ['date' => 'desc'], 3);

        return $this->render('front/voirArticleBlog.html.twig', array(
            'listArticles' => $listArticles,
            'article' => $article
        ));
    }
    
    /**
     * @Route("/contact", name="contact")
     * @Method({"GET","POST"})
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();

        $form = $this->get('form.factory')->create(ContactType::class, $contact);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $mailer = $this->get("app.manager.mailContact");
                $mailer->envoyerMailContact($contact);
                $this->addFlash('notice', 'Votre message a bien été envoyé !');

                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render('front/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
