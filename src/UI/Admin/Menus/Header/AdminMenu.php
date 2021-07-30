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
     * Retourne la requête courante
     * @return ?Request
     */
    private function getCurrentRequest() : ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        $translator = $this->translator;

        $currentRequest = $this->getCurrentRequest();
        $currentRequestRoute = $currentRequest?->attributes->get('_route');
        $currentUri = $currentRequest?->getRequestUri();

        // Ancre des messages
        $messageItem = (new HeaderItemMenu())
            ->setLabel($translator->trans('header.admin.modules.messages.label', [], domain: 'menus'))
            ->setAltLabel($translator->trans('header.admin.modules.messages.alt_label', [], domain: 'menus'))
            ->setRouteName('app_admin_messages_index')
        ;
        if($currentRequestRoute === 'app_admin_messages_index')
        {
            $messageItem->addClass('selected');
        }

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

        $this->addItems([ 
            $messageItem,
            $seasonsItem,
        ]);
    }

}