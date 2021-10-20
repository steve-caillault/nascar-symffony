<?php

/**
 * Menu du panneau d'administration
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\{
    Request,
    RequestStack
};
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\{
    HeaderMenu,
    HeaderItemMenu
};

final class AdminMenu extends HeaderMenu {

    /**
	 * Le type de menu
	 * @var string $type
	 */
	protected string $type = self::TYPE_PRIMARY;

    /**
     * Constructeur
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator
    )
    {
        
    }

    /**
     * Identifiant du menu
     * @return string
     */
    public function getId() : string
    {
        return 'admin-modules-menu';
    }

    /**
     * 
     */
    private function getCurrentRequest()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentRequestRoute = $currentRequest?->attributes->get('_route');
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        $this->addMessagesItem();
        $this->addCountriesItem();
        $this->addPilotsItem();
        $this->addCircuitsItem();
        $this->addSeasonsItem();
    }

    /**
     * Ajoute l'élément des messages
     * @return self
     */
    private function addMessagesItem() : self
    {
        $translator = $this->translator;
        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentRequestRoute = $currentRequest?->attributes->get('_route');

        $messageItem = (new HeaderItemMenu())
            ->setLabel($translator->trans('header.admin.modules.messages.label', [], domain: 'menus'))
            ->setAltLabel($translator->trans('header.admin.modules.messages.alt_label', [], domain: 'menus'))
            ->setRouteName('app_admin_messages_index')
        ;
        if($currentRequestRoute === 'app_admin_messages_index')
        {
            $messageItem->addClass('selected');
        }

        return $this->addItem($messageItem);
    }

    /**
     * Ajoute l'élément des pays
     * @return self
     */
    private function addCountriesItem() : self
    {
        $translator = $this->translator;

        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentUri = $currentRequest?->getRequestUri();
        $countriesUri = $this->urlGenerator->generate('app_admin_countries_list_index');

        $messageItem = (new HeaderItemMenu())
            ->setLabel($translator->trans('header.admin.modules.countries.label', [], domain: 'menus'))
            ->setAltLabel($translator->trans('header.admin.modules.countries.alt_label', [], domain: 'menus'))
            ->setRouteName('app_admin_countries_list_index')
        ;
        if(str_contains($currentUri, $countriesUri))
        {
            $messageItem->addClass('selected');
        }

        return $this->addItem($messageItem);
    }

    /**
     * Ajoute l'élément des pilotes 
     * @return self
     */
    private function addPilotsItem() : self
    {
        $translator = $this->translator;

        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentUri = $currentRequest?->getRequestUri();
        $pilotsUri = $this->urlGenerator->generate('app_admin_pilots_list_index');

        $messageItem = (new HeaderItemMenu())
            ->setLabel($translator->trans('header.admin.modules.pilots.label', [], domain: 'menus'))
            ->setAltLabel($translator->trans('header.admin.modules.pilots.alt_label', [], domain: 'menus'))
            ->setRouteName('app_admin_pilots_list_index')
        ;
        if(str_contains($currentUri, $pilotsUri))
        {
            $messageItem->addClass('selected');
        }

        return $this->addItem($messageItem);
    }

    /**
     * Ajout l'élément des circuits
     * @return self
     */
    private function addCircuitsItem() : self
    {
        $translator = $this->translator;

        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentUri = $currentRequest?->getRequestUri();
        $circuitsUri = $this->urlGenerator->generate('app_admin_circuits_list_index');

        $messageItem = (new HeaderItemMenu())
            ->setLabel($translator->trans('header.admin.modules.circuits.label', [], domain: 'menus'))
            ->setAltLabel($translator->trans('header.admin.modules.circuits.alt_label', [], domain: 'menus'))
            ->setRouteName('app_admin_circuits_list_index')
        ;
        if(str_contains($currentUri, $circuitsUri))
        {
            $messageItem->addClass('selected');
        }

        return $this->addItem($messageItem);
    }

    /**
     * Ajoute l'élément des saisons
     * @return self
     */
    private function addSeasonsItem() : self
    {
        $translator = $this->translator;
        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentUri = $currentRequest?->getRequestUri();

        // Ancre des saisons
        $seasonsItem = (new HeaderItemMenu())
            ->setLabel($translator->trans('header.admin.modules.seasons.label', [], domain: 'menus'))
            ->setAltLabel($translator->trans('header.admin.modules.seasons.alt_label', [], domain: 'menus'))
            ->setRouteName('app_admin_seasons_index')
        ;
        $seasonsUri = $this->urlGenerator->generate('app_admin_seasons_index');
        if(str_contains($currentUri, $seasonsUri))
        {
            $seasonsItem->addClass('selected');
        }

        return $this->addItem($seasonsItem);
    }

}