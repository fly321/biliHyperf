<?php


declare(strict_types=1);

class BilibiliServiceImpl
{

    private string $cookie = "";
    private string $jct = "";
    private string $key = "bilbili";
    private array $bilibili;
    protected int $port = 2243;
    private ClientFactory $clientFactory;

    protected array $_data;

    public function __construct()
    {
        $this->bilibili = [
            "userUrl" => "https://space.bilibili.com/52088969",
            "cookie" => "",
            "info_api" => "https://api.bilibili.com/x/space/acc/info?mid=%s",
            "send_api" => "https://api.live.bilibili.com/msg/send",
            "medal_wall_api" => "https://api.live.bilibili.com/xlive/web-ucenter/user/MedalWall?target_id=%s",
            "msg" => "^_^ 打卡",
            "is_tag" => true, // 是否自动带牌:true 自动带牌 false 不自动带牌
            "tag_api" => "https://api.live.bilibili.com/xlive/app-ucenter/v1/fansMedal/wear",
            "zhibo" => [
                "base64" => "AAABMgAQAAEAAAAHAAAAAXsidWlkIjo1MjA4ODk2OSwicm9vbWlkIjo3MTQwOTU5LCJwcm90b3ZlciI6MywiYnV2aWQiOiJ0ZXN0aW5nIiwicGxhdGZvcm0iOiJ3ZWIiLCJ0eXBlIjoyLCJrZXkiOiJZRWZ4a3VLejNFRGJtdXAyaGNIRFhhbXZWNmx4cjREaUI1YjhaRFJPYnZJZVRJcDdkbVFpWmhEUmQ2anBxNFo2M0c0dXowV0xrRE8wc2ZkUnB1T0FkWGwtMGdBSm5OSkpzeS1nMVNOQ3hjRGFZc2xfOWFBR01lQ0RQcjlwOWZ5Wm4xNHBJSGd5NVU2ZXFxMnc5dXZpQlU0UyJ9",
                "api" => "https://api.live.bilibili.com/xlive/web-room/v1/index/getDanmuInfo?id=%s&type=0",
                "heartbeat" => "AAAAHwAQAAEAAAACAAAAAVtvYmplY3QgT2JqZWN0XQ=="
            ],
            "sapi" => "https://api.bilibili.com/x/frontend/finger/spi"
        ];
        $this->setCookie();
        $this->clientFactory = new clientFactory();
    }


    public function getLists(): array
    {
        // TODO: Implement getLists() method.
        return $this->bilibili['lists'];
    }

    public function getCookie(): string
    {
        return trim($this->bilibili['cookie']);
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
        $url = sprintf($this->bilibili['info_api'], $uid);
        $response = $this->clientFactory->create()->get($url, [
            "headers" => [
                "cookie" => $this->cookie,
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36"
            ]
        ]);
        $data = json_decode($response, true);
        return (string)$data['data']['live_room']['roomid'] ?? "";
    }

    private function gotoLink(string $link){
        try {
            $this->clientFactory->create()->get($link, [
                "headers" => [
                    "cookie" => $this->cookie
                ]
            ]);
        } catch (Throwable $e) {
            var_dump($e->getMessage());
        }
    }

    public function clockIn(string $room_id, string $jct): void
    {
//        $this->gotoLink($this->_data['link']);
        try {
            if ($this->bilibili['is_tag']) {
                $this->useTag($this->_data["medal_info"]["medal_id"], $jct);
            }
        } catch (\Throwable $e) {
        }

        $url = $this->bilibili['send_api'];

        $data = [
            "bubble" => "0",
            "msg" => $this->bilibili["msg"],
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

        $result = $this->clientFactory->create()->post($url, [
            "headers" => [
                "cookie" => $this->cookie
            ],
            "form_params" => $data
        ]);



        try {
            $array = json_decode($result, true);// 写入日志
            var_dump($array);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

    }

    public function listOfFanCards(): array
    {
        var_dump("是否进入listOfFanCards");
        try {
            $uid = $this->getUid($this->bilibili['userUrl']);
            $client = $this->clientFactory->create();
            $response = $client->get(sprintf($this->bilibili['medal_wall_api'], $uid), [
                "headers" => [
                    "cookie" => $this->cookie
                ]
            ]);
            $data = json_decode($response, true);
            return $data["data"]["list"] ?? [];
        } catch (\Throwable $e) {
            var_dump([
                "error" => $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ]);
            return [];
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->_data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->_data = $data;
    }

    public function useTag(int $medal_id, string $jct): void
    {
        $url = $this->bilibili['tag_api'];
        $data = [
            "medal_id" => $medal_id,
            "visit_id" => "7dmon42ggv40",
            "csrf_token" => $jct,
            "csrf" => $jct
        ];
        $result = $this->clientFactory->create()->post($url, [
            "headers" => [
                "cookie" => $this->cookie
            ],
            "form_params" => $data
        ]);

    }

    public function generateMessage(string $room_id): array
    {
        $base64 = $this->bilibili['zhibo']['base64'];
        $str = base64_decode($base64, true);
        $sp = strpos($str, "{");
        $s1 = substr($str, $sp);
        $kh = $this->getKeyAndHost($room_id);
        // json
        $json = json_decode($s1, true);
        $json["roomid"] = (int)$room_id;
        $json["uid"] = (int)$this->getUid($this->bilibili['userUrl']);
//        $json["buvid"] = $this->getBuvid();
        $json["buvid"] = $this->getb3();
        $json["key"] = $this->getKeyAndHost($room_id)["key"];
        // 从指定位置sp开始替换
        $str = substr_replace($str, json_encode($json), $sp);
        // base64
        $base64 = base64_encode($str);
        $data =array(
            "host" => "wss://".$kh["host"]."/sub",
            "msg" => $base64
        );
        var_dump($data);
        return $data;

    }

    public function getBuvid(){
        preg_match("/buvid3=(.*?);/", $this->cookie, $matches);
        return $matches[1] ?? "";
    }

    public function getKeyAndHost(string $room_id){
        $client = $this->clientFactory->create();
        $response = $client->get(sprintf($this->bilibili['zhibo']['api'], $room_id), [
            "headers" => [
                "cookie" => $this->cookie
            ]
        ]);
        $data = json_decode($response, true);
        $hostArr = $data["data"]["host_list"];

        return [
            "key" => $data["data"]["token"],
            "host" => $hostArr[0]["host"],
        ];
    }

    public function getb3()
    {
        // TODO: Implement getb3() method.
        $clinet = $this->clientFactory->create();
        $response = $clinet->get($this->bilibili['sapi'], [
            "headers" => [
                "cookie" => $this->cookie
            ]
        ]);
        $data = json_decode($response, true);
        return $data["data"]["b_3"];
    }
}

class clientFactory {
    public function create(){
        return new self();
    }

    public function get($url, $options){
        $curl = \curl_init();
        $headers = $options["headers"];
        $ha = [];
        foreach ($headers as $key => $value) {
            $ha[] = $key.":".$value;
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $ha,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function post($url, $options) {
        $curl = \curl_init();
        $headers = $options["headers"];
        $ha = [];
        foreach ($headers as $key => $value) {
            $ha[] = $key.":".$value;
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $options["form_params"],
            CURLOPT_HTTPHEADER => $ha,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}

$service = new BilibiliServiceImpl();
$data = $service->listOfFanCards();
$jct = $service->getJct();
foreach ($data as $item) {
    $service->setData($item);
    sleep(5);
    try {
        $room_id = $service->getRoomId($item["medal_info"]['target_id']);// 获取房间号 ， 签到,写入日志
        $service->clockIn($room_id, $jct);
        var_dump("当前时间" . date("Y-m-d H:i:s") . ":" . "{$item['target_name']} 签到成功", "info", true);
    } catch (Exception $e) {
        var_dump($e->getMessage());
    }
}