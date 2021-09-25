<?php

/**
 * Champs d'autocomplètion faisant la recherche d'une entité
 */

namespace App\Form\Input;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface,
    FormView,
    FormInterface,
    DataMapperInterface,
    FormError
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    TextType, HiddenType
};
/***/
use App\Form\AbstractFormType;
use App\Entity\AutocompleteEntityInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

final class AutocompleteType extends AbstractFormType implements DataMapperInterface
{
    /**
     * Constructeur
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private EntityManagerInterface $entityManager)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $placeholder = $options['attr']['placeholder'] ?? null;

        $builder
            ->add('searching', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'autocomplete-text',
                    'placeholder' => $placeholder,
                    'data-model' => $options['entity_class'],
                ],
                
            ])
            ->add('id', HiddenType::class, [
                'attr' => [
                    'class' => 'autocomplete-value',
                ]
            ])
            ->setDataMapper($this)
        ;
    }

    /**
     * Convertie les données aux champs
     * @param mixed $data
     * @param \Traversable $forms
     * @return void
     */
    public function mapDataToForms($data, \Traversable $forms) : void
	{
        // Si l'identifiant est vide
		$entityId = $data;
		if($entityId === null)
		{
			return;
		}

        $formsToArray = iterator_to_array($forms);
		$options = current($formsToArray)->getParent()->getConfig()->getOptions();

        // La classe de l'entité doit être connu
		$entityClass = $options['entity_class'] ?: null;
        if($entityClass === null)
        {
            throw new \Exception('Entity class can not be null.');
        }

        // Recherche l'entité en base de données
		$entity = $this->entityManager->getRepository($entityClass)->find($entityId);
		if($entity === null)
		{
			return;
		}

        // Entité incorrecte
        if(! $entity instanceof AutocompleteEntityInterface)
        {
            throw new \Exception('Entity must implements AutocompleteEntityInterface.');
        }
		
		// Affecte les valeurs aux champs
        $formsToArray['searching']->setData($entity->getAutocompleteDisplayValue());
        $formsToArray['id']->setData($entity->getAutocompleteId());
	}
	
    /**
     * Convertie les fomulaires en données
     * @param \Traversable $forms
     * @param mixed $viewData
     * @return void
     */
	public function mapFormsToData(\Traversable $forms, &$viewData) : void
	{
        $formsToArray = iterator_to_array($forms);
        $parent = current($formsToArray)->getParent();
		$options = $parent->getConfig()->getOptions();

		$entityClass = $options['entity_class'] ?: null;
        if($entityClass === null)
        {
            throw new \Exception('Entity class can not be null.');
        }
		
        // Identifiant vide
		$entityId = $formsToArray['id']->getData();
		if($entityId === null)
		{
			$viewData = null;
			return;
		}
		
        // Entité vide
		$entity = $this->entityManager->getRepository($entityClass)->find($entityId);
		if($entity === null)
		{
			$viewData = null;
			return;
		}

        // Entité
        if(! $entity instanceof AutocompleteEntityInterface)
        {
            throw new \Exception('Entity must implements AutocompleteEntityInterface.');
        }
		
		$viewData = $entity;
	}

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'entity_class' => '',       // Classe de entité
            'route_name' => '',         // Route à appeler en Ajax
            'error_bubbling' => false,
        ]);

        $resolver->setAllowedTypes('entity_class', 'string');
        $resolver->setAllowedTypes('route_name', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['autocomplete_type'] = 'ajax';
        $view->vars['autocomplete_route_name'] = $options['route_name'];
    }
}
