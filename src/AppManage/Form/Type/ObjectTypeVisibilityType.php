<?php

namespace App\AppManage\Form\Type;

use App\AppMain\Entity\Geospatial\ObjectTypeVisibility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectTypeVisibilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minZoom', NumberType::class, [
                'scale' => 2,
            ])
            ->add('maxZoom', NumberType::class, [
                'scale' => 2,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ObjectTypeVisibility::class,
        ]);
    }
}
