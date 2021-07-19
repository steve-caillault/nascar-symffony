<?php

/**
 * Appel Ajax du panneau d'administration
 */

namespace App\Controller\Admin;

use App\Controller\AjaxControllerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Service\AjaxResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AjaxController extends AbstractController implements AjaxControllerInterface, AdminControllerInterface {

    /**
     * Appel Ajax du panneau d'administration
     * @param AjaxResponseService $responseService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/ajax',
            methods: [ 'GET' ]
        )
    ]
    public function index(AjaxResponseService $responseService) : JsonResponse
    {
        return $responseService->getFormatting([
            'success' => true,
        ], AjaxResponseService::STATUS_SUCCESS);
    }

}