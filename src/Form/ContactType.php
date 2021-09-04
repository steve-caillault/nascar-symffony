<?php

/**
 * Formulaire de contact
 */

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
/***/
use App\Entity\ContactMessage;

final class ContactType extends AbstractFormType
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
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
            'label_format' => 'site.contact.fields.%name%.label',
        ]);
    }

}
