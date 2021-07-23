<?php

/**
 * Menu principal du site
 */

namespace App\UI\Site\Menus\Header;

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

final class MainMenu extends HeaderMenu {

    /**
	 * Le type de menu
	 * @var string $type
	 */
	protected string $type = self::TYPE_PRIMARY;

    /**
     * Constructeur
     * @param RequestStack $requestStack
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private RequestStack $requestStack,
        private TranslatorInterface $translator
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

        $contactItem = (new HeaderItemMenu())
            ->setLabel($translator->trans('header.site.main.contact.label', [], domain: 'menus'))
            ->setAltLabel($translator->trans('header.site.main.contact.alt_label', [], domain: 'menus'))
            ->setRouteName('app_site_contact_index')
        ;

        $currentRequestRoute = $this->getCurrentRequest()?->attributes->get('_route');
        if($currentRequestRoute === 'app_site_contact_index')
        {
            $contactItem->addClass('selected');
        }

        $this->addItems([ 
            $contactItem,
        ]);
    }

}