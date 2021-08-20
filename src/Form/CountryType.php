<?php

/**
 * Gestion du formulaire d'un pays
 */

namespace App\Form;

use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
/***/
use App\Entity\Country;

final class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', options: [
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('name', options: [
                'attr' => [
                    'autocomplete' => 'off',
                ],
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'mimeTypes' => [ 'image/jpeg', 'image/jpg', 'image/png' ],
                        'mimeTypesMessage' => 'countries.edit.image.mime',
                        'uploadIniSizeErrorMessage' => 'countries.edit.image.size',
                        'maxSizeMessage' => 'countries.edit.image.size',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
            'translation_domain' => 'form',
            'label_format' => 'admin.countries.edit.fields.%name%.label',
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }
}
