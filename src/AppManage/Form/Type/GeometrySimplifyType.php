<?php

namespace App\AppManage\Form\Type;

use App\AppMain\Entity\Geospatial\Simplify;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeometrySimplifyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minZoom', NumberType::class, [
                'scale' => 1,
            ])
            ->add('maxZoom', NumberType::class, [
                'scale' => 1,
            ])
            ->add('tolerance', NumberType::class, [
                'scale' => 6,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Simplify::class,
        ]);
    }
}
