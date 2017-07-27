<?php

namespace BackBundle\Form;

use BackBundle\Entity\MHLCategorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MHLEditCategorieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $start = 2000;
        $years = [];
        for ($i=$start;$i <= date('Y'); $i++) {
            $years[] = $i;
        }
        
        $builder
            ->add('title', DateType::class, ['years' => $years])
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
            'data_class' => MHLCategorie::class // Classe de l'entité utilisé par le formulaire
        ]);
    }


}