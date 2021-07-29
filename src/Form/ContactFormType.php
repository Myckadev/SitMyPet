<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required'=>true,
                'attr'=>[
                    'placeholder'=>"Votre nom"
                ]
            ])
            ->add('prenom', TextType::class, [
                'required'=>true,
                'attr'=>[
                    'placeholder'=>"Votre prénom"
                ]
            ])
            ->add('mail', EmailType::class, [
                'required'=>true,
                'attr'=>[
                    'placeholder'=>"Votre mail"
                ]
            ])
            ->add('need', ChoiceType::class, [
                'required'=>true,
                'label'=>'Type de demande',
                'attr'=>[
                    'class'=> 'form-select'

                ],
                'choices'=>[
                    'Réclamation'=>"Reclamation",
                    'Informations'=>"Informations",
                    'Autre'=>"Autre"
                ]
            ])
            ->add('message', TextareaType::class,[
                'required'=> true,
                'label'=>false,
                'attr'=>[
                    'rows'=>8,
                    'cols'=>109,
                    'max_lenght'=>255,
                    'placeholder'=>"Votre demande (255 caractères max)"
                ],
                'constraints'=>[
                    new NotBlank([
                        'message'=>'Veuillez poser votre question.'
                    ]),
                    new Length([
                        'max'=>2000,
                        'maxMessage'=>"Message trop long."
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
