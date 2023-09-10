<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BilibiliService;
use App\Service\BilibiliServiceImpl;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\EventDispatcher;
use Hyperf\Pool\Channel;
use Psr\Container\ContainerInterface;
use Swoole\Event;

#[Command]
class BilibiliCommand extends HyperfCommand
{

    /**
     * @var BilibiliServiceImpl $bilibiliService
     */
    #[Inject(BilibiliServiceImpl::class)]
    protected BilibiliService $bilibiliService;
    #[Inject]
    protected \Hyperf\Coordinator\Timer $timer;
    #[Inject]
    protected EventDispatcher $event;

    private int $num = 0;


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

        set_time_limit(0);
        date_default_timezone_set('Asia/Shanghai');
        $this->line("bilibili:clock_in running...", "info", true);

        // q:判断是否是首次运行
        if (!$this->num) {
            $this->__logicHandle()();
        }

        $channel = new Channel(1);
        $this->timer->tick(600, $this->__logicHandle());
        $channel->pop(0);
//        Event::wait();
    }


    private function __logicHandle(): \Closure{
        $this->num++;
        $this->line("第{$this->num}次运行", "info", true);
        return function (){
            $this->line("开始执行", "info", true);
            $this->line("当前时间".date("Y-m-d H:i:s"));
            $this->bilibiliService->setCookie();
            $this->line("获取cookie成功：".$this->bilibiliService->getCookie(), "info", true);
            $data = $this->bilibiliService->listOfFanCards();
            $this->line("获取粉丝勋章列表成功:".json_encode($data), "info", true);
            $this->line("开始签到", "info", true);
            $jct = $this->bilibiliService->getJct();
            foreach ($data as $item) {
                $this->bilibiliService->setData($item);
                sleep(5);
                $room_id = $this->bilibiliService->getRoomId($item["medal_info"]['target_id']);
                // 获取房间号 ， 签到,写入日志
                $this->bilibiliService->clockIn($room_id, $jct);
                $this->line("当前时间".date("Y-m-d H:i:s").":"."{$item['target_name']} 签到成功", "info", true);
            }
        };
    }

}
