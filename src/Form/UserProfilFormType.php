<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required'=>false,
                'label'=> "Nom D'utilisateur",
                'attr'=>[
                    'placeholder'=>"Changez votre nom d'utilisateur"
                ]
            ] )
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'Les mails doivent concorder.',
                'options' => ['attr' => ['class' => 'email-field']],
                'required' => false,
                'first_options'  => [
                    'label' => 'Nouveau Email',
                    'attr' => [
                        'placeholder'=>"Votre email"
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer nouveau Email',
                    'attr' => [
                        'placeholder'=> "Confirmation d'email"
                    ]
                ]
            ])
            ->add('nom')
            ->add('prenom')
            ->add('age')
            ->add('pays')
            ->add('adresse')
            ->add('ville')
            ->add('city')
            ->add('roles', ChoiceType::class ,[
                'required' => true,
                'choices' => [
                    'Sitter' => "ROLE_SITTER",
                    'Master' => "ROLE_MASTER"
                ]
            ])
            ->add('editPicture', FileType::class)
            ->add('description')
            ->add('rayon_action')
            ->add('tarif')
            ->add('animaux')
        ;
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($roleArray){
                    return count($roleArray)? $roleArray[0]: null;
                },
                function ($roleString){
                    return [$roleString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
