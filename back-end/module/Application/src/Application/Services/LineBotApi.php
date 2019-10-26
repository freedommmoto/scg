<?php

namespace Application\Services;

use GuzzleHttp\Client;

class LineBotApi
{
    private $apiUrl = 'https://api.line.me/v2/bot/message/reply';
    private $inputArray = [];
    private $outputArray = [];
    private $outputStatus = 0;

    /**
     * @return bool
     */
    public function testWebHook(): bool
    {
        $this->setInputData();
        $replyToken = $this->inputArray['events'][0]['replyToken'];

        $messages = [];
        $messages['replyToken'] = $replyToken;
        $messages['messages'][0] = $this->getFormatTextMessage('ฮัลโหลเวิลด์');
        //$messages['messages'][0] = $this->getFormatTextTemplateMessageJson();

        return $this->sentMessage($messages);
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
     * @param $messages
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function sentMessage($messages): bool
    {
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

    private function getFormatTextTemplateMessageJson()
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
                      "actions": [
                          {
                            "type": "postback",
                            "label": "ร้านคุ้มอีสาน",
                            "data": "action=buy&itemid=123"
                          },
                          {
                            "type": "postback",
                            "label": "ร้านอาหารเจ เวจจี้ดิช",
                            "data": "action=add&itemid=123"
                          },
                          {
                            "type": "postback",
                             "label": "About Beef",
                            "data": "action=add&itemid=123"
                          },
                          {
                            "type": "postback",
                             "label": "ฉันยังไม่หิว",
                            "data": "action=add&itemid=123"
                          }
                      ]
                  }
                }', true);
    }

    private function setInputData(): void
    {
        $jsonInput = file_get_contents('php://input');
        file_put_contents('input.txt', $jsonInput . PHP_EOL, FILE_APPEND);
        $this->inputArray = json_decode($jsonInput, true);
    }

}