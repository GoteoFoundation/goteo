<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Goteo\Application\View;
use Goteo\Entity\DataSet;
use Goteo\Repository\DataSetRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DataSetApiController extends AbstractApiController
{
    public function dataSetsAction(Request $request): JsonResponse {
        $filters = [];

        $page = $request->query->getDigits('page', 0);
        $limit = $request->query->getDigits('limit', 10);
        $offset = 0;

        if ($request->query->has('sdg') && $request->query->getAlnum('sdg')) {
            $filters['sdgs'] = explode(',', $request->query->getAlnum('sdg'));
        }

        if ($request->query->has('footprint') && $request->query->getDigits('footprint')) {
            $filters['footprints'] = explode(',', $request->query->getDigits('footprint'));
        }

        $dataSetRepository = new DataSetRepository();
        $dataSets = $dataSetRepository->getListByFootprintAndSDGs($filters);

        View::setTheme('responsive');
        return $this->jsonResponse([
            'total' => count($dataSets),
            'page' => $page,
            'limit' => $limit,
            'result_total' => count($dataSets),
            'html' => View::render(
                'partials/components/data_sets', [
                    'dataSets' => $dataSets
                ]
            )
        ]);
    }
}
