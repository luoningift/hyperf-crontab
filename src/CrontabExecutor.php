<?php
declare(strict_types=1);

namespace HKY\HyperfCrontab;

use Closure;
use Hyperf\Crontab\Crontab;
use Hyperf\Crontab\Strategy\Executor;

class CrontabExecutor extends Executor
{

    protected function decorateRunnable(Crontab $crontab, Closure $runnable): Closure
    {
        if ($crontab->isSingleton()) {
            $runnable = $this->runInSingleton($crontab, $runnable);
        }

        if ($crontab->isOnOneServer()) {
            $runnable = $this->runOnOneServer($crontab, $runnable);
        }
        return function () use ($crontab, $runnable) {
            $runnable();
            $this->container->get(CrontabRecordRelyRedis::class)->add($crontab);
        };
    }

    protected function runOnOneServer(Crontab $crontab, Closure $runnable): Closure
    {
        return function () use ($crontab, $runnable) {
            $taskMutex = $this->getServerMutex();

            if (!$taskMutex->attempt($crontab)) {
                $this->logger->info(sprintf('Crontab task [%s] skipped execution at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
                return;
            }

            $runnable();
            $this->container->get(CrontabRecordRelyRedis::class)->add($crontab);
        };
    }

    protected function runInSingleton(Crontab $crontab, Closure $runnable): Closure
    {
        return function () use ($crontab, $runnable) {
            $taskMutex = $this->getTaskMutex();

            if ($taskMutex->exists($crontab) || !$taskMutex->create($crontab)) {
                $this->logger->info(sprintf('Crontab task [%s] skipped execution at %s.', $crontab->getName(), date('Y-m-d H:i:s')));
                return;
            }

            try {
                $runnable();
            } finally {
                $taskMutex->remove($crontab);
            }
            $this->container->get(CrontabRecordRelyRedis::class)->add($crontab);
        };
    }
}