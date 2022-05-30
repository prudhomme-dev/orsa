<?php

namespace App\Form;

use App\Entity\ApplicationNote;
use App\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationNoteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Status', EntityType::class, ["class" => Status::class, "label" => false, "placeholder" => "Sélectionner un statut"])
            ->add('messageNote', TextType::class, ['label' => 'Description de l\'action',
                'attr' => ['placeholder' => 'Description de l\'action'],
                'row_attr' => [
                    'class' => 'form-floating'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ApplicationNote::class,
        ]);
    }
}
