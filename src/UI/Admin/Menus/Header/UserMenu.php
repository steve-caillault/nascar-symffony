<?php

/**
 * Menu de l'utilisateur connecté au panneau d'administration
 */

namespace App\UI\Admin\Menus\Header;

use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\UI\Menus\Header\{
    HeaderMenu,
    HeaderItemMenu
};
use App\Entity\User;

final class UserMenu extends HeaderMenu {

    /**
	 * Le type de menu
	 * @var string $type
	 */
	protected string $type = self::TYPE_PRIMARY;

    /**
     * Utilisateur connecté
     * @var ?User
     */
    private ?User $user;

    /**
     * Constructeur
     * @param Security $security
     * @param TranslatorInterface $translator
     */
    public function __construct(Security $security, private TranslatorInterface $translator)
    {
        $user = $security->getUser();
        $this->user = $security->getUser();
    }

    /**
	 * Alimente le menu avec les éléments du menu
	 * @return void
	 */
	protected function fill() : void
    {
        if($this->user === null)
        {
            return;
        }

        $userNameItem = (new HeaderItemMenu())
            ->setLabel($this->user->getFullName())
        ;

        $logoutItem = (new HeaderItemMenu())
            ->setLabel($this->translator->trans('header.admin.user.logout.label', domain: 'menus'))
            ->setAltLabel($this->translator->trans('header.admin.user.logout.alt_label', domain: 'menus'))
            ->setRouteName('app_admin_security_auth_logout');

        $this->addItems([ 
            $userNameItem, 
            $logoutItem,
        ]);
    }

}