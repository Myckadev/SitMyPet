<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'required' => true,
                'label'=>"Nom d'utilisateur",
                'attr'=>[
                    'placeholder' => "Nom d'utilisateur"
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label'=>'Email',
                'attr'=>[
                    'placeholder' => "Adresse email"
                ]
            ])
            ->add('nom', TextType::class, [
                'required' => true,
                'label'=>'Nom',
                'attr'=>[
                    'placeholder' => "Nom"
                ]
            ])
            ->add('prenom', TextType::class, [
                'required' => true,
                'label'=>'Prénom',
                'attr'=>[
                    'placeholder' => "Prénom"
                ]
            ])
            ->add('age', IntegerType::class, [
                'required' => true,
                'label'=>'Âge',
                'attr'=>[
                    'placeholder' => "Âge"
                ]
            ])
            ->add('pays', TextType::class,[
                'required' => true,
                'label'=>'Pays',
                'attr'=>[
                    'placeholder' => "Pays"
                ]
            ])
            ->add('adresse', TextType::class, [
                'required' => true,
                'label'=>'Adresse',
                'attr'=>[
                    'placeholder' => "Adresse"
                ]
            ])
            ->add('ville', TextType::class, [
                'required' => true,
                'label'=>'Ville',
                'attr'=>[
                    'placeholder' => "Ville"
                ]
            ])
            ->add('city', NumberType::class, [
                'required' => true,
                'label'=>'CodePostal',
                'attr'=>[
                    'placeholder' => "Code postal"
                ]
            ])
            ->add('roles', ChoiceType::class ,[
                'required' => true,
                'choices' => [
                    'Sitter' => "ROLE_SITTER",
                    'Master' => "ROLE_MASTER"
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label'=>' ',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez acceptez nos conditions d\'utilisation',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => [
                        'placeholder'=>"Mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => [
                        'placeholder'=> "Mot de passe"
                    ]
                ],
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
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
