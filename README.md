# 🌟 星星存折 - 儿童奖励管理系统

[![Docker Build](https://github.com/zhy0504/star-savings/workflows/%F0%9D%20Docker%20Build%20%26%20Deploy/badge.svg)](https://github.com/YOUR_USERNAME/star-savings/actions)
[![Security Scan](https://github.com/zhy0504/star-savings/workflows/%F0%9D%20Security%20Scan/badge.svg)](https://github.com/zhy0504/star-savings/actions)
[![GitHub Container Registry](https://img.shields.io/github/v/release/YOUR_USERNAME/star-savings?label=GHCR&logo=github)](https://github.com/zhy0504/star-savings/pkgs/container/star-savings)
[![License](https://img.shields.io/github/license/zhy0504/star-savings)](LICENSE)

一个基于Vue.js + Laravel的现代化儿童星星奖励管理系统，使用GitHub Container Registry容器化部署。帮助家长用有趣的方式管理孩子的日常行为和奖励制度。

> 🎯 适合年龄：3-12岁儿童的家庭使用
> 🚀 部署方式：Docker一键部署
> 🔒 安全性：nginx身份验证 + HTTPS支持

## ✨ 核心特性

### 🎨 现代化用户界面
- **响应式设计** - 完美适配手机、平板、电脑
- **直观操作** - 孩子也能轻松使用
- **精美动画** - 流畅的交互体验
- **暗黑模式** - 护眼模式（计划中）

### 🌟 智能奖励系统
- **多种奖励类型** - 加星星、减星星、兑换奖品
- **自定义规则** - 灵活设置奖励标准
- **历史记录** - 完整的操作记录
- **数据统计** - 可视化的成长曲线

### 🛡️ 企业级安全
- **身份验证** - HTTP基本认证保护
- **数据加密** - 安全的数据传输
- **权限控制** - 细粒度的操作权限
- **安全扫描** - 自动化安全检测

### 🐳 容器化部署
- **Docker支持** - 一键部署到任何平台
- **多平台** - 支持linux/amd64和linux/arm64
- **自动化CI/CD** - GitHub Actions自动构建
- **健康检查** - 实时监控服务状态

## 🚀 快速开始

### 🐳 Docker部署（推荐）

```bash
# 1. 克隆项目
git clone https://github.com/YOUR_USERNAME/star-savings.git
cd star-savings

# 2. 启动服务
docker compose up -d

# 3. 访问应用
# 浏览器打开: http://localhost:8080
# 默认认证: 用户名 admin，密码 star123
```

### 🎨 本地开发环境

#### 前端开发：
```bash
cd frontend
npm install
npm run dev
# 访问: http://localhost:5174
```

#### 后端开发：
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
# API访问: http://localhost:8000/api
```

### 🔧 手动配置部署

```bash
# 1. 构建前端
cd frontend && npm run build

# 2. 配置后端
cd ../backend
composer install --no-dev
php artisan key:generate
php artisan migrate --force
php artisan config:cache

# 3. 配置nginx
# 复制nginx.conf到nginx配置目录
# 修改docker-compose.yml中的镜像地址
```

## 📋 功能演示

### 🏠 主页面
- 📱 查看所有小朋友的星星情况
- ➕ 快速添加新的小朋友
- 🎁 一键进入奖品兑换界面

### 👶 小朋友管理
- 📝 添加/编辑小朋友信息
- 🎂 自动计算年龄
- 👤 自定义头像上传
- ⭐ 实时星星数量显示

### ⭐ 星星操作
- ➕ **加星星** - 表扬奖励（认真、主动、按时、分享）
- ➖ **减星星** - 需要改进（不听话、玩太久、发脾气）
- 📝 **原因记录** - 详细记录每次操作的原因
- 🔄 **批量操作** - 支持多个小朋友同时操作

### 🎁 奖品系统
- 🏪 奖品商店管理
- 💰 星星兑换设置
- 👥 参与小朋友分配
- 📊 兑换进度跟踪
- 🎉 兑换动画效果

### 📊 数据统计
- 📈 星星增长趋势
- 🏆 奖品兑换记录
- 📅 日常行为分析
- 📋 导出数据报告

## 🛠️ 技术架构

### 🎨 前端技术栈
- **Vue.js 3.5+** - 现代化前端框架
- **TypeScript** - 类型安全的JavaScript
- **Vite 7.1+** - 快速构建工具
- **TailwindCSS 4.1+** - 实用优先的CSS框架
- **Vue Router 4.6+** - 单页应用路由
- **Axios** - HTTP客户端
- **Anime.js** - 动画库

### ⚙️ 后端技术栈
- **Laravel 12** - PHP Web框架
- **PHP 8.2+** - 编程语言
- **SQLite** - 轻量级数据库
- **PHP-FPM** - PHP FastCGI进程管理器
- **Composer** - PHP包管理器

### 🐳 基础设施
- **Docker** - 容器化平台
- **Docker Compose** - 多容器编排
- **Nginx** - Web服务器/反向代理
- **GitHub Actions** - CI/CD自动化
- **GitHub Container Registry** - 镜像仓库

## 📁 项目结构

```
star-savings/
├── 📁 frontend/                 # Vue.js前端应用
│   ├── 📁 src/
│   │   ├── 📁 api/             # API接口封装
│   │   ├── 📁 components/      # Vue组件
│   │   ├── 📁 composables/     # Vue组合式函数
│   │   ├── 📁 router/          # 路由配置
│   │   ├── 📁 types/           # TypeScript类型定义
│   │   ├── 📁 utils/           # 工具函数
│   │   ├── 📁 views/           # 页面组件
│   │   └── 📄 main.ts          # 应用入口
│   ├── 📄 package.json         # 前端依赖配置
│   └── 📄 vite.config.ts       # Vite构建配置
├── 📁 backend/                  # Laravel后端应用
│   ├── 📁 app/
│   │   ├── 📁 Http/Controllers/ # 控制器
│   │   ├── 📁 Models/           # 数据模型
│   │   └── 📁 Providers/       # 服务提供者
│   ├── 📁 config/               # 配置文件
│   ├── 📁 database/             # 数据库文件
│   ├── 📁 routes/               # 路由定义
│   ├── 📁 storage/              # 存储目录
│   └── 📄 composer.json         # 后端依赖配置
├── 📁 .github/                  # GitHub配置
│   └── 📁 workflows/            # GitHub Actions工作流
├── 📁 scripts/                  # 部署脚本
├── 📄 docker-compose.yml        # Docker编排配置
├── 📄 Dockerfile               # Docker镜像构建
├── 📄 nginx.conf               # Nginx配置
└── 📄 README.md                # 项目文档
```

## 📊 API接口文档

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

### 🔍 健康检查
```http
GET    /api/health                # 服务健康状态
```

## 🔒 安全配置

### 🔐 基本认证
```nginx
# 默认认证信息
用户名: admin
密码: star123
```

### 🔧 修改认证密码
```bash
# 1. 在线生成新密码
# 访问: https://www.web2generators.com/apache-tools/htpasswd-generator

# 2. 更新.htpasswd文件
echo "admin:$(openssl passwd -apr1 '新密码')" > .htpasswd

# 3. 重启nginx容器
docker compose restart nginx
```

## 📦 部署指南

### 🏠 家庭部署
```bash
# 1. 准备服务器（树莓派、NAS等）
# 2. 安装Docker和Docker Compose
# 3. 克隆项目并启动
git clone https://github.com/YOUR_USERNAME/star-savings.git
cd star-savings
docker compose up -d
```

### ☁️ 云服务器部署
```bash
# 1. 购买云服务器（推荐2GB+内存）
# 2. 配置域名和SSL证书
# 3. 使用GitHub Actions自动部署
# 4. 配置防火墙和安全组
```

## 📋 路线图

### 🎯 短期目标（v1.1）
- [ ] 🌙 添加暗黑模式
- [ ] 📱 移动端APP（React Native）
- [ ] 📊 数据导出功能
- [ ] 🎨 自定义主题
- [ ] 📧 邮件通知功能

### 🚀 中期目标（v2.0）
- [ ] 👨‍👩‍👧‍👦 多家庭支持
- [ ] 🤖 AI智能推荐奖励
- [ ] 📈 高级数据分析
- [ ] 🔊 语音操作支持
- [ ] 🌐 多语言支持

### 🌟 长期目标（v3.0）
- [ ] 🏫 学校版本
- [ ] 🎮 游戏化学习
- [ ] 📚 教育内容集成
- [ ] 🤝 社区功能
- [ ] 📱 原生移动应用

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
- [Laravel](https://laravel.com/) - 优雅的PHP Web框架
- [TailwindCSS](https://tailwindcss.com/) - 实用优先的CSS框架
- [Docker](https://www.docker.com/) - 容器化平台
- [GitHub](https://github.com/) - 代码托管和CI/CD

---

<div align="center">

**🌟 如果这个项目对您有帮助，请给我们一个Star！**

Made with ❤️ by [YOUR_NAME](https://github.com/YOUR_USERNAME)

</div>
