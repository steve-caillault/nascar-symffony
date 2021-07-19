<?php

/**
 * Gestion de la réponse en cas d'erreur en Ajax sur l'authentification
 */

namespace App\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
/***/
use App\Service\AjaxResponseService;

final class AuthAjaxSubscriber implements EventSubscriberInterface
{
    /**
     * Constructeur
     * @param TranslatorInterface $translator
     * @param AjaxResponseService $ajaxRequestService
     */
    public function __construct(
        private TranslatorInterface $translator,
        private AjaxResponseService $ajaxResponseService
    )
    {

    }

    /**
     * @param ResponseEvent $event
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event) : void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        // On ne gére que l'appel Ajax à l'authentification
        $route = $request->attributes->get('_route');
        if($route !== 'app_admin_security_ajax_login')
        {
            return;
        }

        // On souhaite gérer uniquement les erreurs 401
        if($response->getStatusCode() !== 401)
        {
            return;
        }

        // Modifie la réponse avec notre formatage
        $responseAjax = $this->ajaxResponseService->getFormatting([
            'error' => $this->translator->trans('credentials.invalid', domain: 'security'),
        ], AjaxResponseService::STATUS_ERROR, statusCode: 401);

        $response
            ->setContent($responseAjax->getContent())
            ->setStatusCode($responseAjax->getStatusCode())
        ;
    }

    /**
     * Evénements à gérer
     * @return array
     */
    public static function getSubscribedEvents() : array
    {
        return [
            // The custom listener must be called before LocaleListener
            'kernel.response' => ['onKernelResponse', 50],
        ];
    }
}