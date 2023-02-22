<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Sites;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, ['label' => 'Nom de la sortie : '])
            ->add('dateDebut', DateTimeType::class, [ 'label' => 'Date de début :', 'date_widget' => 'single_text', 'time_widget' => 'single_text'])
            ->add('duree', null, ['label' => 'Durée : '])
            ->add('dateFin', DateTimeType::class, [ 'label' => 'Date de fin :', 'date_widget' => 'single_text', 'time_widget' => 'single_text'])
            ->add('nbInscritMax', null, ['label' => 'Nombre d\'inscription max : '])
            ->add('description', null, ['label' => 'Description : '])
            ->add('photo', FileType::class, ['label' => 'Photo :','mapped' => false, 'required' => false, 'constraints' => [new Image(['maxSize' => '7024k', 'mimeTypesMessage' => "Format de l'image non supporter"])]])
            ->add('idSite',EntityType::class,['class' => Sites::class,'choice_label' => 'nom','label' => 'Campus : '])
            ->add('idLieu',EntityType::class,['class' => Lieux::class,'choice_label' => 'nom','label' => 'Lieu : '])

            ->add('publier', SubmitType::class, ['label'=> "Publier"])
            ->add('enregistrer', SubmitType::class, ['label'=> "Enregistrer"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
            'csrf_protection' => true
        ]);
    }
}
