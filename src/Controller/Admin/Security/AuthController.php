<?php

/**
 * Contrôleur d'authentification du panneau d'administration
 */

namespace App\Controller\Admin\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Controller\Admin\AdminAbstractController;
use App\UI\Menus\Breadcrumb\BreadcrumbItem;

final class AuthController extends AdminAbstractController {

    /**
     * Connexion
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/auth/login',
            methods: [ 'GET', 'POST' ]
        )
    ]
    public function login(AuthenticationUtils $authenticationUtils) : Response
    {
        if($this->getUser() !== null)
        {
            return $this->redirectToRoute('app_admin_default_index');
        }

        // get the login error if there is one
        $error = ($authenticationUtils->getLastAuthenticationError() !== null);

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->renderView('forms/auth.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'ajax_login_url' => $this->generateUrl('app_admin_security_ajax_login'),
        ]);

        $this->getBreadcrumb()->addItem(new BreadcrumbItem(
            'admin.login.label', 
            'admin.login.alt', 
            'app_admin_security_auth_login
        '));

        return $this->render('admin/auth.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Déconnexion
     * @return void
     */
    #[
        RouteAnnotation(
            path: '/auth/logout',
            methods: [ 'GET' ]
        )
    ]
    public function logout() : void
    {
        // Gérée par Symfony
    }

}