<?php

namespace App\Form;

use App\Entity\Food;
use App\Entity\Categoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class FoodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('imagen', FileType::class, [
            'label' => 'Nombre imagen (JPG o PNG)',
            'label_attr' => ['class' => 'etiqueta'],
            'data_class' => null,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Por favor, seleccione un archivo jpg o png',
                ])
            ],
        ])
        ->add('nombre', TextType::class, [
            'label' => 'Nombre:',
            'label_attr' => ['class' => 'etiqueta']
        ])
        ->add('descripcion', TextType::class, [
            'label' => 'Descripción:',
            'label_attr' => ['class' => 'etiqueta']
        ])
        /* ->add('categoria', NumberType::class, [
            'label' => 'Categoría:',
            'label_attr' => ['class' => 'etiqueta', 'value' => '1'],
            'data' => '1'
        ]) */
        ->add('categoria', EntityType::class, [
            'class' => Categoria::class
        ])
        ->add('precio', NumberType::class, [
            'label' => 'Precio:',
            'label_attr' => ['class' => 'etiqueta']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Food::class,
        ]);
    }
}
