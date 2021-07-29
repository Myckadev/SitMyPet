<?php

namespace App\Form;

use App\Entity\PetType;
use App\Entity\Planning;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PlanningFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [

            ])
            ->add('date_start', DateTimeType::class, [
                "html5"=>false,
                "format" => 'd-M-y',
                "data" => new \DateTime(),
                "widget"=>"choice",
                "minutes"=>[0, 15, 30, 45]
            ])
            ->add('date_end', DateTimeType::class, [
                "html5"=>false,
                "format" => 'd-M-y',
                "data" => new \DateTime(),
                "widget"=>"choice",
                "minutes"=>[0, 15, 30, 45]
            ])
            ->add('pet_type', EntityType::class, [
                'class'=>PetType::class,
                'choice_label'=>'name',
                'multiple'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}
