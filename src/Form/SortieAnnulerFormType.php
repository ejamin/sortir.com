<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Sites;
use App\Entity\Sorties;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieAnnulerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, ['label' => 'Nom de la sortie : ','disabled' => true])
            ->add('dateDebut', DateTimeType::class, [ 'label' => 'Date de début', 'date_widget' => 'single_text', 'time_widget' => 'single_text','disabled' => true])
            ->add('duree', null, ['label' => 'Durée : ','disabled' => true])
            ->add('dateFin', DateTimeType::class, [ 'label' => 'Date de fin', 'date_widget' => 'single_text', 'time_widget' => 'single_text','disabled' => true])
            ->add('nbInscritMax', null, ['label' => 'Nombre d\'inscription max : ','disabled' => true])
            ->add('description', null, ['label' => 'Description : ','disabled' => true])
            #->add('photo')
            ->add('idSite',EntityType::class,['class' => Sites::class,'choice_label' => 'nom','disabled' => true])
            ->add('idLieu',EntityType::class,['class' => Lieux::class,'choice_label' => 'nom','disabled' => true])

            ->add('motif', null, ['label' => 'Motif d\'annulation : '])

            ->add('valider', SubmitType::class, ['label'=> "Valider", 'required' => true])
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
