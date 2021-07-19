<?php

/**
 * Contrôleur de test
 */

namespace App\Controller\Testing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Symfony\Component\HttpKernel\Exception\{
    UnauthorizedHttpException,
    AccessDeniedHttpException,
    NotFoundHttpException,
    HttpException,
    
};
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
/***/
use App\UI\Pagination\Pagination;

final class DefaultController extends AbstractController {

    /**
     * Page de tests avec des paramètres dans la route
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/with-params/{param1}',
            requirements: [
                'param1' => '[^\/]+',
            ],
            methods: [ 'GET' ]
        )
    ]
    public function params() : Response
    {
        return new Response();
    }

    /**
     * Page de test de la pagination
     * @param string $paramType
     * @param string $paramName
     * @param int $itemsPerPage
     * @param int $totalItems
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/pagination/{paramType}/{paramName}/{itemsPerPage}/{totalItems}/{customPage}',
            requirements: [
                'paramType' => 'query|route',
                'paramName' => '[^\/]+',
                'itemsPerPage' => '[0-9]+',
                'totalItems' => '[0-9]+',
                'customPage' => '[0-9]+',
            ],
            defaults: [
                'customPage' => 1,
            ],
            methods: [ 'GET' ]
        )
    ]
    public function pagination(
        string $paramType, 
        string $paramName, 
        int $itemsPerPage,
        int $totalItems
    ) : Response
    {
        $pagination = (new Pagination(itemsPerPage: $itemsPerPage, totalItems: $totalItems))
            ->setPageParameterName($paramName)
            ->setPageParameterType($paramType)
        ;

        return $this->render('testing/ui/pagination.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Page de test d'erreur
     * @param int $errorStatus
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/error-{errorStatus}',
            requirements: [ 'errorStatus' => '[0-9]+' ],
            methods: [ 'GET' ]
        )
    ]
    public function error(int $errorStatus) : Response
    {
        
        $exception = match($errorStatus) {
            401 => new UnauthorizedHttpException(''),
            403 => new AccessDeniedHttpException(),
            404 => new NotFoundHttpException(),
            default => new HttpException($errorStatus)
        };

        return $this->forward('App\Controller\ErrorController::index', [
            'exception' => $exception,
        ]);
    }

    /**
     * Page de test de log
     * @param LoggerInterface $logger
     * @param string $message
     * @return Response
     */
    #[
        RouteAnnotation(
            path: '/log/{message}',
            requirements: [ 'message' => '[^\/]+' ],
            methods: [ 'GET' ]
        )
    ]
    public function log(LoggerInterface $logger, string $message) : Response
    {
        $logger->debug($message);
        return new Response();
    }
    

}