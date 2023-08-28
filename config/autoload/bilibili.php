<?php
return array(
    "userUrl" => "https://space.bilibili.com/52088969",
    "cookie" => BASE_PATH.DIRECTORY_SEPARATOR."cookie.txt",
    "info_api" => "https://api.bilibili.com/x/space/wbi/acc/info?mid=%s",
    "send_api" => "https://api.live.bilibili.com/msg/send",
    "medal_wall_api" => "https://api.live.bilibili.com/xlive/web-ucenter/user/MedalWall?target_id=%s",
    "msg" => "^_^ 打卡",
    "is_tag" => true, // 是否自动带牌:true 自动带牌 false 不自动带牌
    "tag_api" => "https://api.live.bilibili.com/xlive/app-ucenter/v1/fansMedal/wear"
);