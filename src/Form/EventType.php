<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'événement',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
            ])
            ->add('place', TextType::class, [
                'label' => 'Lieu',
            ])
            ->add('type', TextType::class, [
                'label' => 'Type d\'événement',
            ])
            ->add('limitedSpace', IntegerType::class, [
                'label' => 'Nombre de places disponibles',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
