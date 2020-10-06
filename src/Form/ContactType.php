<?php


namespace App\Form;


use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Your Name'

                ]
            ])
            ->add('subject', TextType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Subject'

                ]
            ])
            ->add('tel', NumberType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Your Number'

                ]
            ])
            ->add('email', EmailType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Your Email'

                ]
            ])
            ->add('message', TextareaType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'rows' => '4',
                    'placeholder' => 'Your Message'

                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }

}