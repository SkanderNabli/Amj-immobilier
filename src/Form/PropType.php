<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Title',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your title'
                ]
            ])
            ->add('descr', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Description of your property',
                    'rows' => '4'
                ]
            ])
            ->add('surface', NumberType::class, [
                'label' => 'Area',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your area'
                ]
            ])
            ->add('rooms', NumberType::class, [
                'label' => 'Rooms',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your number rooms'
                ]
            ])
            ->add('bedrooms',NumberType::class,[
                'label' => 'Bedrooms',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your number bedrooms'
                ]
            ])
            ->add('floor', NumberType::class, [
                'label' => 'Floor',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your floor'
                ]
            ])
            ->add('price', NumberType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your price'
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            ->add('heat', ChoiceType::class, [
                'placeholder' => 'Select type heat',
                'required' => false,
                'choices' => array_flip(Property::HEAT)
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => 'City',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your title'
                ]
            ])
            ->add('address', TextType::class, [
                'required' => true,
                'label' => 'Address',
                'label_attr' => [
                    'class' => 'col-form-label'
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter Location'
                ]
            ])
            ->add('postal_code', NumberType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your postal code'
                ],
                'label_attr' => [
                    'class' => 'col-form-label'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'placeholder' => 'Select Category',
                'required' => true,
                'attr' => [
                    'class' => 'change-tab',
                    'data-change-tab-target' => 'category-tabs',
                    'data-placeholder' => 'Select Category'
                ],
                'choices' => array_flip(Property::CATEGORY),
                'choice_attr' => function ($choice, $index, $value) {
                    return ['value' => $value];
                }

            ])
            ->add('options', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Option::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'list-unstyled columns-2']
            ])
            ->add('imageFiles',FileType::class,[
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'file-upload-input with-preview',
                    'title' => 'Click to add files',
                    'accept' => 'jpeg|jpg|png'
                ]
            ])
            ->add('lat' , HiddenType::class)
            ->add('lng' , HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms'
        ]);
    }
}
