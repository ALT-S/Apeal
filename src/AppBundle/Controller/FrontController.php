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
     * @Route("/Blog", name="blog")
     */
    public function blogAction()
    {
        return $this->render(':front:blog.html.twig');
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
