<?php

namespace Application\Services;

use GuzzleHttp\Client;

class LineBotApi
{
    private $apiUrl = 'https://api.line.me/v2/bot/message/reply';
    private $inputArray = [];
    private $outputArray = [];
    private $outputStatus = 0;
    private $restaurantID = '';

    public function __construct()
    {
        $this->setInputData();
    }

    /**
     * @return bool
     */
    public function testWebHook(): bool
    {
        $messages = [];
        $messages['messages'][0] = $this->getFormatTextMessage('ฮัลโหลเวิลด์');
        $messages['messages'][1] = $this->getFormatPictureMessage('https://lh3.googleusercontent.com/p/AF1QipM-AuigFwtqReObR2B_-XB8ng8ip0cBatFGv8QP=s1600-w1024');
        //$messages['messages'][2] = $this->getFormatTextTemplateMessageJson();

        return $this->sentMessage($messages);
    }

    /**
     * @param array $restaurants
     * @return bool|\Psr\Http\Message\ResponseInterface
     */
    public function sendRestaurantsList(array $restaurants)
    {
        $messages = [];
        $messages['messages'][0] = $this->getFormatTextTemplateMessageJson($restaurants);
        return $this->sentMessage($messages);
    }

    public function sendThankyouMessage()
    {
        $messages = [];
        $messages['messages'][0] = $this->getFormatTextMessage('เราได้ทำการส่งรายการอาหาร เรียบร้อยแล้ว');
        return $this->sentMessage($messages);
    }

    public function sendNotUndestandMessage()
    {
        $messages = [];
        $messages['messages'][0] = $this->getFormatTextMessage('ฉันไม่เข้าใจที่คุณพูด กรุณาลองพิมพ์ "สั้งข้าว" กับฉันดู');
        return $this->sentMessage($messages);
    }

    public function sendRestaurantDetails($restaurant)
    {
        $messages = [];
        $messages['messages'][0] = $this->getFormatTextMessage('คุณเลือกร้าน ' . $restaurant['name']);
        $messages['messages'][1] = $this->getFormatTextMessage(' ที่อยู่ : ' . $restaurant['address']);
        $messages['messages'][2] = $this->getFormatPictureMessage($restaurant['img']);
        $messages['messages'][3] = $this->getFormatTextMessage('เลือกสั้งอะไรของร้าน ' . $restaurant['name'] . ' ดี?');
        return $this->sentMessage($messages);
    }

    public function checkUserIsSelectRestaurant(): bool
    {
        if (isset($this->inputArray['events'][0]['postback']['data'])) {
            $postbackData = $this->inputArray['events'][0]['postback']['data'];

            if (!isset($postbackData['id'])) {
                $postbackData['id'] = 'none';
            } else {
                $this->restaurantID = $postbackData;
            }

            if ((string)$postbackData['id'] !== 'none') {
                return true;
            }

            file_put_contents('postback.txt', $postbackData . PHP_EOL, FILE_APPEND);
        }
        return false;
    }

    /**
     * @return array
     */
    public function getInput(): array
    {
        return $this->inputArray;
    }

    /**
     * @return array
     */
    public function getOutput(): array
    {
        return $this->outputArray;
    }

    /**
     * @return int
     */
    public function getOutputStatus(): int
    {
        return $this->outputStatus;
    }

    /**
     * @return string
     */
    public function getRestaurantID(): string
    {
        return $this->restaurantID;
    }

    /**
     * @return bool
     */
    public function checkUserIsOrder(): bool
    {
        if (isset($this->inputArray['events'][0]['message']['text'])) {
            $userText = $this->inputArray['events'][0]['message']['text'];
            if (strpos($userText, 'สั้ง') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $messages
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function sentMessage(array $messages): bool
    {
        $messages['replyToken'] = $this->inputArray['events'][0]['replyToken'];
        $this->outputArray = $messages;
        file_put_contents('output.txt', json_encode($messages, true) . PHP_EOL, FILE_APPEND);

        $client = new Client([
            'headers' => ['Content-Type' => 'application/json; charset=UTF-8', 'Authorization' => 'Bearer ' . getenv('LINE_TOKEN')]
        ]);
        $response = $client->post($this->apiUrl, ['body' => json_encode($messages, true)]);
        $this->outputStatus = $response->getStatusCode();

        if ($this->outputStatus === 200) {
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

    /**
     * @param string $imgUrl
     * @return array
     */
    private function getFormatPictureMessage($imgUrl): array
    {
        $datas = [];
        $datas['type'] = 'image';
        $datas['originalContentUrl'] = $imgUrl;
        $datas['previewImageUrl'] = $imgUrl;

        return $datas;
    }

    private function getFormatTextTemplateMessageJson(array $restaurants)
    {
        return json_decode('{
                  "type": "template",
                  "altText": "This is a buttons template",
                  "template": {
                      "type": "buttons",
                      "thumbnailImageUrl": "https://medias3.prestastore.com/835054-pbig/chat-bot-for-social-networking.jpg",
                      "imageAspectRatio": "rectangle",
                      "imageSize": "cover",
                      "imageBackgroundColor": "#FFFFFF",
                      "title": "Menu",
                      "text": "กรุณาเลือกร้านที่คุณต้องการสั้งอาหาร",
                      "defaultAction": {
                          "type": "uri",
                          "label": "View detail",
                          "uri": "http://example.com/page/123"
                      },
                      "actions": ' . json_encode($restaurants, true) . '
                  }
                }', true);
    }

    public function setInputData(): void
    {
        $jsonInput = file_get_contents('php://input');
        file_put_contents('input.txt', $jsonInput . PHP_EOL, FILE_APPEND);
        $this->inputArray = json_decode($jsonInput, true);
    }

}