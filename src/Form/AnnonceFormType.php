<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Pet;
use App\Entity\User;
use App\Repository\PetRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>'Titre de l\'annonce',
            ])
            ->add('date_start', DateType::class, [
                'label'=>'Date de début',
                "format" => 'd-M-y',
                "data" => new \DateTime(),
                'html5' => false,
                'placeholder'=>[
                    'year'=>'Année', 'month' => 'Mois', 'day' => 'Jour'
                ]
            ])
            ->add('date_end', DateType::class, [
                'label'=>'Date de fin',
                "format" => 'd-M-y',
                "data" => new \DateTime(),
                'html5' => false,
                'placeholder'=>[
                    'year'=>'Année', 'month' => 'Mois', 'day' => 'Jour'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr'=>[
                    'placeholder'=>'Veuillez décrire votre besoin'
                ]
            ])
            ->add('pets', EntityType::class, [
                'label'=>false,
                'class'=>Pet::class,
                'choice_label'=>'nickname',
                'multiple'=>true,
                "attr"=>[
                    'class'=>"select2"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
