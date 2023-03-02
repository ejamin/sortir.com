<?php

namespace App\Form;

use App\Entity\Participants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Sites;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo',TextType::class,["label" => "Pseudo"])
            ->add('prenom',TextType::class,["label" => "Prenom"])
            ->add('nom',TextType::class,["label" => "Nom"])
            ->add('telephone',TextType::class,["label" => "Telephone"])
            ->add('email',TextType::class,["label" => "Email"])        
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne sont pas identiques',
                'options' => ['attr' => ['class' => 'password-field d-fill d-flex d-row']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe', 'attr' => ['class'=> 'flex-fill align-self-center p-2']],
                'second_options' => ['label' => 'Confirmer', 'attr' => ['class'=> 'flex-fill align-self-center p-2']]
            ])
            ->add('idSites', EntityType::class, [
                'class' => Sites::class,
                'choice_label' => function ($site) {
                    return $site->getNom();
                }])
            ->add('image', FileType::class, ['mapped' => false, 'required' => false, 'constraints' => [new Image(['maxSize' => '7024k', 'mimeTypesMessage' => "Format de l'image non supporter"])]])
            ->add('save', SubmitType::class,["label" => "Enregistrer"]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participants::class,
            'csrf_protection' => true
        ]);
    }
}
