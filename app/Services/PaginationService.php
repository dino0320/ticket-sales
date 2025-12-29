<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginationService
{
    /**
     * Get paginated data response
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    public static function getPaginatedDataResponse(LengthAwarePaginator $paginator, array $data): array
    {
        $response = [
            'data' => $data,
            'links' => self::getLinksResponse($paginator->linkCollection()->all()),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
        ];

        return $response;
    }

    /**
     * Get links response
     *
     * @param array $links
     * @return array
     */
    private static function getLinksResponse(array $links): array
    {
        $linksResponse = [];
        foreach ($links as $link) {
            $linksResponse[] = [
                'url' => $link['url'],
                'label' => html_entity_decode($link['label']),
                'active' => $link['active'],
            ];
        }

        return $linksResponse;
    }
}
