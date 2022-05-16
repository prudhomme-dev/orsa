<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeMailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newEMail', RepeatedType::class, [
                'mapped' => false,
                'type' => EmailType::class,
                'invalid_message' => 'L\'adresse de confirmation n\'est pas identique',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Votre nouvelle adresse E-Mail',
                    'attr' => ['autocomplete' => 'new-email', 'placeholder' => 'Votre nouvelle adresse E-Mail'],
                    'row_attr' => [
                        'class' => 'form-floating col-10'],
                ],
                'second_options' => ['label' => 'Confirmez votre nouvelle adresse E-Mail',
                    'attr' => ['autocomplete' => 'new-email', 'placeholder' => 'Confirmez votre nouvelle adresse E-Mail'],
                    'row_attr' => [
                        'class' => 'form-floating col-10']],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
