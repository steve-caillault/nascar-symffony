<?php

/**
 * Appel Ajax du panneau d'administration
 */

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Service\AjaxResponseService;

final class AjaxController extends AbstractAjaxController {

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