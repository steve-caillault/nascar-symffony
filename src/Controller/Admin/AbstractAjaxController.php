<?php

/**
 * Contrôleur abstrait Ajax du panneau d'administration
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/***/
use App\Controller\AjaxControllerInterface;

abstract class AbstractAjaxController extends AbstractController implements AjaxControllerInterface, AdminControllerInterface {

}