### easyswoole_api 从传统php到swoole的过渡api接口

1. #### 如何运行

   - 请确定easyswoole官网demo可以正常运行
   - git clone https://github.com/2125zyrc/easyswoole_api.git
   - cd easyswoole_api
   - php vendor/easyswoole/easyswoole/bin/easyswoole install && 选择N
   - php easyswoole start
   - 打开浏览器测试

1. #### 小的知识点（~~一点小的心得~~）

   - 封装统一异常类  使用`instanceof`方法判断是否继承自定义异常类
	- 使用`trait`封装`LoginMiddleware`类  结合jwt实现auth中间件判断用户登录权限并且减少控制器冗余逻辑使代码更加简洁，容易维护
   - 使用`abstract`、`final`、`call_user_func`以及php7语法封装低耦合的配置文件类库
   - ...
3. #### 运用知识点

   - 使用jwt验证权限
   - 使用orm操作数据库
   - 使用协程操作redis
   - ...

空闲时间会持续更新代码！！！如果对您有帮助，请记得star！！！




