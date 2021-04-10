<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Contact;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'=>"Nom"
            ])
            ->add('firstName', TextType::class, [
                'label'=>"Prénom"
            ])
            ->add('phoneNbr', TextType::class, [
                'label'=>"Téléphone"
            ])
            ->add('email', TextType::class, [
                'label'=>"Email"
            ])
            ->add('category', EntityType::class, [
                'choice_label'=>"category",
                'class'=>Category::class,
                'label'=>"Catérogie"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
