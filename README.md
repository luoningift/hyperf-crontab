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
给所有crontab加项目名称前缀，防止设置crontab设置锁后不同项目间冲突
```
### 版本改动:
v1.0.0  给所有crontab加项目名称前缀
