<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BilibiliServiceImpl;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Config\Annotation\Value;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Hyperf\WebSocketClient\ClientFactory;


#[Command]
class ZhiboOnline extends HyperfCommand
{
    #[Inject]
    protected BilibiliServiceImpl $bilibiliService;
    #[Inject]
    protected ClientFactory $clientFactory;
    protected int $time = 0;
    #[Value("bilibili")]
    protected array $bilibili;
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('zhibo:online');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
        // 接收url
        $this->addArgument('url', InputArgument::REQUIRED, '用户url');
    }

    public function handle()
    {
        $url = $this->input->getArgument('url');
        $this->line($url);
        $this->logic($url);

    }

    public function logic($url){
        $this->line("进入直播间中....", "info", true);
        $this->bilibiliService->setCookie();
        $uid = $this->bilibiliService->getUid($url);
        $room_id = $this->bilibiliService->getRoomId($uid);
        $data = $this->bilibiliService->generateMessage($room_id);

        $client = $this->clientFactory->create($data["host"], false);
        // string 转 二进制

        $client->push(base64_decode($data["msg"]), WEBSOCKET_OPCODE_BINARY);
        $this->time = time();
//        var_dump($data["host"]);
//        var_dump($client);
        // 循环接收数据
        while (true) {
            if ($this->time + 10 < time()) {
                $this->line("发送心跳包", "info", true);
                $client->push(base64_decode($this->bilibili["zhibo"]["heartbeat"]), WEBSOCKET_OPCODE_BINARY);
                $this->time = time();
            }
            // 接收的是二进制数据
            $res = $client->recv(0);

            if (!$res) {
                // 重连
                $this->line("重连中...", "info", true);
                $client->close();
                return $this->logic($url);
            }else {
                // 判断是否存在{
                $msg = $res->getData();
                $pos = strpos($msg, "{");
                if ($pos !== false) {
                    $msg = substr($msg, $pos);
//                    $msg = json_decode($msg, true);
                    $this->line("msg:". $msg);
                }

            }
        }

    }
}

