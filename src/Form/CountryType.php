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
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Entity\Country;
use App\Form\Input\ImageType;
use App\Service\Country\CountryFlagService;

final class CountryType extends AbstractType
{
    /**
     * Constructeur
     * @param TranslatorInterface $translator
     * @param CountryFlagService $countryFlagService
     */
    public function __construct(
        private TranslatorInterface $translator,
        private CountryFlagService $countryFlagService
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $country = $builder->getData();

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
            ->add('image', ImageType::class, [
                'mapped' => false,
                'required' => false,
                'image_url' => $this->countryFlagService->getImageUrl($country),
                'alt_label_image' => $this->translator->trans('admin.countries.edit.fields.image.alt_label', [
                    'name' => $country->getName()
                ], domain: 'form'),
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
