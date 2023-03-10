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
use Symfony\Component\Validator\Constraints\All;

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
            ->add('files', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Illustrations"
                ],
                'label' => "Illustration (formats images uniquements)",
                // 'data_class' => null,
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'multiple' => true,

                // Contrainte sur le type des images de type multiple
                // Voir la r??solution ici : https://stackoverflow.com/questions/61589890/multiple-file-validation-this-value-should-be-of-type-string
                'constraints' => [
                    new All([
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
                        ]
                    ])
                ],
            ])
            ->add('video_1', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Lien Vid??o 1"
                ],
                'label' => "Lien Video 1 (3 Max.)",
                'mapped' => false,
                'required' => false
            ])
            // ->add('video_2', TextareaType::class, [
            //     'attr' => [
            //         'class' => 'form-control',
            //         'placeholder' => "Lien Vid??o 2"
            //     ],
            //     'label' => "Lien Video 2",
            //     'mapped' => false,
            //     'required' => false
            // ])
            // ->add('video_3', TextareaType::class, [
            //     'attr' => [
            //         'class' => 'form-control',
            //         'placeholder' => "Lien Vid??o 3"
            //     ],
            //     'label' => "Lien Video 3",
            //     'mapped' => false,
            //     'required' => false
            // ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Cat??gorie"
                ],
                'label' => "Cat??gorie",
                'mapped' => false,
                'required' => false,
            ])
            // ->add('user', TextType::class, [
            //     'attr' => [
            //         'class' => 'form-control',
            //         'placeholder' => "Cr??ateur"
            //     ],
            //     'label' => "Cr??ateur"
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
