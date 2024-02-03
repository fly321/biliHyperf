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
        "base64" => "AAABMgAQAAEAAAAHAAAAAXsidWlkIjo1MjA4ODk2OSwicm9vbWlkIjo3MTQwOTU5LCJwcm90b3ZlciI6MywiYnV2aWQiOiJ0ZXN0aW5nIiwicGxhdGZvcm0iOiJ3ZWIiLCJ0eXBlIjoyLCJrZXkiOiJZRWZ4a3VLejNFRGJtdXAyaGNIRFhhbXZWNmx4cjREaUI1YjhaRFJPYnZJZVRJcDdkbVFpWmhEUmQ2anBxNFo2M0c0dXowV0xrRE8wc2ZkUnB1T0FkWGwtMGdBSm5OSkpzeS1nMVNOQ3hjRGFZc2xfOWFBR01lQ0RQcjlwOWZ5Wm4xNHBJSGd5NVU2ZXFxMnc5dXZpQlU0UyJ9",
        "api" => "https://api.live.bilibili.com/xlive/web-room/v1/index/getDanmuInfo?id=%s&type=0",
        "heartbeat" => "AAAAHwAQAAEAAAACAAAAAVtvYmplY3QgT2JqZWN0XQ=="
    ],
    "sapi" => "https://api.bilibili.com/x/frontend/finger/spi",
    "xz" => \Hyperf\Support\env("XZ_URL"),
    "wechat_hook" => \Hyperf\Support\env("WECHAT_HOOK")
);