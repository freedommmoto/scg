<?php

namespace Application\Services;

use GuzzleHttp\Client;

class LineBotApi
{
    private $apiUrl = 'https://api.line.me/v2/bot/message/reply';
    private $inputArray = [];

    /**
     * @return bool
     */
    public function webHook(): bool
    {
        $this->setInputData();
        $replyToken = $this->inputArray['events'][0]['replyToken'];

        $messages = [];
        $messages['replyToken'] = $replyToken;
        $messages['messages'][0] = $this->getFormatTextMessage('ฮัลโหลเวิลด์');

        return $this->sentMessage($messages);
    }

    /**
     * @param $messages
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function sentMessage($messages): bool
    {
        $client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=UTF-8', 'Authorization' => 'Bearer ' . getenv('LINE_TOKEN')]
        ]);
        $response = $client->post($this->apiUrl, ['body' => json_encode($messages)]);

        if ($response->getStatusCode() === 200) {
            return true;
        }

        return false;
    }

    /**
     * @param string $text
     * @return array
     */
    private function getFormatTextMessage($text): array
    {
        $datas = [];
        $datas['type'] = 'text';
        $datas['text'] = $text;
        return $datas;
    }

    private function setInputData(): void
    {
        $jsonInput = file_get_contents('php://input');
        file_put_contents('input.txt', $jsonInput . PHP_EOL, FILE_APPEND);
        $this->inputArray = json_decode($jsonInput, true);
    }

}