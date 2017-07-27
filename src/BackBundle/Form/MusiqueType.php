<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 19/06/2017
 * Time: 13:08
 */

namespace BackBundle\Form;



use BackBundle\Entity\MSMusique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MusiqueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',     TextType::class, array(
                'label' => 'Titre de la musique :' ))
            ->add('date',DateType::class)
            /*->add('file',      FileType::class , array(
                'label' => 'Photo de la musique :',
                'required' => false))*/
            ->add('lien', TextType::class)
            ->add('enregistrer',      SubmitType::class)
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