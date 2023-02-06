<?php

namespace App\Form;

use Assert\NotBlank;
use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Votre nom"
                ],
                'label' => "Nom",
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Votre prénom"
                ],
                'label' => "Votre Prénom",
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Votre email"
                ],
                'label' => "Email",
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ]
            ])
            ->add('object', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Objet"
                ],
                'label' => "Objet de la demande",
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 2,
                        'minMessage' => "L'objet de votre demande doit avoir au moins {{ limit }} caractères",
                        'max' => 100,
                        'maxMessage' => "L'objet de votre demande doit avoir au maximum {{ limit }} caractères"
                    ]),
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 5
                ],
                'label' => 'Votre Message',
                'label_attr' => [
                    'class' => 'mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
