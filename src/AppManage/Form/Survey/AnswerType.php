<?php

namespace App\AppManage\Form\Survey;


use App\AppMain\Entity\Survey\Question\Answer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();

                /** @var Answer $data */
                $data = $event->getData();

                $form->add('parent', EntityType::class, [
                    'class'         => Answer::class,
                    'choice_label'  => 'title',
                    'placeholder'   => '',
                    'query_builder' => function (EntityRepository $er) use ($data) {
                        return $er->createQueryBuilder('u')
                                  ->andWhere('u.question = :question')
                                  ->andWhere('u.id != :answer')
                                  ->setParameter('question', $data->getQuestion())
                                  ->setParameter('answer', $data)
                            ;
                    },
                ]);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}
