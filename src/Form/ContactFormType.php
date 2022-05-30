<?php

namespace App\Form;

use App\Entity\Civility;
use App\Entity\Contact;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contactFirstname', TextType::class, [
                'label' => 'Prénom',
                "required" => true,
                'attr' => ['autocomplete' => 'firstname', 'placeholder' => 'Prénom'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'minMessage' => "Veuillez saisir le prénom du contact avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('contactLastname', TextType::class, [
                'label' => 'Nom',
                "required" => true,
                'attr' => ['autocomplete' => 'lastname', 'placeholder' => 'Nom'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => "Veuillez saisir le nom du contact avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Adresse E-Mail',
                "required" => false,
                'attr' => ['autocomplete' => 'email', 'placeholder' => 'Adresse E-Mail'],
                'row_attr' => [
                    'class' => 'form-floating']])
            ->add('contactPhone', TextType::class, [
                'label' => 'Téléphone',
                "required" => false,
                'attr' => ['autocomplete' => 'phone', 'placeholder' => 'Téléphone'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => "Veuillez saisir un N° de téléphone avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('contactMobilePhone', TextType::class, [
                'label' => 'Téléphone Mobile',
                "required" => false,
                'attr' => ['autocomplete' => 'mobilephone', 'placeholder' => 'Téléphone Mobile'],
                'row_attr' => [
                    'class' => 'form-floating'],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => "Veuillez saisir un N° de téléphone avec au minimum {{ limit }} caractères"
                    ])
                ]])
            ->add('contactFunction', TextType::class, [
                'label' => 'Fonction',
                "required" => false,
                'attr' => ['autocomplete' => 'function', 'placeholder' => 'Fonction'],
                'row_attr' => [
                    'class' => 'form-floating']])
            ->add('Civility', EntityType::class, [
                'class' => Civility::class,
                'label' => false,
                "required" => true,
                "placeholder" => "Sélectionner une civilité"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
