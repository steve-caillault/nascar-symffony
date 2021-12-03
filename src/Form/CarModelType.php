<?php

/**
 * Edition d'un modèle de voiture
 */

namespace App\Form;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/***/
use App\Entity\{
    CarModel,
    Motor
};
use App\Form\Input\AutocompleteType;

final class CarModelType extends AbstractEntityType
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
            ->add('motor', AutocompleteType::class, [
                'entity_class' => Motor::class,
                'route_name' => $this->urlGenerator->generate('app_admin_autocompletesearching_searching'),
                'attr' => [
                    'placeholder' => 'admin.cars.edit.fields.motor.placeholder',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => CarModel::class,
            'label_format' => 'admin.cars.edit.fields.%name%.label',
        ]);
    }

}
