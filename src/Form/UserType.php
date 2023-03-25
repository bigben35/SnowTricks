<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Nom d'utilisateur"
                ],
                'label' => "Nom d'Utilisateur"
            ])
            // ->add('roles')
            // ->add('password')
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Email"
                ],
                'label' => "Email"
            ])
            ->add('avatar', FileType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Avatar"
                ],
                'label' => "Avatar (jpg, jpeg, png)",
                'required' => false,
                'mapped' => false,
                'multiple' => false,
                'constraints' => [
                    new All([
                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'image/jpg',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid image type',
                            ])
                        ]
                    ])
                ],
                ])
                ->add('is_verified')
                // ->add('resetToken')
                ;
                $avatarType = $builder->get('avatar')->getType()->getInnerType();
                var_dump($avatarType);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
