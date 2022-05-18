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

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contactFirstname', TextType::class, [
                'label' => 'Prénom',
                "required" => true,
                'attr' => ['autocomplete' => 'firstname', 'placeholder' => 'Prénom'],
                'row_attr' => [
                    'class' => 'form-floating']])
            ->add('contactLastname', TextType::class, [
                'label' => 'Nom',
                "required" => true,
                'attr' => ['autocomplete' => 'lastname', 'placeholder' => 'Nom'],
                'row_attr' => [
                    'class' => 'form-floating']])
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
                    'class' => 'form-floating']])
            ->add('contactMobilePhone', TextType::class, [
                'label' => 'Téléphone Mobile',
                "required" => false,
                'attr' => ['autocomplete' => 'mobilephone', 'placeholder' => 'Téléphone Mobile'],
                'row_attr' => [
                    'class' => 'form-floating']])
            ->add('contactFunction', TextType::class, [
                'label' => 'Fonction',
                "required" => false,
                'attr' => ['autocomplete' => 'function', 'placeholder' => 'Fonction'],
                'row_attr' => [
                    'class' => 'form-floating']])
            ->add('Civility', EntityType::class, [
                'class' => Civility::class,
                'label' => false,
                "required" => true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
