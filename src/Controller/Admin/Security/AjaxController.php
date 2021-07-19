<?php

/**
 * Contrôleur d'authentification du panneau d'administration depuis un appel Ajax
 */

namespace App\Controller\Admin\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Controller\Admin\AdminControllerInterface;
use App\Controller\AjaxControllerInterface;
use App\Service\AjaxResponseService;

final class AjaxController extends AbstractController implements AjaxControllerInterface, AdminControllerInterface {

    /**
     * Connexion en Ajax
     * @param AjaxResponseService $responseService
     * @return JsonResponse
     */
    #[
        RouteAnnotation(
            path: '/auth/login/ajax',
            methods: [ 'POST' ]
        )
    ]
    public function login(AjaxResponseService $responseService) : JsonResponse
    {
        $logged = ($this->getUser()?->getId() !== null);

        $responseStatus = ($logged) ? AjaxResponseService::STATUS_SUCCESS : AjaxResponseService::STATUS_ERROR;

        return $responseService->getFormatting([
            'logged' => $logged,
        ], $responseStatus);
    }

}