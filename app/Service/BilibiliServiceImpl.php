<?php

namespace App\Service;

use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Logger\LoggerFactory;
use Swoole\Exception;

class BilibiliServiceImpl implements BilibiliService
{

    private string $cookie = "";
    private string $jct = "";
    private string $key = "bilbili";
    #[Value("bilibili")]
    private array $bilibili;

    #[Inject]
    private ClientFactory $clientFactory;

    #[Inject]
    private LoggerFactory  $loggerFactory ;



    public function getLists(): array
    {
        // TODO: Implement getLists() method.
        return $this->bilibili['lists'];
    }

    public function getCookie(): string
    {
        return file_get_contents($this->bilibili['cookie']);
    }

    public function setCookie()
    {
        $this->cookie = $this->getCookie();
    }

    public function getJct(): string
    {
        //  正则匹配 bili_jct=(\w+)
        preg_match("/bili_jct=(\w+)/", $this->cookie, $matches);
        return $matches[1] ?? "";
    }


    public function getUid(string $url): string
    {
        // \d+ 匹配数字
        preg_match("/com\/(\d+)/", $url, $matches);
        return $matches[1] ?? "";
    }

    public function getRoomId(string|int $uid): string
    {
        $client = $this->clientFactory->create();
        $url = sprintf($this->bilibili['info_api'], $uid);
        $response = $client->get($url, [
            "headers" => [
                "cookie" => $this->cookie
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data']['live_room']['roomid'] ?? "";
    }

    public function clockIn(string $room_id, string $jct): void
    {
        $client = $this->clientFactory->create();
        $url = $this->bilibili['send_api'];

        $data = [
            "bubble" => "0",
            "msg" => "打卡",
            "color" => "5566168",
            "mode" => "1",
            "room_type" => "0",
            "jumpfrom" => "0",
            "fontsize" => "25",
            "rnd" => time(),
            "roomid" => $room_id,
            "csrf" => $jct,
            "csrf_token" => $jct
        ];

        $result = $client->post($url, [
            "headers" => [
                "cookie" => $this->cookie
            ],
            "form_params" => $data
        ]);



        try {
            $array = json_decode($result->getBody()->getContents(), true);// 写入日志
            $this->loggerFactory->make("bilibili")->info("room_id:$room_id", $array);
        } catch (Exception $e) {
            $this->loggerFactory->make("bilibili")->error("room_id:$room_id", [
                "error" => $e->getMessage(),
                "content" => $result->getBody()->getContents(),
            ]);
        }

    }

    public function listOfFanCards(): array
    {
        try {
            $uid = $this->getUid($this->bilibili['userUrl']);
            $client = $this->clientFactory->create();
            $response = $client->get(sprintf($this->bilibili['medal_wall_api'], $uid), [
                "headers" => [
                    "cookie" => $this->cookie
                ]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return $data["data"]["list"] ?? [];
        } catch (GuzzleException $e) {
            $this->loggerFactory->make("bilibili")->error("获取粉丝勋章列表失败", [
                "error" => $e->getMessage(),
            ]);
            return [];
        }
    }
}