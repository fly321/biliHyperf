# bilibili 粉丝牌自动签到打卡
```shell
composer install
php bin/hyperf.php bilibili:clock_in
```

### 配置文件
> config/autoload/bilibili.php

```php
<?php
return array(
    "userUrl" => "https://space.bilibili.com/52088969", // 替换成你的链接
    "cookie" => BASE_PATH.DIRECTORY_SEPARATOR."cookie.txt", // 根目录新增cookie.txt 内容不要带cookie:
    "info_api" => "https://api.bilibili.com/x/space/wbi/acc/info?mid=%s",
    "send_api" => "https://api.live.bilibili.com/msg/send",
    "medal_wall_api" => "https://api.live.bilibili.com/xlive/web-ucenter/user/MedalWall?target_id=%s"
);
```
![https://telegra.ph//file/e64d2f7a0be695a087f4a.png](https://telegra.ph//file/e64d2f7a0be695a087f4a.png)
