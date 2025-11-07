# 🌟 星星存折 - 儿童奖励管理系统

[![Docker Build](https://github.com/zhy0504/star-savings/actions/workflows/docker-build.yml/badge.svg)](https://github.com/zhy0504/star-savings/actions/workflows/docker-build.yml)
[![GitHub Container Registry](https://img.shields.io/badge/GHCR-latest-blue?logo=github)](https://github.com/zhy0504/star-savings/pkgs/container/star-savings)
[![License](https://img.shields.io/github/license/zhy0504/star-savings)](LICENSE)

一个基于 Vue.js + Laravel 的现代化儿童星星奖励管理系统，使用 Docker 容器化部署。帮助家长用有趣的方式管理孩子的日常行为和奖励制度。

> 🎯 **适合年龄**：3-12岁儿童的家庭使用
> 🚀 **部署方式**：Docker 一键部署
> 🔒 **安全性**：nginx 身份验证 + HTTPS 支持

## ✨ 核心特性

### 🎨 现代化用户界面
- **响应式设计** - 完美适配手机、平板、电脑
- **直观操作** - 孩子也能轻松使用
- **精美动画** - 流畅的交互体验
- **实时更新** - 加减星记录实时展示

### 🌟 智能奖励系统
- **灵活配置** - 每个理由可设置默认星星数量（如：做家务 +3⭐，发脾气 -2⭐）
- **快速操作** - 点击理由标签自动填充星星数量
- **完整记录** - 首页展示最近的加减星明细
- **多种类型** - 加星星、减星星、兑换奖品

### 🛡️ 企业级安全
- **身份验证** - HTTP 基本认证保护
- **数据加密** - 安全的数据传输
- **智能认证** - API 接口无需认证，前端调用更流畅

### 🐳 容器化部署
- **Docker 支持** - 一键部署到任何平台
- **自动初始化** - 数据库自动创建和迁移
- **健康检查** - 实时监控服务状态

## 🚀 快速开始

### 方式一：使用 Docker Compose（推荐）

```bash
# 1. 克隆项目
git clone https://github.com/zhy0504/star-savings.git
cd star-savings

# 2. 启动服务（自动从 GitHub Container Registry 拉取镜像）
docker compose up -d

# 3. 访问应用
# 浏览器打开: http://localhost:8080
# 默认认证: 用户名 admin，密码 star123
```

### 方式二：服务器部署（完整步骤）

```bash
# 1. 创建部署目录
mkdir -p /root/docker/star
cd /root/docker/star

# 2. 下载配置文件
wget https://raw.githubusercontent.com/zhy0504/star-savings/main/docker-compose.yml
wget https://raw.githubusercontent.com/zhy0504/star-savings/main/nginx.conf
wget https://raw.githubusercontent.com/zhy0504/star-savings/main/.htpasswd
wget https://raw.githubusercontent.com/zhy0504/star-savings/main/deploy-setup.sh

# 3. 运行初始化脚本
chmod +x deploy-setup.sh
./deploy-setup.sh

# 4. 启动服务
docker compose up -d

# 5. 查看日志确认启动成功
docker compose logs -f backend

# 应该看到：
# ✓ .env file created
# ✓ Migrations completed  ← 数据库初始化成功
# ✓ APP_KEY generated
# ✓ Optimization completed
```

### 验证部署

```bash
# 检查容器状态
docker compose ps

# 测试 API 接口
curl http://localhost:8080/api/health

# 应该返回：
# {"status":"ok","timestamp":"...","service":"Star Savings API"}
```

## 📋 功能演示

### 🏠 主页面
- 📱 查看所有小朋友的星星情况
- ➕ 快速添加新的小朋友
- 🎁 一键进入奖品兑换界面
- 📋 **最近记录** - 显示最近的加减星明细

### 👶 小朋友管理
- 📝 添加/编辑小朋友信息
- 🎂 自动计算年龄
- 👤 自定义头像上传
- ⭐ 实时星星数量显示

### ⭐ 星星操作（新功能）
- ➕ **加星星** - 点击理由标签自动设置星星数量
  - 😊 认真 (1⭐)
  - 🏃 主动 (1⭐)
  - 😴 按时 (1⭐)
  - 🤝 分享 (2⭐)
  - 🧹 做家务 (3⭐)
- ➖ **减星星** - 智能扣除星星
  - 😢 不听话 (1⭐)
  - 🎮 玩太久 (1⭐)
  - 😴 不按时 (1⭐)
  - 😤 发脾气 (2⭐)
- 📝 **原因记录** - 详细记录每次操作的原因
- 🔄 **实时更新** - 操作后立即刷新明细

### 🎁 奖品系统
- 🏪 奖品商店管理
- 💰 星星兑换设置
- 👥 参与小朋友分配
- 📊 兑换进度跟踪
- 🎉 兑换动画效果

### ⚙️ 系统设置（新功能）
- 🔢 **加星星上限** - 自定义每次加星星的最大数量
- ➕ **加星星理由** - 自定义理由和对应的星星数量
- ➖ **减星星理由** - 自定义理由和对应的星星数量

## 🛠️ 技术架构

### 🎨 前端技术栈
- **Vue.js 3.5+** - 现代化前端框架
- **TypeScript** - 类型安全的 JavaScript
- **Vite 7.1+** - 快速构建工具
- **Vue Router 4.6+** - 单页应用路由
- **Axios** - HTTP 客户端

### ⚙️ 后端技术栈
- **Laravel 12** - PHP Web 框架
- **PHP 8.2+** - 编程语言
- **SQLite** - 轻量级数据库
- **PHP-FPM** - PHP FastCGI 进程管理器

### 🐳 基础设施
- **Docker** - 容器化平台
- **Docker Compose** - 多容器编排
- **Nginx** - Web 服务器/反向代理
- **GitHub Actions** - CI/CD 自动化
- **GitHub Container Registry** - 镜像仓库

## 📁 项目结构

```
star-savings/
├── 📁 frontend/                 # Vue.js 前端应用
│   ├── 📁 src/
│   │   ├── 📁 api/             # API 接口封装
│   │   ├── 📁 components/      # Vue 组件
│   │   │   ├── ChildCard.vue
│   │   │   ├── StarModal.vue
│   │   │   └── RecentStarRecords.vue  # 加减星明细组件
│   │   ├── 📁 views/           # 页面组件
│   │   │   ├── HomePage.vue
│   │   │   ├── SettingsView.vue       # 系统设置页面
│   │   │   └── ChildDetail.vue
│   │   └── 📁 types/           # TypeScript 类型定义
│   └── 📄 package.json
├── 📁 backend/                  # Laravel 后端应用
│   ├── 📁 app/
│   │   ├── 📁 Http/Controllers/
│   │   │   ├── ChildController.php
│   │   │   ├── StarController.php
│   │   │   ├── RewardController.php
│   │   │   └── SettingController.php
│   │   └── 📁 Models/
│   ├── 📁 database/
│   │   └── 📁 migrations/
│   │       └── 2025_01_01_000000_create_initial_tables.php  # 整合后的迁移文件
│   └── 📁 routes/
│       └── api.php
├── 📄 docker-compose.yml        # Docker 编排配置
├── 📄 Dockerfile               # Docker 镜像构建
├── 📄 docker-entrypoint.sh     # 容器启动脚本
├── 📄 deploy-setup.sh          # 部署初始化脚本
├── 📄 nginx.conf               # Nginx 配置
├── 📄 .htpasswd                # HTTP 基本认证密码文件
└── 📄 README.md                # 项目文档
```

## 📊 API 接口文档

### 👶 小朋友管理
```http
GET    /api/children              # 获取所有小朋友
GET    /api/children/{id}         # 获取单个小朋友详情
POST   /api/children              # 创建新小朋友
PUT    /api/children/{id}         # 更新小朋友信息
DELETE /api/children/{id}         # 删除小朋友
```

### ⭐ 星星操作
```http
GET    /api/stars/recent          # 获取最近的星星记录（新增）
POST   /api/children/{id}/stars/add      # 加星星
POST   /api/children/{id}/stars/subtract # 减星星
```

### 🎁 奖品管理
```http
GET    /api/rewards               # 获取所有奖品
POST   /api/rewards               # 创建新奖品
PUT    /api/rewards/{id}          # 更新奖品信息
DELETE /api/rewards/{id}          # 删除奖品
POST   /api/rewards/{id}/redeem   # 兑换奖品
```

### ⚙️ 系统设置（新增）
```http
GET    /api/settings              # 获取所有设置
GET    /api/settings/{key}        # 获取单个设置
PUT    /api/settings/{key}        # 更新设置
```

### 🔍 健康检查
```http
GET    /api/health                # 服务健康状态
```

## 🔒 安全配置

### 🔐 默认认证信息

```
用户名: admin
密码: star123
```

⚠️ **重要**：首次部署后请立即修改默认密码！

### 🔧 修改认证密码

#### 方法 1：使用在线工具（推荐）

1. 访问：https://www.web2generators.com/apache-tools/htpasswd-generator
2. 输入用户名：`admin`
3. 输入新密码（建议至少 12 位，包含大小写字母、数字和特殊字符）
4. 选择加密方式：`MD5 (Apache specific)`
5. 复制生成的哈希值
6. 更新 `.htpasswd` 文件：
   ```bash
   echo "admin:生成的哈希值" > .htpasswd
   ```
7. 重启 nginx 容器：
   ```bash
   docker compose restart nginx
   ```

#### 方法 2：使用 htpasswd 命令

```bash
# 安装 htpasswd 工具
# Ubuntu/Debian:
sudo apt-get install apache2-utils

# CentOS/RHEL:
sudo yum install httpd-tools

# 生成新密码
htpasswd -c .htpasswd admin

# 重启 nginx
docker compose restart nginx
```

### 🛡️ 智能认证模式

项目默认使用智能认证配置：
- ✅ **主页面需要密码** - 保护用户界面
- ✅ **API 接口无需认证** - 前端调用正常工作
- ✅ **静态资源无需认证** - 提升性能
- ✅ **存储文件需要密码** - 保护上传文件

## 🗄️ 数据库说明

### 数据库类型
- **SQLite** - 轻量级、零配置、文件型数据库
- **位置**：`backend/storage/app/database.sqlite`

### 数据表结构

```sql
-- 孩子信息表
children (id, name, birthday, gender, avatar, star_count, created_at, updated_at)

-- 奖励表
rewards (id, name, image, star_cost, is_redeemed, redeemed_at, created_at, updated_at)

-- 奖励-孩子关联表
reward_children (id, reward_id, child_id, deduction_amount, created_at, updated_at)

-- 星星记录表
star_records (id, child_id, amount, type, reward_id, reason, created_at, updated_at)

-- 系统设置表
settings (id, key, value, type, description, created_at, updated_at)
```

### 数据库迁移

**首次部署时**，容器会自动执行数据库迁移：

```bash
# 容器启动时自动运行
php artisan migrate --force

# 这会：
✓ 创建所有数据表
✓ 插入默认配置数据
✓ 完成数据库初始化
```

**默认配置数据**：
- 加星星上限：100 颗
- 加星星理由：认真(1⭐)、主动(1⭐)、按时(1⭐)、分享(2⭐)、做家务(3⭐)
- 减星星理由：不听话(1⭐)、玩太久(1⭐)、不按时(1⭐)、发脾气(2⭐)

## 🔧 常见问题

### 问题 1：容器启动失败

```bash
# 查看详细日志
docker compose logs backend

# 常见原因：
# 1. 数据库文件权限问题
# 2. 端口被占用
# 3. 镜像拉取失败
```

**解决方法**：
```bash
# 重新运行初始化脚本
./deploy-setup.sh

# 重启容器
docker compose restart
```

### 问题 2：数据库迁移失败

```bash
# 查看迁移日志
docker compose logs backend | grep -A 10 "Running database migrations"

# 手动运行迁移
docker compose exec backend php artisan migrate --force
```

### 问题 3：无法访问应用

```bash
# 检查端口是否被占用
netstat -tlnp | grep 8080

# 检查防火墙
sudo ufw status
sudo ufw allow 8080

# 检查 Nginx 配置
docker compose exec nginx nginx -t
```

### 问题 4：数据库文件不存在

```bash
# 检查数据库文件
ls -la backend/storage/app/database.sqlite

# 如果不存在，重新创建
touch backend/storage/app/database.sqlite
chown 33:33 backend/storage/app/database.sqlite
chmod 664 backend/storage/app/database.sqlite

# 重启容器
docker compose restart backend
```

### 问题 5：权限错误

```bash
# 确保 storage 目录权限正确（www-data UID=33）
chown -R 33:33 backend/storage
chmod -R 775 backend/storage
```

## 📦 数据备份

### 备份数据库

```bash
# 备份 SQLite 数据库
cp backend/storage/app/database.sqlite \
   backup/database-$(date +%Y%m%d-%H%M%S).sqlite

# 或者使用 Docker 命令
docker compose exec backend sqlite3 /var/www/html/storage/app/database.sqlite .dump > backup.sql
```

### 恢复数据库

```bash
# 停止服务
docker compose down

# 恢复数据库文件
cp backup/database-20250107-120000.sqlite \
   backend/storage/app/database.sqlite

# 启动服务
docker compose up -d
```

### 定时备份

```bash
# 创建定时备份任务
crontab -e

# 添加每天凌晨 2 点备份
0 2 * * * cp /root/docker/star/backend/storage/app/database.sqlite /root/docker/star/backup/database-$(date +\%Y\%m\%d).sqlite
```

## 🔄 更新部署

当有新版本时：

```bash
cd /root/docker/star

# 拉取最新镜像
docker compose pull

# 重启服务
docker compose up -d

# 查看日志确认更新成功
docker compose logs -f backend
```

**注意**：更新时会自动运行新的数据库迁移，不会丢失现有数据！

## 📝 开发指南

### 本地开发环境

#### 前端开发
```bash
cd frontend
npm install
npm run dev
# 访问: http://localhost:5174
```

#### 后端开发
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
# API 访问: http://localhost:8000/api
```

### 添加新功能

如果要添加新功能（如添加新的数据表）：

```bash
# 1. 创建新的迁移文件
cd backend
php artisan make:migration create_tasks_table

# 2. 编辑迁移文件
# backend/database/migrations/2025_xx_xx_xxxxxx_create_tasks_table.php

# 3. 运行迁移
php artisan migrate

# 4. 用户更新时会自动运行新的迁移
```

## 📋 路线图

### ✅ 已完成（v1.0）
- ✅ 基础的星星管理功能
- ✅ 奖品兑换系统
- ✅ Docker 容器化部署
- ✅ HTTP 基本认证
- ✅ **理由配置星星数量**
- ✅ **首页展示加减星明细**
- ✅ **系统设置功能**

### 🎯 短期目标（v1.1）
- [ ] 🌙 添加暗黑模式
- [ ] 📊 数据导出功能
- [ ] 🎨 自定义主题
- [ ] 📧 邮件通知功能

### 🚀 中期目标（v2.0）
- [ ] 👨‍👩‍👧‍👦 多家庭支持
- [ ] 🤖 AI 智能推荐奖励
- [ ] 📈 高级数据分析
- [ ] 🌐 多语言支持

## 🤝 贡献指南

我们欢迎所有形式的贡献！

### 🚀 快速贡献
1. Fork 项目
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 创建 Pull Request

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情。

## 🙏 致谢

感谢以下开源项目和服务：

- [Vue.js](https://vuejs.org/) - 现代化前端框架
- [Laravel](https://laravel.com/) - 优雅的 PHP Web 框架
- [Docker](https://www.docker.com/) - 容器化平台
- [GitHub](https://github.com/) - 代码托管和 CI/CD

---

<div align="center">

**🌟 如果这个项目对您有帮助，请给我们一个 Star！**

Made with ❤️ by [zhy0504](https://github.com/zhy0504)

</div>
