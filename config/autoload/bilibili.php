<?php
return array(
    "userUrl" => "https://space.bilibili.com/52088969",
    "cookie" => BASE_PATH.DIRECTORY_SEPARATOR."cookie.txt",
    "info_api" => "https://api.bilibili.com/x/space/wbi/acc/info?mid=%s",
    "send_api" => "https://api.live.bilibili.com/msg/send",
    "medal_wall_api" => "https://api.live.bilibili.com/xlive/web-ucenter/user/MedalWall?target_id=%s",
    "msg" => "^_^ 打卡",
    "is_tag" => true, // 是否自动带牌:true 自动带牌 false 不自动带牌
    "tag_api" => "https://api.live.bilibili.com/xlive/app-ucenter/v1/fansMedal/wear",
    "zhibo" => [
        "base64" => "AAABMAAQAAEAAAAHAAAAAXsidWlkIjo1MjA4ODk2OSwicm9vbWlkIjo4MDM5NywicHJvdG92ZXIiOjMsImJ1dmlkIjoidGVzdDEiLCJwbGF0Zm9ybSI6IndlYiIsInR5cGUiOjIsImtleSI6ImZFbHVBX3FrN0xNZ0VNV29KUlZzWWp0T0ZSSDBMb3Awd3c1VGpkZUt6RTdzRGZfMHJJc2NZUjZ0MWpTYVVKX1VQeTZTcEpYTmtBM2pqeWhWSlRPM3pfZTRqdXgwRFNvZUd3Qlh4SVVVbXBXZHBGUjJ4VlNKbnExUDJ1aHp0MVgwVUdvLWRMZFZzakpWYTN0NlhoYzBJZz09In0=",
        "api" => "https://api.live.bilibili.com/xlive/web-room/v1/index/getDanmuInfo?id=%s&type=0",
        "heartbeat" => "AAAAHwAQAAEAAAACAAAAAVtvYmplY3QgT2JqZWN0XQ=="
    ]
);