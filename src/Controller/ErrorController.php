<?php

/**
 * Contrôleur des pages d'erreur
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\{ 
    Request,
    Response 
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
/***/
use App\Service\AjaxResponseService;

final class ErrorController extends AbstractController
{
    /**
     * Page d'erreur
     * @param Request $request
     * @param \Throwable $exception
     * @param TranslatorInterface $translator
     * @param AjaxResponseService $ajaxResponseService
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/error',
            methods: [ "GET" ]
        )
    ]
    public function index(
        Request $request, 
        \Throwable $exception,
        TranslatorInterface $translator,
        AjaxResponseService $ajaxResponseService
    ) : Response
    {
        $statusCode = (method_exists($exception, 'getStatusCode')) ? $exception->getStatusCode() : $exception->getCode();
        // $errorMessage = $exception->getMessage();

        // Si le code est invalide
        if($statusCode <= 100 or $statusCode >= 600)
        {
            $statusCode = 500;
        }

        $allowedCodes = [ 401, 403, 404, 500, ];
        $displayingStatusCode = $statusCode;
		if(! in_array($displayingStatusCode, $allowedCodes))
		{
			$displayingStatusCode = 500;
		}
        
        $displayingMessage = match($statusCode) {
            401 => 'error.type.unauthorized',
            403 => 'error.type.denied',
            404 => 'error.type.not_found',
            default => 'error.type.default',
        };

        // $displayingMessage = $errorMessage;

        $displayingData = [
            'code' => $displayingStatusCode,
            'message' => $translator->trans($displayingMessage),
        ];

        if($request->isXmlHttpRequest())
        {
            return $ajaxResponseService->getFormatting(
                $displayingData, 
                AjaxResponseService::STATUS_ERROR, 
                statusCode: $statusCode
            );
        }

        return $this->render('commons/error.html.twig', $displayingData)
            ->setStatusCode($statusCode);
    }

}
