<?php

/**
 * Vérification qu'un appel Ajax est appelé en Ajax
 */

namespace App\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
/***/
use App\Controller\AjaxControllerInterface;

final class AjaxSubscriber implements EventSubscriberInterface
{
	
    /**
     * Constructeur
     * @param KernelInterface $kernel 
     */
    public function __construct(private KernelInterface $kernel)
    {

    }

	/**
	 * @param RequestEvent $event
	 * @return void
	 */
	public function onKernelRequest(RequestEvent $event) : void
	{
		$request = $event->getRequest();
        $controllerParam = $request->attributes->get('_controller');
        $controllerData = explode('::', $controllerParam);
        $controllerClassName = $controllerData[0] ?? null;

        // On ne s'interesse qu'aux contrôleurs implémentant AjaxControllerInterface
        if(! class_exists($controllerClassName) or ! is_subclass_of($controllerClassName, AjaxControllerInterface::class))
        {
            return;
        }

        $environment = $this->kernel->getEnvironment();
        $isAjax = $request->isXmlHttpRequest();

        if($environment !== 'dev' and ! $isAjax)
        {
            throw new AccessDeniedException('errors.denied');
        }
	}
	
	/**
	 * Evénements à gérer
	 * @return array
	 */
	public static function getSubscribedEvents() : array
	{
		return [
			'kernel.request' => ['onKernelRequest'],
		];
	}
}