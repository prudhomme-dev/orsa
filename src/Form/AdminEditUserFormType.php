<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdminEditUserFormType extends AbstractType
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
            ->add('civility', EntityType::class, ["class" => Civility::class,
                "label" => false, "placeholder" => "Sélectionner une civilité"
            ])
            ->add('firstnameUser', TextType::class, ['label' => 'Prénom',
                'attr' => ['autocomplete' => 'firstname', 'placeholder' => 'Prénom'],
                'row_attr' => [
                    'class' => 'form-floating',
                ],])
            ->add('lastnameUser', TextType::class, ['label' => 'Nom',
                'attr' => ['autocomplete' => 'lastname', 'placeholder' => 'Nom'],
                'row_attr' => [
                    'class' => 'form-floating',
                ],])
            ->add('address', TextType::class, ['label' => 'Adresse',
                'attr' => ['autocomplete' => 'address', 'placeholder' => 'Adresse'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false])
            ->add('addressTwo', TextType::class, ['label' => 'Adresse Complémentaire',
                'attr' => ['autocomplete' => 'address2', 'placeholder' => 'Adresse Complémentaire'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false])
            ->add('addressThree', TextType::class, ['label' => 'Adresse Complémentaire Suite',
                'attr' => ['autocomplete' => 'address3', 'placeholder' => 'Adresse Complémentaire'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false])
            ->add('phone', TextType::class, ['label' => 'Téléphone fixe',
                'attr' => ['autocomplete' => 'phone', 'placeholder' => 'Téléphone fixe'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false])
            ->add('mobilePhone', TextType::class, ['label' => 'Mobile',
                'attr' => ['autocomplete' => 'phone', 'placeholder' => 'Mobile'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false])
            ->add('emailContact', TextType::class, ['label' => 'E-Mail de contact',
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'E-Mail de contact'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false])
            ->add('idCity', HiddenType::class, ["required" => false, "mapped" => false, "data" => ""])
            ->add('email', TextType::class, ['label' => 'E-Mail de connexion',
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'E-Mail de connexion'],
                'row_attr' => [
                    'class' => 'form-floating',
                ],]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true
        ]);
    }
}
