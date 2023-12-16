<?php

namespace App\Form;

use App\Entity\DTO\InscriptionFormDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user_name', TextType::class, ['label' => 'Nom d\'utilisateur'])
            ->add('user_mail', EmailType::class, ['label' => 'Adresse mail'])
            ->add('user_passwd', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('user_passwd_confirm', PasswordType::class, ['label' => 'Confirmation du mot de passe'])
            ->add('sign_in', SubmitType::class, ['label' => 'S\'inscrire'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InscriptionFormDTO::class
        ]);
    }
}
