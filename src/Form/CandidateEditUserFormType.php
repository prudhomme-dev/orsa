<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;


class CandidateEditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', HiddenType::class, [
                'label' => 'Votre Adresse E-Mail',
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'Votre Adresse E-Mail'],
                'row_attr' => [
                    'class' => 'form-floating'],
            ])
            ->add('civility', EntityType::class, ["class" => Civility::class,
                "label" => false, "placeholder" => "Sélectionner une civilité"
            ])
            ->add('firstnameUser', TextType::class, ['label' => 'Prénom',
                'attr' => ['autocomplete' => 'firstname', 'placeholder' => 'Prénom'],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
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
                ]])
            ->add('address', TextType::class, ['label' => 'Adresse',
                'attr' => ['autocomplete' => 'address', 'placeholder' => 'Adresse'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir votre adresse minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('addressTwo', TextType::class, ['label' => 'Adresse Complémentaire',
                'attr' => ['autocomplete' => 'address2', 'placeholder' => 'Adresse Complémentaire'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir votre adresse minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('addressThree', TextType::class, ['label' => 'Adresse Complémentaire Suite',
                'attr' => ['autocomplete' => 'address3', 'placeholder' => 'Adresse Complémentaire'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false])
            ->add('phone', TextType::class, ['label' => 'Téléphone fixe',
                'attr' => ['autocomplete' => 'phone', 'placeholder' => 'Téléphone fixe'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false,
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir votre adresse minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('mobilePhone', TextType::class, ['label' => 'Mobile',
                'attr' => ['autocomplete' => 'phone', 'placeholder' => 'Mobile'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => "Veuillez saisir votre N° de téléphone avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('emailContact', TextType::class, ['label' => 'E-Mail de contact',
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'E-Mail de contact'],
                'row_attr' => [
                    'class' => 'form-floating',
                ], "required" => false,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => "Veuillez saisir votre N° de téléphone mobile avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('idCity', HiddenType::class, ["required" => false, "mapped" => false, "data" => ""])
            ->add('emailContact', EmailType::class, ['label' => 'E-Mail de contact',
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'E-Mail de contact'],
                'row_attr' => [
                    'class' => 'form-floating',
                ]])
            ->add('uploadedCv', FileType::class, ['label' => false, 'required' => false, "mapped" => false, "row_attr" => ["style" => "display:none;"], "attr" => ["accept" => "application/pdf", "onchange" => "alert('CV sélectionné, validez les modifications de profils');"],
                'constraints' => [new File([
                    'maxSize' => '10M',
                    'maxSizeMessage' => 'Taille du fichier trop importante',
                    'mimeTypes' => ['application/pdf'], 'mimeTypesMessage' => 'Merci de téléverser un fichier PDF'
                ])]])
            ->add("coverletterContent", TextareaType::class, ['label' => false, "required" => false, "attr" => ["class" => "textareamce"]]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true
        ]);
    }
}
