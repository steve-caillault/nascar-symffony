<?php

/**
 * Gestion du formulaire d'un pays ou d'un état
 */

namespace App\Form\Country;

use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Form\AbstractEntityType;
use App\Form\Input\ImageType;
use App\Service\State\FlagServiceFactory;

abstract class AbstractStateType extends AbstractEntityType
{
    /**
     * Constructeur
     * @param TranslatorInterface $translator
     * @param FlagServiceFactory $flagServiceFactory
     */
    public function __construct(
        private TranslatorInterface $translator,
        private FlagServiceFactory $flagServiceFactory,
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $state = $builder->getData();
        
        $builder
            ->add('code')
            ->add('name')
            ->add('image', ImageType::class, [
                'mapped' => false,
                'required' => false,
                'image_url' => $this->flagServiceFactory->get($state)->getImageUrl($state),
                'alt_label_image' => $this->translator->trans('admin.states.edit.fields.image.alt_label', [
                    'name' => $state->getName(),
                    'state_type' => strtolower($state->getStateType()),
                ], domain: 'form'),
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'mimeTypes' => [ 'image/jpeg', 'image/jpg', 'image/png' ],
                        'mimeTypesMessage' => 'states.edit.image.mime',
                        'uploadIniSizeErrorMessage' => 'states.edit.image.size',
                        'maxSizeMessage' => 'states.edit.image.size',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label_format' => 'admin.states.edit.fields.%name%.label',
        ]);
    }
}
