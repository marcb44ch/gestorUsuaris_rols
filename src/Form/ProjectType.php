<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('fecha_inicio')
            ->add('fecha_fin')
            ->add('estado', ChoiceType::class, [
                'label' => 'Selecciona una opció...',
                'choices' => [
                    'Pendiente' => 'pendiente',
                    'En proceso' => 'en_proceso',
                    'Finalizado' => 'finalizado',
                ],
                'mapped' => true,
                'expanded' => false,
                'multiple' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
