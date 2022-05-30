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

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre Adresse E-Mail',
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'Votre Adresse E-Mail'],
                'row_attr' => [
                    'class' => 'form-floating']
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "J'accepte les conditions d'utilisation du service",
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Votre mot de passe',
                    'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Votre Mot de passe'],
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
                'second_options' => ['label' => 'Confirmez votre mot de passe',
                    'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Confirmez votre mot de passe'],
                    'row_attr' => [
                        'class' => 'form-floating col-8']],
            ])
            ->add('civility', EntityType::class, ["class" => Civility::class,
                "label" => "Civilité", "placeholder" => "Sélectionner une civilité"
            ])
            ->add('firstnameUser', TextType::class, ['label' => 'Prénom',
                'attr' => ['autocomplete' => 'firstname', 'placeholder' => 'Prénom'],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir votre prénom avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('lastnameUser', TextType::class, ['label' => 'Nom',
                'attr' => ['autocomplete' => 'lastname', 'placeholder' => 'Nom'],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => "Veuillez saisir votre nom avec au minimum {{ limit }} caractères"
                    ])
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
