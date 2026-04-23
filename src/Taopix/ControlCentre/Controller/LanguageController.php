<?php

namespace Taopix\ControlCentre\Controller;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Taopix\ControlCentre\Helper\Loader\LanguageLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[OA\Tag(name: 'Localization', description: 'Localization related endpoints'), OA\Response(ref: '#/components/responses/TooManyRequests', response: 429)]
#[OA\Response(ref: '#/components/responses/RequestMalformed', response: 400), OA\Response(ref: '#/components/responses/GenericError', response: 404), OA\Response(ref: '#/components/responses/ResponseMalformed', response: 500)]
class LanguageController extends AbstractController
{
    #[Route('/api/language/{language}/{section}', name: 'loadLanguageRoute',
        requirements: ['language' => '[a-zA-Z]{2}(_[a-zA-Z]{2})?', 'section' => '\*|[a-zA-Z]+'], defaults: ['language' => 'en', 'section' => '*'],
        methods: [Request::METHOD_GET])]
    #[OA\Get(description: 'Returns translated strings for the given language code and section', parameters: [
            new OA\Parameter(name: 'language', description: 'Taopix language code.', in: 'path', required: true),
            new OA\Parameter(name: 'section', description: 'Section code to get strings for.', in: 'path', required: true),
        ], responses: [
            200 => new OA\Response(response: 200, description: 'Strings loaded', content: new OA\JsonContent(ref: '#/components/schemas/AssociativeArray', example: ['bold' => 'Bold', 'italic' => 'Italic'])),
    ])]
    public function loadLanguage(Request $request, CacheInterface $languageCache): JsonResponse
    {
        $response = new JsonResponse(null, 200, [
			'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type']
		]);

        if (Request::METHOD_OPTIONS === $request->getMethod()) {
			$response->headers->add(['Access-Control-Max-Age' => 7200]);
			return $response;
		}

        $projectBaseDir = $this->getParameter('kernel.project_dir');
        $params = $request->attributes->get('_route_params');
        $key = $params['language'].'-'.$params['section'];

        try {
                $strings = $languageCache->get($key, function () use ($params, $projectBaseDir) {
                $loader = new LanguageLoader($projectBaseDir);

                return $loader->loadLanguage($params['language'], $params['section']);
            });
        } catch (\Throwable $ex) {
            //$response->setStatusCode(Response::HTTP_NOT_FOUND);
            $strings = [];
        }

        return $response->setData($strings);
    }
}
