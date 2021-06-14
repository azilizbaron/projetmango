<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
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
            ->add('Nom')
            ->add('Prenom', null, [
                'label' => 'Prénom'
            ])
            ->add('email',EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
            ])
            ->add('tel', null, [
                'label' => 'Téléphone'
            ])
            ->add('date_naissance', DateType::class, ['widget' => 'single_text','years' => range(1940, date('Y')),
            'label' => 'Date de naissance'])
            ->add('adresse')
            ->add('cp', null, [
                'label' => 'Code postal'
            ])
            ->add('ville')
            ->add('num_licence', null ,[
                'label'  =>'Numéro de licence'])
            ->add('membre', ChoiceType::class, [
                'choices'  => [
                    'Non' => 0,
                    'Oui' => 1,
                ],
                'label' => 'Déjà adhérent ?'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
