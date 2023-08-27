<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BilibiliService;
use App\Service\BilibiliServiceImpl;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;

#[Command]
class BilibiliCommand extends HyperfCommand
{

    #[Inject(BilibiliServiceImpl::class)]
    protected BilibiliService $bilibiliService;
    #[Inject]
    protected \Hyperf\Coordinator\Timer $timer;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('bilibili:clock_in');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('bilbili clock in');
    }

    public function handle()
    {
        date_default_timezone_set('Asia/Shanghai');
        $this->line(" bilibili:clock_in running", "info", true);
        // 每天0点执行
        $this->timer->tick(86400000, function (){
            $this->line("当前时间".date("Y-m-d H:i:s"));
            $this->bilibiliService->setCookie();
            $this->line("获取cookie成功：".$this->bilibiliService->getCookie(), "info", true);
            $data = $this->bilibiliService->listOfFanCards();
            $this->line("获取粉丝勋章列表成功", "info", true);
            $this->line("开始签到", "info", true);
            $jct = $this->bilibiliService->getJct();
            foreach ($data as $item) {
                sleep(5);
                $room_id = $this->bilibiliService->getRoomId($item["medal_info"]['target_id']);
                // 获取房间号 ， 签到,写入日志
                $this->bilibiliService->clockIn($room_id, $jct);
                $this->line("当前时间".date("Y-m-d H:i:s").":"."{$item['target_name']} 签到成功", "info", true);
            }
        });


    }
}
