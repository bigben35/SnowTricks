<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('slug', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Slug"
                ],
                'label' => "Slug"
            ])
            ->add('illustration', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Illustration"
                ],
                'label' => "Illustration"
            ])
            ->add('video', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Vidéo"
                ],
                'label' => "Video"
            ])
            // ->add('created_at')
            // ->add('modified_at')
            ->add('categories', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Catégorie"
                ],
                'label' => "Catégorie"
            ])
            ->add('user', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Créateur"
                ],
                'label' => "Créateur"
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
