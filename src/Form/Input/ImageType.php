<?php

/**
 * Gestion d'un champs image avec prévisualisation
 */

namespace App\Form\Input;

use Symfony\Component\Form\{
    AbstractType,
    FormView,
    FormInterface
};
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ImageType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function getParent() : string
    {
        return FileType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'image_url' => null,        // URL de l'image de prévisualisation
            'alt_label_image' => null,  // Propriété alt de l'image de prévisualisation
        ]);

        $resolver->setAllowedTypes('image_url', [ 'null', 'string', ]);
        $resolver->setAllowedTypes('alt_label_image', [ 'null', 'string', ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['alt_label_image'] = $options['alt_label_image'];
        $view->vars['image_url'] = $options['image_url'];
    }
}