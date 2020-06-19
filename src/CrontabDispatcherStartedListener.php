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

use Hyperf\Contract\ConfigInterface;
use Hyperf\Crontab\Crontab;
use Hyperf\Crontab\CrontabManager;
use Hyperf\Crontab\Event\CrontabDispatcherStarted;
use Hyperf\Event\Contract\ListenerInterface;

class CrontabDispatcherStartedListener implements ListenerInterface
{

    /**
     * @var \Hyperf\Crontab\CrontabManager
     */
    protected $crontabManager;

    /**
     * @var \Hyperf\Contract\ConfigInterface
     */
    private $config;


    public function __construct(CrontabManager $crontabManager, ConfigInterface $config)
    {
        $this->crontabManager = $crontabManager;
        $this->config = $config;
    }

    public function listen(): array
    {
        return [
            CrontabDispatcherStarted::class,
        ];
    }

    public function process(object $event)
    {
        $appName = strtolower(str_replace('-', '_', strval($this->config->get('app_name')))) . '_';
        //获取所有的crontab
        $crontabs = $this->crontabManager->getCrontabs();
        /** @var $crontab Crontab */
        foreach($crontabs as $k => $crontab) {
            //所有的crontab增加项目名称前缀 防止项目间冲突
            $crontab->setName($appName . $crontab->getName());
        }
    }
}
