<?php

namespace App\Enums;

enum TaskStatus:string
{
    case TODO = 'todo';
    case DEVELOP = 'develop';
    case DONE = 'done';
    case CLOSE = 'close';

    public static function getValues(): array
    {
        return [
            self::TODO,
            self::DEVELOP,
            self::DONE,
            self::CLOSE,
        ];
    }
}
