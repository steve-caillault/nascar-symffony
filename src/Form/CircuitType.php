<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/***/
use App\Form\Input\AutocompleteType;
use App\Entity\{
    City, 
    Circuit
};

final class CircuitType extends AbstractEntityType
{
    /**
     * Constructeur
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('name')
            ->add('city', AutocompleteType::class, [
                'entity_class' => City::class,
                'route_name' => $this->urlGenerator->generate('app_admin_autocompletesearching_searching'),
                'attr' => [
                    'placeholder' => 'admin.circuits.edit.fields.city.placeholder',
                ],
            ])
            ->add('distance', options: [
                'invalid_message' => 'circuits.edit.distance.integer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Circuit::class,
            'label_format' => 'admin.circuits.edit.fields.%name%.label',
        ]);
    }
}
