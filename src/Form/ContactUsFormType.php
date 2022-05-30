<?php

namespace App\Form;

use App\Entity\Civility;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactUsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Civility', EntityType::class, ["class" => Civility::class, "mapped" => false, "label" => false, "placeholder" => "Sélectionner une civilité"])
            ->add('FirstName', TextType::class, ["mapped" => false, "label" => "Prénom",
                'attr' => ['autocomplete' => 'firstname', 'placeholder' => 'Prénom'],
                'row_attr' => [
                    'class' => 'form-floating',
                ]])
            ->add('LastName', TextType::class, ["mapped" => false, "label" => "Nom",
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
            ->add('Email', EmailType::class, ["mapped" => false, "label" => "Adresse E-Mail",
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'E-Mail'],
                'row_attr' => [
                    'class' => 'form-floating',
                ]])
            ->add('PhoneNumber', TextType::class, ["mapped" => false, "label" => "Téléphone",
                'attr' => ['autocomplete' => 'phone', 'placeholder' => 'Téléphone'],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                "required" => false,
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => "Veuillez saisir votre N° de téléphone avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add("Message", TextareaType::class, ["mapped" => false,
                "attr" => [
                    "class" => "textareamce"
                ],
                "label" => "Votre message"
                , "required" => false,
                "constraints" => [
                    new NotBlank(["message" => "Vous devez saisir un message"])
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
