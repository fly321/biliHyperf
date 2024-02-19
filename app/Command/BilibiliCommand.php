<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\BilibiliService;
use App\Service\BilibiliServiceImpl;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
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

    public const RoomMap = array(
        698438232 => 22673512,
        17992194 => 5573692,
        1802392 => 9395707,
        7706705 => 80397,
        1104048496 => 22642754,
        15641218 => 573893,
        52522 => 850221,
        1359949418 => 23224539,
        11431931 => 675014,
        1652111117 => 24008276,
        1501380958 => 22746343,
        477342747 => 21672022,
        1011797664 => 24697117,
        1694610556 => 25971921,
        698029620 => 22696653,
        16510117 => 623698,
        471460273 => 22195814,
        1219196749 => 23303212,
        455899334 => 22163937,
        1734978373 => 22696954,
        551527845 => 22810269,
        631070414 => 22389206,
        1950658 => 41682,
        2110567804 => 23794191,
        51030552 => 1603600,
        43272050 => 21646457,
        3821157 => 21692711,
        283886865 => 9234980,
        11073 => 48743,
        75937648 => 21725187,
        14633696 => 8781662,
        4176573 => 870691,
        1856528671 => 23256987,
        745493 => 8792912,
        479633069 => 21677969,
        899804 => 411318,
        617459493 => 22384516
    );


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
//        if (!$this->num) {
        $this->__logicHandle()();
//        }

        /*$channel = new Channel(1);
        $this->timer->tick(600, $this->__logicHandle());
        $channel->pop(0);*/
//        Event::wait();
    }


    private function __logicHandle(): \Closure
    {
        return function () {
            $this->num++;
            $this->line("第{$this->num}次运行", "info", true);
            $this->line("开始执行", "info", true);
            $this->line("当前时间" . date("Y-m-d H:i:s"));
            $this->bilibiliService->setCookie();
            $this->line("获取cookie成功：" . $this->bilibiliService->getCookie(), "info", true);
            $data = $this->bilibiliService->listOfFanCards();
            $this->line("获取粉丝勋章列表成功:" . json_encode($data), "info", true);
            $this->line("开始签到", "info", true);
            $jct = $this->bilibiliService->getJct();
            $this->line("jct:" . $jct);
            foreach ($data as $item) {
                $this->bilibiliService->setData($item);
                sleep(5);
                try {
                    $room_id = (string)static::RoomMap[$item["medal_info"]['target_id']];
                } catch (\Exception $e) {
                    $room_id = $this->bilibiliService->getRoomId($item["medal_info"]['target_id']);
                }
                $this->line("当前房间号" . $room_id);
                // 获取房间号 ， 签到,写入日志
                $this->bilibiliService->clockIn($room_id, $jct);
//                $msg = "当前时间[" . date("Y-m-d H:i:s") . "]:" . "{$item['target_name']} 【{$room_id}】 签到成功";
//                $this->line($msg, "info", true);
                try {
                    $this->sendMsg($item, $room_id);
                } catch (GuzzleException|Exception $e) {
                    $this->line("发送失败:" . $e->getMessage(), "error", true);
                }
            }
        };
    }

    private function sendMsg($item, $room_id) {
        $time = date("Y-m-d H:i:s");
        $avatar = $item['target_icon'];
/*        $markdown = <<<MARKDOWN
## 签到结果
> 用户名 : {$item['target_name']}
> 签到房间 : {$room_id}
> 签到时间 : {$time}
> 签到结果 : 成功
MARKDOWN;*/
        $msg = "用户名 : {$item['target_name']}\n签到房间 : {$room_id}\n签到时间 : {$time}\n签到结果 : 成功\n\n";
        $this->bilibiliService->sendWechatNews("签到消息", $msg, $item["link"] ?? "", $avatar);
    }

}
