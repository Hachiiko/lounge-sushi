<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Imię i nazwisko',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Numer telefonu',
            ])
            ->add('numberOfPeople', IntegerType::class, [
                'label' => 'Ilość osób',
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                ]
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Data',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => [
                    'autocomplete' => false,
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Złóż rezerwację',
            ])
        ;
    }
}