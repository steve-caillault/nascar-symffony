<?php

/**
 * Point d'entrée de l'authentification du panneau d'administration
 */

namespace App\Security\Admin;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\{ 
    Request, 
    JsonResponse, 
    RedirectResponse 
};
/***/
use App\Service\AjaxResponseService;

final class AuthenticationEntryPoint implements AuthenticationEntryPointInterface {

    /**
     * Service de formatage d'une réponse Ajax
     * @var AjaxResponseService
     */
    private AjaxResponseService $ajaxResponseService;

    /**
     * Générateur d'url
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * Affecte le service de formatage de la réponse Ajax
     * @param AjaxResponseService $ajaxResponseService
     * @return void
     * @required
     */
    public function setAjaxResponseService(AjaxResponseService $ajaxResponseService) : void
    {
        $this->ajaxResponseService = $ajaxResponseService;
    }

    /**
     * Modifie le générateur d'URL
     * @param UrlGeneratorInterface $urlGenerator
     * @return void
     * @required
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator) : void
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritdoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $loginUrl = $this->urlGenerator->generate(
            'app_admin_security_auth_login', 
            referenceType: UrlGeneratorInterface::ABSOLUTE_URL
        );

        if($request->isXmlHttpRequest())
        {
            return $this->ajaxResponseService->getFormatting([
                'login_url' => $loginUrl,
            ], AjaxResponseService::STATUS_ERROR, statusCode: 401);
        }
        else
        {
            return new RedirectResponse($loginUrl);
        }
    }

}