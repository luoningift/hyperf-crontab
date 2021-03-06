<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HKY\HyperfCrontab;

use Hyperf\Crontab\Mutex\ServerMutex;
use Hyperf\Crontab\Strategy\Executor;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'listeners' => [
                CrontabDispatcherStartedListener::class
            ],
            'dependencies' => [
                Executor::class => CrontabExecutor::class,
                ServerMutex::class => RedisServerMutex::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
                'ignore_annotations' => [
                    'mixin',
                ],
            ]
        ];
    }
}
