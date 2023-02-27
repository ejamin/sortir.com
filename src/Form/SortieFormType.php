<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Sites;
use App\Entity\Sorties;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Image;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, ['label' => 'Nom de la sortie : ', 'required' => true])
            ->add('dateDebut', DateTimeType::class, [ 'label' => 'Date de début :', 'date_widget' => 'single_text', 'time_widget' => 'single_text', 'required' => true,
                'constraints' => [new GreaterThan(['value' => (new DateTime('now')),'message' => 'La date est dépassée'])],'attr' => ['min' => (new DateTime('now'))->format('dd-MM-yyyyTHH:mm')]])
            ->add('duree', null, ['label' => 'Durée : ', 'required' => true,'constraints' => new GreaterThan(['value' => 0, 'message' => 'La durée doit être positif'])])
            ->add('dateFin', DateTimeType::class, [ 'label' => 'Date de fin :', 'required' => true, 'date_widget' => 'single_text', 'time_widget' => 'single_text','attr' => [ 'class' => 'form-control-sm','min' => 'dateDebut','message' => 'La date de cloture doit être avant la date de début']])
            ->add('nbInscritMax', null, ['label' => 'Nombre d\'inscription max : ', 'required' => true,'constraints' => new GreaterThan(['value' => 0, 'message' => 'Le nombre de place doit être positif'])])
            ->add('description', null, ['label' => 'Description : ', 'required' => true])
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