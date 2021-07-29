<?php

namespace App\Form;

use App\Entity\Pet;
use App\Entity\PetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        if($options['ajout']==true){

            $builder
                ->add('nickname', TextType::class, [
                    'label'=>'Nom'
                ])
                ->add('picture', FileType::class, [
                    'label'=>'Photo'
                ])
                ->add('description', TextareaType::class)
                ->add('age', NumberType::class)
                ->add('type', EntityType::class,[
                    "class"=>PetType::class,
                    "choice_label"=>'name'
                ])
            ;
        }
        else{
            $builder
                ->add('nickname', TextType::class, [
                    'label'=>'Nom'
                ])
                ->add('editPicture', FileType::class, [
                    'label'=>'Photo'
                ])
                ->add('description', TextareaType::class)
                ->add('age', NumberType::class)
                ->add('type', EntityType::class,[
                    "class"=>PetType::class,
                    "choice_label"=>'name'
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver){

        $resolver->setDefaults([
            'data_class' => Pet::class,
            'ajout'=>false
        ]);
    }
}
