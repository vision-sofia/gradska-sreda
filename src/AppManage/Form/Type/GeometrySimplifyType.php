<?php

namespace App\AppManage\Form\Type;

use App\AppMain\Entity\Geospatial\Simplify;
use App\Doctrine\ValueObject\IntRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class GeometrySimplifyType extends AbstractType
{
    protected $translator;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minZoom', NumberType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('maxZoom', NumberType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('tolerance', NumberType::class, [
                'scale' => 6,
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, static function (FormEvent $event) {
                /** @var Simplify $data */
                $data = $event->getData();

                if ($data->getZoom()) {
                    $form = $event->getForm();
                    $form->get('minZoom')->setData($data->getZoom()->getEnd());
                    $form->get('maxZoom')->setData($data->getZoom()->getStart());
                }
            })
            ->addEventListener(FormEvents::SUBMIT, static function (FormEvent $event) {
                /** @var Simplify $data */
                $data = $event->getData();
                $form = $event->getForm();

                $max = $form->get('maxZoom')->getData();
                $min = $form->get('minZoom')->getData();

                if ($max && $min) {
                    $data->setZoom(new IntRange($max, $min));
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Simplify::class,
            'constraints' => [
                new Callback([
                    'callback' => [$this, 'checkZoomRange'],
                ]),
            ],
        ]);
    }

    public function checkZoomRange($data, ExecutionContextInterface $context): void
    {
        /** @var FormInterface $form */
        $form = $context->getRoot();

        $max = $form->get('maxZoom')->getData();
        $min = $form->get('minZoom')->getData();

        if ($max >= $min) {
            $context
                ->buildViolation('zoom.invalid_range')
                ->addViolation()
            ;
        }
    }
}
