<?php

namespace App\Form;

use App\Entity\Groupe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class GroupeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, ['label' => 'Nom du groupe : ', 'required' => true])
            ->add('photo', FileType::class, ['label' => 'Photo :','mapped' => false, 'required' => false, 'constraints' => [new Image(['maxSize' => '7024k', 'mimeTypesMessage' => "Format de l'image non supporter"])]])

            ->add('valider', SubmitType::class, ['label'=> "Valider"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Groupe::class,
        ]);
    }
}
