#### Hyperf-cache

##### 1.安装
在项目中 `composer.json` 的 `repositories` 项中增加
``` 
{
    ....
    "repositories":{
        "hky/hyperf-crontab":{
            "type":"vcs",
            "url":"http://icode.kaikeba.com/base/hky-packages-hyperf-crontab.git"
        }
        ....
    }
}
```
修改完成后执行 
```bash
$ composer require hky/hyperf-cache
```
如果遇到错误信息为:
`Your configuration does not allow connections to http://icode.kaikeba.com/base/hky-packages-hyperf-http-client.git. See https://getcomposer.org/doc/06-config.md#secure-http for details` 
执行以下命令
```bash
$ composer config secure-http false
```
##### 2.作用
```$xslt
1、给所有crontab加项目名称前缀，防止设置crontab设置锁后不同项目间冲突
2、记录定时任务执行时间，使用的redis数据类型为有序列表，每日的记录默认保留两天
   记录key为 项目名称(app_name):framework:crontab_record:日期
   例子：lps_course_study_progress:framework:crontab_record:2020-06-22
   数据结构：score 为执行的时间戳 value为 (定时任务名称:执行时间)
   例子：1) "lps_course_study_progress_crontab_process_active:2020-06-22 16:01:00"
        2) "1592812860"
```
### 版本改动:
v1.0.0  给所有crontab加项目名称前缀和记录定时执行时间
