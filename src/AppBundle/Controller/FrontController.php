<?php

namespace AppBundle\Controller;

use AppBundle\Form\ContactType;
use AppBundle\Form\Model\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function accueilAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('front/accueil.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/A propos", name="about")
     */
    public function aboutAction()
    {
        // replace this example code with whatever you need
        return $this->render('front/about.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/Jean-Marc Durrieu", name="page_JM")
     */
    public function page_JMAction()
    {
        return $this->render('front/jm.html.twig');
    }

    /**
     * @Route("/Marie-Helene Lavergne", name="page_MH")
     */
    public function page_MHAction()
    {
        return $this->render('front/mh.html.twig');
    }
    
    /**
     * @Route("/Monsieur Serge", name="page_serge")
     */
    public function page_sergeAction()
    {
        return $this->render('front/serge.html.twig');
    }

    /**
     * @Route("/Actualités/{page}", requirements={"page" = "\d+"}, defaults={"page" = 1}, name="actualites")
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
     * @Route("/Contact", name="contact")
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
