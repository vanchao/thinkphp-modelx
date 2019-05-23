这是一个在Thinkphp5.1.12版本测试过的Model命令行生成代码
当你使用Model Object，尤其是在phpstorm下进行开发的时候，
是不是很苦恼？像Yii框架Gii生成的model都有数据表字段属性
提示，ThinkPHP却没有，尤其是多个字段的数据表，开发起来，
有那么一丢丢不爽。好了，这个命令行插件，就是为了解决这个
小小的问题。

如何使用？

将代码克隆下来，或者以zip包形式下载下来(记得解压缩)，把
common文件夹放到application下，然后在application文件夹下
找到command.php，在配置数组里添加如下代码：
'app\common\command\make\Modelx',

来到项目根目录，输入php think list，如果能看到如下字样：
make:modelx  Create a new model class with property doc
OK，恭喜你，安装成功。

接下来就是如何使用它，比如说你数据库有那么一个表名为test
的表，那么请输入：
php think make:modelx index/Test
去index模块的model文件夹下，看看发生了什么？
如果你有一张表名为user_content的数据表，那么
1、你的config/database.php配置了prefix参数：
php think make:modelx index/Content
2、你的database.php没有配置prefix参数：
php think make:modelx index/UserContent

好了，作者很懒，懒得再往下进行流水账式的叙述了。具体内容，
看源代码，至于想怎么修改，改成啥样，就看各位看官的心情了，
英文烂，就不多写什么了，反正老外用TP框架的基本上也没几个。
觉得这个东东还可以的话，请看官来个star ^_^V。

