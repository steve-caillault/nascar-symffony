<?php

/**
 * Formulaire de contact
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType, TextareaType
};
use Symfony\Component\OptionsResolver\OptionsResolver;
/***/
use App\Entity\ContactMessage;

final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', options: [
                'attr' => [
                    'placeholder' => 'site.contact.fields.from.placeholder',
                ],
            ])
            ->add('email')
            ->add('subject', options: [
                'attr' => [
                    'placeholder' => 'site.contact.fields.subject.placeholder',
                ],
            ])
            ->add('message', TextareaType::class, options: [
                'attr' => [
                    'placeholder' => 'site.contact.fields.message.placeholder',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
            'translation_domain' => 'form',
            'label_format' => 'site.contact.fields.%name%.label',
            'attr' => [
                'novalidate' => 'novalidate',
            ],
        ]);
    }

}
