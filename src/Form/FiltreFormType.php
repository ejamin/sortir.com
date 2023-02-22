<?php

namespace App\Form;

use App\Entity\Lieux;
use App\Entity\Villes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Sites;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class FiltreFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('sites', EntityType::class, [
            'class' => Sites::class,
            'choice_label' => function ($site) {
                return $site->getNom();
            }])
        ->add('searchText', TextType::class,['label' => 'Le nom de la sortie contient : ','required'   => false])
        ->add('dateMin',DateTimeType::class, array('widget' => 'single_text','label' => 'Entre ','required'   => false))
        ->add('dateMax',DateTimeType::class, array('widget' => 'single_text','label' => 'et ','required'   => false))
        ->add('organisateur', CheckboxType::class, ['label'    => 'Sorties dont je suis l\'organisateur/trice','required' => false])
        ->add('inscrit', CheckboxType::class, ['label'    => 'Sorties auxquelles je suis inscrit/e','required' => false])
        ->add('pasInscrit', CheckboxType::class, ['label'    => 'Sorties auxquelles je ne suis pas inscrit/e','required' => false])
        ->add('passees', CheckboxType::class, ['label'    => 'Sorties passées','required' => false])            
        ->add('search', SubmitType::class)
        ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true
        ]);
    }
}
