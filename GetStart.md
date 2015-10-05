多年来一直在做PHP的开发，积累的大量经验。好多的编程思想都融入到了这个框架。
共包含1个文件夹和2个文件。
index.php  //示例，运行mvc
config.php //配置文件

最简单的例子
1、配置config.php，数据库用户名及密码 自动load的项，默认的是
$php->loadlibs('db,tpl,cache'); //加载了数据库、模板系统和缓存系统。
$php->loadConfig();  //加载了系统配置器

2、编写你的程序
```

<?php
require('config.php');  //只要包含了config.php，就可以通过使用$php->来调用任意的调用框架的功能和类库了

$res = $php->db->query('show tables');  //数据库查询
$data = $res->fetchall(); //获取数据

$php->tpl->assign('title','hello world'); //模板赋值
$php->tpl->display('index.html');  //模板渲染

$php->cache->set('my_key','value',3600);
echo $php->cache->get('my_key');
?>
```