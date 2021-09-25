<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/***/
use App\Entity\{
    Pilot, 
    City
};
use App\Form\Input\AutocompleteType;

final class PilotType extends AbstractEntityType
{
    /**
     * Constructeur
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('public_id')
            ->add('first_name')
            ->add('last_name')
            ->add('birthdate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('birth_city', AutocompleteType::class, [
                'entity_class' => City::class,
                'route_name' => $this->urlGenerator->generate('app_admin_autocompletesearching_searching'),
                'attr' => [
                    'placeholder' => $this->translator->trans('admin.pilots.edit.fields.birth_city.placeholder', domain: 'form')
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Pilot::class,
            'label_format' => 'admin.pilots.edit.fields.%name%.label',
        ]);
    }
}
