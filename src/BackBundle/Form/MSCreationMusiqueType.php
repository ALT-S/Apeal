<?php

namespace BackBundle\Form;


use BackBundle\Entity\MSMusique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MSCreationMusiqueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        
        $builder
            ->add('title', TextType::class)
            ->add('date',DateType::class)
            //->add('file', FileType::class)
            ->add('lien', TextType::class)
            ->add('Enregistrer', SubmitType::class)
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MSMusique::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}