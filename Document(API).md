# API说明文档
PS：域名（即API前缀）应以宏定义/常量的方式统一管理，便于修改，如：`#define API_WEBSITE http://example.com`

PS2：后续可能API会删除.php后缀，希望届时注意。

## 登录相关
### 登录
```
POST /login.php
```

输入参数：

```
username: 用户名
password: 用户名对应密码(明文即可)
```

输出(JSON格式):

```
{
    status: 200, //返回状态码
    msg: "OK", //状态码描述
    data: {
		token: "xxxxxxxxxxxxxxxxxxxxxxxxxx" //返回Token，仅在status为200时包含此字段
	}
}
```

输出状态码一览：

```
200: 正常，并返回Token
400: 请求方式或传参错误
403: 用户名或密码错误
```

其它说明：

```
返回的Token应当在后续请求中加入到HTTP请求头部的Authorization字段中。
```

### 注册
```
POST /register.php
```

输入参数：

```
username: 用户名(应为字母开头，不含大写字母，4-20字节)
password: 用户名对应密码(应为8-20字节，仅含大小写字母、数字、下划线(_)，明文即可)
```

输出(JSON格式):

```
{
    status: 200, //返回状态码
    msg: "OK", //状态码描述
	data: {
		token: "xxxxxxxxxxxxxxxxxxxxxxxxxx" //返回Token，仅在status为200时包含此字段
	}
}
```

输出状态码一览：

```
200: 注册成功，并返回Token
400: 请求方式或传参错误
403: 用户名已存在
404: 服务器链接错误，请稍后再试
1001: 用户名长度过短
1002: 密码长度过短
1003: 用户名长度过长
1004: 密码长度过长
1005: 用户名格式非法
1006: 密码格式非法
```

其它说明：

```
返回的Token应当在后续请求中加入到HTTP请求头部的Authorization字段中。
```
