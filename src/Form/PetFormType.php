<?php

namespace App\Form;

use App\Entity\Pet;
use App\Entity\PetType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('nickname', TextType::class)
            ->add('picture', FileType::class)
            ->add('description')
            ->add('age', NumberType::class)
            ->add('type', EntityType::class,[
                "class"=>PetType::class,
                "choice_label"=>'name',
                "multiple"=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver){

        $resolver->setDefaults([
            'data_class' => Pet::class,
        ]);
    }
}
