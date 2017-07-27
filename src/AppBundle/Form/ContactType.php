<?php

namespace AppBundle\Form;


use AppBundle\Form\Model\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * Classe qui gère le formulaire de la demande de contact
 *
 */
class ContactType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',    TextType::class)
            ->add('prenom',    TextType::class)
            ->add('email',    EmailType::class)
            ->add('contenu',   TextareaType::class)
            ->add('envoyer',      SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Contact::class
        ));
    }

}