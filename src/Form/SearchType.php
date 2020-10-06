<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Property;
use App\Entity\Search;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> 'What are you looking for?'
                ]
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder'=> 'Enter Location'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'placeholder' => 'Select Category',
                'required' => false,
                'attr' => [
                    'data-placeholder' => 'Select Category'
                ],
                'choices' => array_flip(Property::CATEGORY),
                'choice_attr' => function ($choice, $index, $value) {
                    return ['value' => $value];
                }
            ])
            ->add('maxprice', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Maximal Price',
                    'class' => 'form-control small'
                ]
            ])
            ->add('minsurface', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Minimal Surface',
                    'class' => 'form-control small'
                ]
            ])
            ->add('featured', CheckboxType::class, [
                'required' => false
            ])
            ->add('options', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Option::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'placeholder' => 'Take Options'
                ]
            ])
            ->add('lat', HiddenType::class)
            ->add('lng', HiddenType::class)
            ->add('distance', ChoiceType::class,[
                'label' => false,
                'placeholder' => 'Distance',
                'required' => false,
                'choices' => [
                    '5 km' => 5,
                    '10 km' => 10,
                    '50 km' => 50,
                    '100 km' => 100,
                    '1000000 km' => 1000000,
                ],
                'attr' => [
                    'class' => 'small'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
