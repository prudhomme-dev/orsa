<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Votre nouveau mot de passe',
                    'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Votre nouveau Mot de passe'],
                    'row_attr' => [
                        'class' => 'form-floating'],
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
                        'class' => 'form-floating']],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
