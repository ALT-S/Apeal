<?php


namespace UserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use UserBundle\Validator\ValidationMdp;

class ModifMdp extends AbstractType
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('ancienMdp', PasswordType::class, array(
                'mapped' => false,
                'constraints' => array(
                    new Callback(array($this, 'validatePassword'))
                )
                ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Mot de passe :'),
                'second_options' => array('label' => 'Confirmation du mot de passe :')
        ))

            ->add('envoyer', SubmitType::class);
    }

    public function validatePassword($ancienMdp, ExecutionContextInterface $context)
    {
        $user = $context->getRoot()->getData();

        if ($this->passwordEncoder->isPasswordValid($user, $ancienMdp) === false) {
            $context->addViolation('Le mot de passe actuel ne corresponds pas.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_user';
    }
}

