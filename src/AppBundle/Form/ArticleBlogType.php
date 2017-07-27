<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 19/06/2017
 * Time: 13:08
 */

namespace AppBundle\Form;


use AppBundle\Entity\ArticleBlog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleBlogType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',     TextType::class, array(
                'label' => 'Titre de l\'évènement :' ))
            ->add('dateEvenement', DateType::class)
            ->add('file',      FileType::class , array(
                'label' => 'Photo de l\'évènement :',
                'required' => false))
            ->add('content',   TextareaType::class, array(
                'required' => false,
                    'label' => 'Votre évènement :')
            )
            ->add('enregistrer',      SubmitType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleBlog::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}