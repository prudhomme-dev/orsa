<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePwdFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("actualPassword", PasswordType::class, ['label' => 'Votre mot de passe actuel', 'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Votre Mot de passe actuel'],
                'row_attr' => [
                    'class' => 'form-floating col-8'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 35,
                    ]),
                ]
            ])
            ->add('newPlainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Votre nouveau mot de passe',
                    'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Votre nouveau Mot de passe'],
                    'row_attr' => [
                        'class' => 'form-floating col-8'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci de renseigner un mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 35,
                        ]),
                    ]
                ],
                'second_options' => ['label' => 'Confirmez nouveau votre mot de passe',
                    'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Confirmez nouveau votre mot de passe'],
                    'row_attr' => [
                        'class' => 'form-floating col-8']],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
