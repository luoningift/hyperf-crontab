<?php
declare(strict_types=1);

namespace HKY\HyperfCrontab;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Crontab;
use Hyperf\Crontab\LoggerInterface;
use Hyperf\Redis\RedisFactory;
use Psr\Container\ContainerInterface;

/**
 * 记录定时任务是否执行
 * Class CrontabRecordRelyRedis
 * @package HKY\HyperfCrontab
 */
class CrontabRecordRelyRedis
{

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var RedisFactory
     */
    private $redisFactory;

    /**
     * @var null|\Hyperf\Crontab\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Hyperf\Contract\ConfigInterface
     */
    private $config;

    public function __construct(ContainerInterface $container, RedisFactory $redisFactory, ConfigInterface $config)
    {
        $this->container = $container;
        if ($container->has(LoggerInterface::class)) {
            $this->logger = $container->get(LoggerInterface::class);
        } elseif ($container->has(StdoutLoggerInterface::class)) {
            $this->logger = $container->get(StdoutLoggerInterface::class);
        }
        $this->redisFactory = $redisFactory;
        $this->config = $config;
    }

    /**
     * 记录record信息
     * @param Crontab $crontab
     */
    public function add(Crontab $crontab)
    {

        try {
            $nowTime = time();
            $redis = $this->redisFactory->get($crontab->getMutexPool());
            $key = $this->getRecordKey();
            $redis->zAdd($this->getRecordKey(), strval($nowTime), strval($crontab->getName()) . ':' . date('Y-m-d H:i:s', $nowTime));
            $ttl = $redis->ttl($key);
            if ($ttl !== false && intval($ttl) == -1) {
                $expireSecond = 86400 * 2;
                if (!$redis->expire($key, $expireSecond)) {
                    $redis->expire($key, $expireSecond);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Crontab task [%s] failed record at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Crontab task [%s] failed record at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
        }
    }

    private function getRecordKey()
    {
        $appName = strtolower(str_replace('-', '_', strval($this->config->get('app_name'))));
        return $appName . ':framework:crontab_record:' . date('Y-m-d');
    }
}