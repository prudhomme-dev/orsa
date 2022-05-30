<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CompanyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Raison Sociale',
                'attr' => ['autocomplete' => 'companyName', 'placeholder' => 'Raison Sociale'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir la raison sociale avec minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => ['autocomplete' => 'addressCompany', 'placeholder' => 'Adresse'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir l'adresse minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('addressTwo', TextType::class, [
                'label' => 'Adresse Complémentaire',
                "required" => false,
                'attr' => ['autocomplete' => 'address2Company', 'placeholder' => 'Adresse Complémentaire'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir l'adresse minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('addressThree', TextType::class, [
                'label' => 'Adresse Complémentaire (Suite)',
                "required" => false,
                'attr' => ['autocomplete' => 'address3Company', 'placeholder' => 'Adresse Complémentaire (Suite)'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir l'adresse minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('phoneCompany', TextType::class, [
                'label' => 'N° de téléphone',
                "required" => false,
                'attr' => ['autocomplete' => 'phoneCompany', 'placeholder' => 'N° de téléphone'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => "Veuillez saisir un N° de téléphone avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('emailCompany', EmailType::class, [
                'label' => 'Adresse E-Mail',
                "required" => false,
                'attr' => ['autocomplete' => 'emailCompany', 'placeholder' => 'Adresse E-Mail'],
                'row_attr' => [
                    'class' => 'form-floating']])
            ->add('idCity', HiddenType::class, ["required" => false, "mapped" => false, "data" => ""]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
            'allow_extra_fields' => true
        ]);
    }
}
