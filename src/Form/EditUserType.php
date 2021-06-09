<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\IsTrue;

class EditUserType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    { 
        $user = $this->security->getUser();
        
        $builder
        ->add('Nom', TextType::class, [
            'data' => $user->getNom()
        ])
        ->add('Prenom', null, [
            'label' => 'Prénom',
            'data' => $user->getPrenom()
        ])
        ->add('email',EmailType::class, [
            'data' => $user->getEMail()
        ])
        ->add('tel', null, [
            'label' => 'Téléphone',
            'data' => $user->getTel()
        ])
        ->add('Adresse', null, [
            'data' => $user->getAdresse()
        ])
        ->add('cp', null, [
            'data' => $user->getCp()
        ])
        ->add('ville', null, [
            'data' => $user->getVille()
        ])
        ->add('num_licence', null ,[
            'label'  =>'Numéro de licence',
            'data' => $user->getNumLicence()])
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
