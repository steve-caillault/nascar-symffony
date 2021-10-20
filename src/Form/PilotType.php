<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    )
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('public_id')
            ->add('first_name')
            ->add('last_name')
            ->add('birthdate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'invalid_message' => 'pilots.edit.birthdate.date',
            ])
            ->add('birth_city', AutocompleteType::class, [
                'entity_class' => City::class,
                'route_name' => $this->urlGenerator->generate('app_admin_autocompletesearching_searching'),
                'attr' => [
                    'placeholder' => 'admin.pilots.edit.fields.birth_city.placeholder'
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
