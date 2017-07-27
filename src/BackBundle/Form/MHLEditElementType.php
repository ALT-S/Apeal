<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 18/07/2017
 * Time: 18:14
 */

namespace BackBundle\Form;


use BackBundle\Entity\MHLElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MHLEditElementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class)
            ->add('file', FileType::class, ['required' => false])
            ->add('Modifier', SubmitType::class)
         ;

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
             'data_class' => MHLElement::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}