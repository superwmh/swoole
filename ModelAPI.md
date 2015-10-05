详细请见Swoole官方网站：
[Swoole Model数据模型的使用](http://swoole.com/News/83.html)
<p>可以使用命令行工具增加一个新的Model。在命令行下，进入网站的libs目录</p>
```
php manage.php addm TestModel```
<p>&nbsp;或者，在apps/models 目录新建一个 TestModel.model.php文件。代码如下</p>
```

<?php
class TestModel extends Model
{
public $table = 'test';
}
```
<p>&nbsp;新的Model必须继承自Model基类。必须有表名才可以使用Model数据库封装。现在在MySQL中新建一个表test.</p>
```

CREATE TABLE wap.test (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
userid INT NOT NULL ,
title VARCHAR( 255 ) NOT NULL ,
content TEXT NOT NULL ,
addtime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ;
```
<h2>&nbsp;代码1：仅使用Model接口的数据库操作方式</h2>
<p>下面演示一段代码，分别的功能是获取一条数据，获取userid为2的所有内容，获取某段时间userid为2的内容，更新数据内容，删除，新增一条数据。</p>
<p>&nbsp;</p>
```

$content = $model->get(1);  //获取数据库的id为1的内容
$gets1['userid'] = 2;           //SQL语句的where条件
$gets1['order'] = 'id desc';    //排序方式
$gets1['limit'] = 10;            //查询10条
$list1 = $model->gets($gets1);

$gets2['where'][] = 'userid=2';
$gets2['where'][] = &quot;addtime>'2009-10-12 00:00:00'&quot;;
$gets2['where'][] = &quot;addtime&lt;'2009-11-12 00:00:00'&quot;;

/* 下面代码可以直接支持分页 */
$gets2['pagesize'] = 10; //每页10条
$gets2['page'] = empty($_GET['page'])?1:(int)$_GET['page']; //当前页数，从GET中获取

$list2 = $model->gets($gets,$pager);  //$pager 是一个任意的变量名，自动会赋值为Pager对象
echo $pager->totalpage;  //总页数
echo $pager->render();   //生成一个上一页，下一页，页码的HTML内容

$set['title'] = 'hello world';
$set['content'] = &quot;你好&quot;;
$model->set(1,$set);   //把ID为1的title,content设置为上面对应的值

$insert['title'] = 'hello world';
$insert['content'] = '你好';
echo $model->put($insert);   //插入一条新的记录，并返回自增的ID，如果没有自增ID，返回为空

$model->del(2);  //删除ID为2的记录
```
<p>&nbsp;</p>
<p>另外，Swoole的Model数据模型对象，还支持复数的操作，批量更新内容用$model->sets接口和批量删除用$model->dels接口</p>
<h2>代码2：ORM数据操作方式</h2>
```

$content = $model->get(1);   //这里返回的是一个Record对象
$content->title = 'hello world';  //Update操作
$content->save();                 //保存操作，这时会执行SQL语句
echo $content->addtime;           //输入值
$content->delete();                 //删除此条数据

$all = $model->all();        //这里返回一个RecordSet对象
$all->filter('userid=2');    //增加一些限定条件，避免读取全部的数据库内容

//遍历符合条件的所有数据库记录
foreach($all as $obj)
{
$obj->title = 'hello';
echo $obj->content;
}
```