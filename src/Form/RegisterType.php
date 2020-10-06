<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class,[
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' =>'Your Username'
                ]
            ])
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => [
                    'class' => 'form-control',
                    'placeholder' =>'Your password'
                ]],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('firstname', TextType::class,[
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' =>'Your Firstname'
                ]
            ])
            ->add('lastname', TextType::class,[
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' =>'Your Lastname'
                ]
            ])
            ->add('tel', NumberType::class,[
                'required' => false,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' =>'Your Mobile Number'
                ]
            ])
            ->add('email', EmailType::class,[
                'required' => true,
                'attr' => [
                    'class' => "form-control",
                    'placeholder' =>'Your Email'
                ]
            ])
            ->add('civility', ChoiceType::class,[
                'required' => true,
                'choices' => array_flip(User::CIV)
            ])
            ->add('newsletter',CheckboxType::class,[
                'required' => false,
                'label' => 'Receive Newsletter',
                'value' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
