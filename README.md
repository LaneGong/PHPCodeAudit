# PHPCodeAudit

声明：只是记录学习的过程，用以复习和互相交流学习，共同提升技术！切勿利用技术进行违法活动，后果自负！！维护国家网络安全是义不容辞的使命，网安人必将砥砺躬行！✊🏻

本次试验使用环境：[PhPstudy](https://www.xp.cn/)

有条件情况下推荐使用Docker搭建，可以跨平台。原先是有一个Docker环境的(docker-nginx-php-mysql环境)，可惜他不兼容arm64，无奈换环境了，当然PhPstudy还是很好用的。

> 源码整理在上述`code`文件夹中

## 语法简要练习GrammarExer.php

```
http://localhost/1GrammarExer.php?name=xxx&age=yy
```

## XSS漏洞

### 复现

开发者没有对用户输入和输出做过滤

```
http://localhost/xss.php?name=<script>alert(1)</script>&age=xx
```

直接执行js脚本了

### 防御

`htmlspecialchars()` 函数将预定义的字符转换为 HTML 实体

## 任意文件上传漏洞

上传文件时未进行文件类型和格式做合法性校验，导致任意文件上传

### 复现

首先在`fileupload.html`文件中写了一个简易表单页面，可以用来选择上传文件，以`POST`提交至`file.php`进行处理，校验文件内容类型，如果不是`image/jpeg`和`image/png`则报错`Invalid file`，如果文件类型正确则上传`upload`文件夹。

上传jpeg和png是没有问题的，此时我上传一个php文件，通过bs抓包发现是不行的。

![](/images/image-20220622214944199.png)

**绕过**

![](/images/image-20220622215144063.png)

成功绕过。

### 防御

核心思想：验证后缀

1. 采取黑名单

   - 将`.php`列为黑名单，问题：通过大写PHP后缀绕过 `fileblacklist1.php`
   - 在上述基础上加一步转换为小写，从而实现防护

   该方法防不完全，我可以上传其他后缀文件...

2. 采取白名单🌟

   该方法相对于前一种，只允许某种后缀通过，防范效果更佳！

## 命令注入漏洞

### 复现

以自带命令做演示...

- system()
- exec()
- shell_exec()
- ......

查看当前文件夹目录

```
http://localhost/command.php?file=.
```

执行的命令：`ls .`

命令注入

```
http://localhost/command.php?file=.%20%26%26%20echo 111 %3E testcommand.txt
```

%20%26%26%20：URL编码➡️ ` && ` 

执行的命令：`ls . && echo 111 > testcommand.txt`

### 防御

`escapeshellarg()`函数对参数进行转义

## 代码执行漏洞

### 复现

`eval()` 函数把字符串按照 PHP 代码来计算

```
http://localhost/runcodetest.php?cmd=phpinfo();
```

`create_function()`

```
http://localhost/runcodetest.php?cmd=phpinfo();
```

## 任意文件读取漏洞

### 复现

在文件下载操作中，文件名及路径由客户端传入的参数控制，并且未进行有效的过滤，导致用户可恶意下载任意文 件。

> file_get_contents() 把整个文件读入一个字符串中。该函数是用于把文件的内容读入到一个字符串中的首选方法。

```
http://localhost/filedownload.php?file=../../../../../../etc/passwd
```

由于没有对参数过滤，可以通过`../`形式进行跳转，从而读到敏感文件。

## 任意文件包含漏洞

### 复现

```
http://localhost/fileinclude.php?file=testfileinclude.php
```

和任意文件读取的差别在于：

- 文件读取：不执行代码
- 文件包含：造成代码执行，如果不是代码，则会对文件内容输出（⚠️与后缀无关）

## 反序列化漏洞

体会序列化与反序列化：

```
http://localhost/serialize.php
http://localhost/unserialize.php
```

### [魔术方法](https://www.php.net/manual/zh/language.oop5.magic.php)

魔术方法是一种特殊的方法，当对对象执行某些操作时会覆盖 PHP 的默认操作。

|      函数      | 说明                                                         |
| :------------: | :----------------------------------------------------------- |
| \__construct() | 当对象创建(new)时会自动调用。但在unserialize()时是不会自动调用的。 |
| \__destruct()  | 当对象被销毁时会自动调用。                                   |
|   __wakeup()   | unserialize()函数执行时会自动调用。                          |
|  __toString()  | 当反序列化后的对象被输出的时候(转化为字符串的时候)被调用     |
|   __sleep()    | serialize()函数执行时先被调用                                |

`unser1.php`

> 既然会自动调用，是否能做点有意思的事情？

### 漏洞利用

#### 传入序列化后的payload

`unser2.php`通过反序列化时自动调用向`shell.php`写入内容,require类似进行文件包含操作。

```
http://localhost/unser2.php?test=O:4:%22test%22:1:{s:4:%22test%22;s:3:%22123%22;}
```

`unser3.php`生成payload：创建一个对象，对象包含恶意代码，进行序列化操作，获得序列化字符串即payload

Payload:`O:4:"test":1:{s:4:"test";s:25:"<?php system('whoami') ?>";}`

调用`unser2.php`，传入payload，执行反序列化操作

```
http://localhost/unser2.php?test=O:4:%22test%22:1:{s:4:%22test%22;s:25:%22%3C?php%20system(%27whoami%27)%20?%3E%22;}
```

#### 没法传入payload的情况

`unser_phar.php`没有调用`unserialize()`

[**PHar反序列化**](https://www.php.net/manual/zh/intro.phar.php)

在文件操作函数（file_exists()、is_dir()等）参数可控的情况下，配合phar://伪协议，可以不依赖unserialize()直接进行反序列化操作。

phar的本质是一种压缩文件，其中每个被压缩文件的权限、属性等信息都放在这部分。这部分还会以序列化的形式存储用户自定义的meta-data。

**漏洞利用**

本质上则是将payload写入了phar文件的meta-data部分，读取文件内容时相当于传入了phar存储的序列化内容，将会直接执行反序列化操作，从而调取__wakeup()方法，配合require实现文件包含漏洞。

1. 生成phar文件，php文件`PharExploit.php`，网站跑一下得到phar文件

   > ⚠️要将php.ini中的phar.readonly选项设置为Off，重启
   
   ```ini
   [Phar]
   ; http://php.net/phar.readonly
   phar.readonly = Off
   ```

2. 将生成的phar.phar文件放到网站目录，进行EXP

   ```
   http://localhost/unser_phar.php?filename=phar://phar.phar
   ```


## SQL注入漏洞

> 此漏洞借助PHPstudy复现有点小困难，需要编辑一下数据库...

### 复现

源码`sql1.php`

SQL参数拼接，未作任何过滤，因此无需要正确密码，通过SQL注入读取所有数据。

> 原则上用户名和密码必须全对才能从数据库中选出符合条件的数据

利用1: 读取用户名为admin，密码随便写的账户信息

payload：`http://localhost/sql1.php?username=admin%27%23&password=xxx`

`%27`等价于`'`,`%23`等价于`#`

等价的sql语句结果为

```sql
select * from users where user='admin'#' and password='9336ebf25087d91c818ee6e9ec29f8c1'
```

#代表注释即只有前半句username是有效的。

利用2: 获取所有用户信息

payload：`http://localhost/sql1.php?username=%27%20or%20%271%27=%271%27%23&password=xx`

等价的sql语句结果为

```sql
select * from users where user='' or '1'='1'#' and password='9336ebf25087d91c818ee6e9ec29f8c1'
```

### 防御

预编译方法防范sql注入漏洞`sql2.php`

> PDO预处理:https://book.itheima.net/course/1258677827423715330/1265953996862119937/1277478421638684675
>
> 预处理SQL语句中的参数占位符是由冒号“:”和标识符组成，在为参数占位符绑定数据时，只要保证数组中元素的“键名”与参数占位符的“标识符”相同即可，键名中的冒号可以省略。

```php
#关键代码
$sql='select * from users where user=:username and password=:password';
$stmt=$pdo->prepare($sql);
$stmt->execute(array(":username"=>$username,":password"=>$password));
```

通过占位符为什么能防止SQL注入？

[SQL注入&预编译](https://forum.butian.net/share/1559)

我的简单理解：前面是直接拼接，相当于传进来的参数和初始语句组合出了一条完整的SQL命令。而通过占位符原始语句全都提前编译好了，最终比对的只有变量部分，变量传的是什么则查询时数据库比对的就是什么。如`username=%27%20or%20%271%27=%271%27%23`，则数据库查询时是拿`%27%20or%20%271%27=%271%27%23`去和数据库中的user比对，有就返回，没有就不存在。因此预编译加占位符能有效防止SQL注入！
