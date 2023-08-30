<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BilibiliServiceImpl;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
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
        $this->bilibiliService->setCookie();
        $uid = $this->bilibiliService->getUid($url);
        $room_id = $this->bilibiliService->getRoomId($uid);
        $data = $this->bilibiliService->generateMessage($room_id);

        $client = $this->clientFactory->create($data["host"]);
        $client->push(json_encode([
            "type" => "binary",
            "data" => $data["msg"]
        ]), WEBSOCKET_OPCODE_BINARY);
        var_dump($data["host"]);
        var_dump($client);
        // 循环接收数据
        while (true) {
            // 接收的是二进制数据
            $res = $client->recv(0);
            var_dump($res);
            exit();
            // 我需要转换成字符串
            var_dump($res);
            if ($res === false) {
                // 重连
                $client->close();
                return $this->logic($url);
            }
        }
        // client 掉线重连

    }
}
