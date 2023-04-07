<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Video;
use App\Form\VideoType;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
                    'style' => 'height: 150px;',
                    'placeholder' => "Description"
                ],
                'label' => "Description"
            ])
            ->add('files', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Illustrations"
                ],
                'label' => "Illustration (formats images uniquements)",
                'mapped' => false,
                'required' => false,
                'multiple' => true,

                // Contrainte sur le type des images de type multiple
                // Voir la résolution ici : https://stackoverflow.com/questions/61589890/multiple-file-validation-this-value-should-be-of-type-string
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
                                'maxSizeMessage' => 'Le fichier image ne doit pas dépasser 1 Mo',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'image/webp',
                                ],
                                'mimeTypesMessage' => 'Veuillez télécharger une image valide',
                            ])
                        ]
                    ])
                ],
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'error_bubbling' => false,
                'prototype' => true,
                'required' => false
            ])
            ->add('categories', null, [
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
