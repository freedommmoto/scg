<?php

namespace Application\Services;

use GuzzleHttp\Client;

class GooglePlaceApi
{
    private $apiURL;

    private $allPageData = [];

    public function __construct()
    {
        $this->apiURL = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
        $this->limitPage = 20;
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
        }

        return $this->allPageData;
    }

    private function getOnlyName(): void
    {
        $rows = [];
        foreach ($this->allPageData as $row) {
            $rows[] = $row['name'];
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
        $client = new Client();
        $response = $client->request('GET', $this->apiURL, ['query' => [
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
        $client = new Client();
        $response = $client->request('GET', $this->apiURL, ['query' => [
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