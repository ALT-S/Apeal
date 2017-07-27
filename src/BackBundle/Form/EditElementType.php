<?php
/**
 * Created by PhpStorm.
 * User: Anne-Laure
 * Date: 18/07/2017
 * Time: 18:14
 */

namespace BackBundle\Form;


use BackBundle\Entity\JMDElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditElementType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('Modifier', SubmitType::class)
         ;

        if ($options['data']->getType() == 'video') {
            $builder->add('url', UrlType::class);
        } elseif ($options['data']->getType() == 'photo') {
            $builder->add('file', FileType::class, ['required' => false]);
        }

    }

    /**
     * {@inheritdoc}
     */
   public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JMDElement::class, // Classe de l'entité utilisé par le formulaire
        ]);
    }


}