<?php

/**
 * Recherche d'entité par autocomplètion
 */

namespace App\Controller\Admin;

use Symfony\Component\Validator\{
    Validation,
    Constraints
};
use Symfony\Component\HttpFoundation\{ 
    Request,
    JsonResponse
};
use Symfony\Component\Routing\Annotation\Route as RouteAnnotation;
use Doctrine\ORM\EntityManagerInterface;
/***/
use App\Service\AjaxResponseService;
use App\Validator\ClassExists;
use App\Repository\SearchingRepositoryInterface;

final class AutocompleteSearchingController extends AbstractAjaxController {

    /**
     * Recherche par autocomplètion
     * @param Request $request, 
     * @param AjaxResponseService $responseService,
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[
        RouteAnnotation(
            path: '/ajax/entity/searching',
            methods: [ 'POST' ]
        )
    ]
    public function searching(
        Request $request, 
        AjaxResponseService $responseService,
        EntityManagerInterface $entityManager
    ) : JsonResponse
    {
        $post = $request->request;

        $constraints = new Constraints\Collection([
            'class' => [
                new Constraints\NotBlank(),
                new ClassExists(),
            ],
            'searching' => [
                new Constraints\NotBlank(),
                new Constraints\Length(min: 3, max: 50),
            ],
        ]);

    
        $data = [
            'class' => $post->get('class'),
            'searching' => $post->get('searching'),
        ];


        $results = [];
        $errors = Validation::createValidator()->validate($data, $constraints);

        /*foreach($errors as $error)
        {
            dump($error->getMessage());
        }*/

        if(count($errors) === 0)
        {
            $repository = $entityManager->getRepository($data['class']);
            if($repository instanceof SearchingRepositoryInterface)
            {
                $entities = $repository->findBySearching($data['searching'], 10);
                foreach($entities as $entity)
                {
                    $results[] = [
                        'value' => $entity->getAutocompleteId(),
                        'text' => $entity->getAutocompleteDisplayValue(),
                    ];
                }
            }
        }

        return $responseService->getFormatting($results, AjaxResponseService::STATUS_SUCCESS);
    }

}