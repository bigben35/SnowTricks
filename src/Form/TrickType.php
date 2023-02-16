<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Nom"
                ],
                'label' => "Nom"
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Description"
                ],
                'label' => "Description"
            ])
            // ->add('slug', TextType::class, [
            //     'attr' => [
            //         'class' => 'form-control',
            //         'placeholder' => "Slug"
            //     ],
            //     'label' => "Slug",
            //     'required' => false
            // ])
            ->add('filename', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Illustration"
                ],
                'label' => "Illustration (formats images uniquements)",
                // 'data_class' => null,
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // 'multiple' => true,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image type',
                    ])
                ],
            ])
            ->add('video', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Vidéo"
                ],
                'label' => "Video",
                'data_class' => null
            ])
            // ->add('created_at')
            // ->add('modified_at')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Catégorie"
                ],
                'label' => "Catégorie"
            ])
            // ->add('user', TextType::class, [
            //     'attr' => [
            //         'class' => 'form-control',
            //         'placeholder' => "Créateur"
            //     ],
            //     'label' => "Créateur"
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
