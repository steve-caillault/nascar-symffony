<?php

/**
 * Service pour générer une réponse JSON, notamment pour les appels Ajax
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

final class AjaxResponseService {

    public const
        STATUS_SUCCESS = 'SUCCESS',
        STATUS_ERROR = 'ERROR'
    ;

    /**
     * Retourne la réponse JSON à retourner
     * @param array $data
     * @param string $status SUCCESS|ERROR
     * @param ?int $statusCode Code HTTP à retourner
     * @return JsonResponse
     */
    public function getFormatting(
        array $data, 
        string $status = self::STATUS_ERROR, 
        ?int $statusCode = null
    ) : JsonResponse
    {
        $statusCode ??= ($status === self::STATUS_SUCCESS) ? 200 : 400;
        return new JsonResponse([
            'status' => $status,
            'data' => $data,
        ], status: $statusCode);
    }

}