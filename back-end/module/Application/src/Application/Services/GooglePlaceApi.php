<?php

namespace Application\Services;

use GuzzleHttp\Client;

class GooglePlaceApi
{
    private $client;
    private $placeURL = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
    private $photoURL = 'https://maps.googleapis.com/maps/api/place/photo';
    private $allPageData = [];
    private $limitPage = 20;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param bool $showOnlyName
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllPage($showOnlyName = false): array
    {
        $firstPageArray = $this->getFirstPage();
        if (isset($firstPageArray['results'])) {
            $this->allPageData = array_merge($firstPageArray['results'], $this->allPageData);
        }

        if (isset($firstPageArray['next_page_token'])) {
            $pageArray['next_page_token'] = $firstPageArray['next_page_token'];

            $lastPage = false;
            $numPage = 0;

            while (!$lastPage && $numPage < $this->limitPage) {
                set_time_limit(10);
                $numPage++;

                if (!isset($pageArray['next_page_token'])) {
                    $lastPage = true;
                } else {
                    /**
                     * the pagetoken isn't valid yet, you have to wait a few seconds between consecutive requests.
                     */
                    sleep(2);

                    $pageArray = $this->getNextPage($pageArray['next_page_token']);
                    if (isset($pageArray['results'])) {
                        $this->allPageData = array_merge($pageArray['results'], $this->allPageData);
                    }
                }
            }

        }


        if ($showOnlyName) {
            $this->getOnlyName();
        } else {
            $this->getPicture();
        }

        return $this->allPageData;
    }

    private function getPicture(): void
    {
        foreach ($this->allPageData as $key => $row) {

            if (isset($row['photos'][0]['photo_reference'])) {
                $this->client->get($this->photoURL, [
                    'query' => [
                        'key' => getenv('GOOGLE_SECRET_KEY'),
                        'maxwidth' => 1024,
                        'photoreference' => $row['photos'][0]['photo_reference']
                    ],
                    'on_stats' => function ($stats) use (&$url) {
                        $url = $stats->getEffectiveUri();
                    }
                ]);
                $this->allPageData[$key]['loaded_picture'] = (string)$url;
            }
        }
    }

    private function getOnlyName(): void
    {
        $rows = [];
        foreach ($this->allPageData as $row) {
            $rows[]['name'] = $row['name'];
        }
        $this->allPageData = [];
        $this->allPageData['name'] = $rows;
        $this->allPageData['total'] = count($rows);
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFirstPage(): array
    {
        $response = $this->client->request('GET', $this->placeURL, ['query' => [
            'key' => getenv('GOOGLE_SECRET_KEY'),
            'query' => 'restaurants near Bang Sue, Bangkok',
            'location' => '13.8287752,100.5216388',
            'radius' => '12.92'
        ]]);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $content = $response->getBody();
            return json_decode($content, true);
        }
        return [];
    }

    /**
     * @param string $nextPageToken
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getNextPage($nextPageToken): array
    {
        $response = $this->client->request('GET', $this->placeURL, ['query' => [
            'key' => getenv('GOOGLE_SECRET_KEY'),
            'pagetoken' => $nextPageToken
        ]]);

        $statusCode = $response->getStatusCode();

        if ($statusCode === 200) {
            $content = $response->getBody();
            return json_decode($content, true);
        }
        return [];
    }
}