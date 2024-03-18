<?php

namespace App\Form;

use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Vich\UploaderBundle\Form\Type\VichImageType; // Ajoutez cette ligne

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomEquipe')
            ->add('logoFile', VichImageType::class, [ 
                'label' => 'Logo de l\'équipe',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('created_at', BirthdayType::class, [
                'widget' => 'choice',
                'format' => 'dd-MM-yyyy',
            ])
            ->add('pays')
            ->add('valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
