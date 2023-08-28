<?php

namespace App\Service;


interface BilibiliService
{
    public function getLists(): array;
    public function getCookie(): string;
    public function setCookie();
    public function getJct(): string;
    public function getUid(string $url): string;
    public function getRoomId(string|int $uid): string;
    public function clockIn(string $room_id, string $jct): void;
    public function listOfFanCards(): array;
    public function useTag(int $medal_id, string $jct): void;
}