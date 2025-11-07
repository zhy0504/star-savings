# 星星存折项目 - 身份验证配置

## 🔐 认证配置说明

### 当前认证方式
- **类型**: HTTP基本认证 (Basic Authentication)
- **用户名**: `admin`
- **密码**: `star123`

### 当前认证方式（智能认证）

项目默认使用智能认证配置：
- ✅ **主页面需要密码** - 保护用户界面
- ✅ **API接口无需认证** - 前端调用正常工作
- ✅ **静态资源无需认证** - 提升性能
- ✅ **存储文件需要密码** - 保护上传文件

### 完全认证（备用方案）

如果需要所有访问都需要认证：
```bash
# 备份当前配置
cp nginx.conf nginx-smart-auth.conf

# 使用完全认证配置
cp nginx-full-auth.backup.conf nginx.conf

# 重启nginx容器
docker compose restart nginx
```

### 修改用户名和密码

#### 方法1: 在线生成
1. 访问：https://www.web2generators.com/apache-tools/htpasswd-generator
2. 输入用户名和密码
3. 复制生成的密码
4. 更新 `.htpasswd` 文件

#### 方法2: 使用htpasswd命令
```bash
# 安装htpasswd工具（如果还没有）
# Ubuntu/Debian: sudo apt-get install apache2-utils
# CentOS/RHEL: sudo yum install httpd-tools

# 生成新的密码文件
htpasswd -c .htpasswd admin

# 添加更多用户
htpasswd .htpasswd user2
```

### 密码文件格式

`.htpasswd` 文件示例：
```
# 用户名:加密后的密码
admin:$apr1$rY6v8Wq9$JzK8V3F7hP2L6Q9XwR4bZ0
user2:$apr1$aB3xYz9$pL8Q7R6W9X2N5V4F3K1Z0
```

### 认证效果

#### 访问受保护的网站时：
1. 浏览器弹出登录框
2. 输入用户名：`admin`
3. 输入密码：`star123`
4. 点击确定即可访问

#### API接口调用：
- **智能认证模式**: `/api/*` 接口无需认证，方便前端调用
- **完全认证模式**: 所有请求都需要认证

### 安全建议

1. **使用强密码**：不要使用简单的密码
2. **定期更换密码**：定期更新认证密码
3. **HTTPS加密**：生产环境建议使用HTTPS
4. **限制访问**：结合IP白名单使用

### 禁用认证

如果不需要认证，可以：
```bash
# 备份当前配置
cp nginx.conf nginx-auth-enabled.conf

# 使用无认证版本
# 删除nginx.conf中的认证配置行
# auth_basic "..."
# auth_basic_user_file /etc/nginx/.htpasswd;

# 重启容器
docker compose restart nginx
```

### 故障排除

#### 密码错误
```
401 Unauthorized
```
检查：
- 用户名和密码是否正确
- `.htpasswd` 文件格式是否正确
- nginx是否重新加载了配置

#### 配置文件错误
```
nginx: [emerg] invalid path in "/etc/nginx/.htpasswd"
```
检查：
- `.htpasswd` 文件是否存在
- docker-compose.yml的volume挂载是否正确
- 文件权限是否正确

#### 测试认证
```bash
# 测试基本认证
curl -u admin:star123 http://localhost:8080

# 测试API访问（智能认证模式）
curl http://localhost:8080/api/health
```

### 自定义认证配置

你可以创建不同的认证配置文件：

1. **开发环境** - 无认证
2. **测试环境** - 简单认证
3. **生产环境** - 严格认证 + HTTPS + IP限制

```bash
# 创建不同的nginx配置
cp nginx.conf nginx-dev.conf      # 开发环境（无认证）
cp nginx.conf nginx-test.conf     # 测试环境（简单认证）
cp nginx.conf nginx-prod.conf     # 生产环境（严格认证）

# 根据环境使用不同配置
cp nginx-dev.conf nginx.conf && docker compose restart nginx
```