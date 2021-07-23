<?php

/**
 * Menu du panneau d'administration
 */

namespace App\UI\Admin\Menus\Header;

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

        $messageItem = (new HeaderItemMenu())
            ->setLabel($translator->trans('header.admin.modules.messages.label', [], domain: 'menus'))
            ->setAltLabel($translator->trans('header.admin.modules.messages.alt_label', [], domain: 'menus'))
            ->setRouteName('app_admin_messages_index')
        ;

        $currentRequestRoute = $this->getCurrentRequest()?->attributes->get('_route');
        if($currentRequestRoute === 'app_admin_messages_index')
        {
            $messageItem->addClass('selected');
        }

        $this->addItems([ 
            $messageItem,
        ]);
    }

}