# 星星存折项目 Docker 部署指南

## 项目概述
这是一个基于 Vue.js 前端 + Laravel 后端的全栈星星存折应用，使用 Docker 容器化部署。

## 技术栈
- **前端**: Vue 3 + TypeScript + Vite + TailwindCSS
- **后端**: Laravel 12 + PHP 8.2 + SQLite
- **Web服务器**: Nginx
- **容器化**: Docker + Docker Compose

## 端口配置
- **应用访问**: http://localhost:8008
- **前端开发**: http://localhost:5174 (开发环境)
- **API接口**: http://localhost:8008/api/*

## Docker 配置文件

### docker-compose.yml
- **backend服务**: Laravel PHP-FPM 应用服务器
- **nginx服务**: Nginx 反向代理，处理静态文件和API请求
- **数据持久化**:
  - `./backend/storage:/var/www/html/storage` (应用数据)
  - `app-public:/var/www/html/public` (前端构建文件)

### Dockerfile
多阶段构建:
1. **Stage 1**: 构建前端资源 (Node.js Alpine)
2. **Stage 2**: Laravel 运行环境 (PHP-FPM)

### nginx.conf
- API请求: `/api/*` → PHP-FPM处理
- 静态资源: `/dist/*` → 直接返回
- SPA路由: 其他所有路径 → `/dist/index.html`

## 环境变量配置

### 生产环境变量 (docker-compose.yml)
```yaml
environment:
  - APP_ENV=production
  - APP_DEBUG=false
  - APP_KEY=base64:5L8Gz3vZk8Yx8MqN8o4jK3n7D6r5P2sQ4wT6uV9aXc=
  - APP_URL=http://localhost:8008
  - LOG_CHANNEL=stderr
  - DB_CONNECTION=sqlite
  - DB_DATABASE=/var/www/html/storage/app/database.sqlite
```

### 数据库配置
- 使用 SQLite 数据库
- 数据文件位置: `./backend/storage/app/database.sqlite`
- 支持的数据表: children, rewards, star_records, reward_children

## 部署步骤

### 1. 构建和启动容器
```bash
# 构建并启动所有服务
docker compose up -d --build

# 查看服务状态
docker compose ps

# 查看日志
docker compose logs -f
```

### 2. 初始化检查
```bash
# 检查后端健康状态
curl http://localhost:8008/api/health

# 检查前端是否可访问
curl http://localhost:8008
```

### 3. 维护操作
```bash
# 重新构建前端
docker compose exec backend php artisan migrate:fresh --force

# 查看应用日志
docker compose logs backend

# 重启服务
docker compose restart
```

## 健康检查
- **后端健康检查**: `/api/health` 端点
- **服务依赖**: nginx 等待 backend 健康后再启动
- **检查间隔**: 30秒
- **超时时间**: 10秒

## 开发环境

### 前端开发服务器
```bash
cd frontend
npm install
npm run dev
# 访问: http://localhost:5174
```

### 后端开发服务器
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
# API访问: http://localhost:8000/api
```

## 故障排除

### 常见问题
1. **APP_KEY错误**: 确保 docker-compose.yml 中设置了正确的 APP_KEY
2. **权限问题**: storage 目录需要正确的写权限
3. **数据库文件**: 确保 SQLite 文件可写
4. **前端构建失败**: 检查 node_modules 和构建过程

### 日志查看
```bash
# 查看所有服务日志
docker compose logs

# 查看特定服务日志
docker compose logs backend
docker compose logs nginx

# 实时日志
docker compose logs -f backend
```

### 数据备份
```bash
# 备份 SQLite 数据库
cp ./backend/storage/app/database.sqlite ./backup/database-$(date +%Y%m%d).sqlite

# 备份存储文件
tar -czf ./backup/storage-$(date +%Y%m%d).tar.gz ./backend/storage
```

## 性能优化
- Laravel 配置缓存 (config:cache)
- 路由缓存 (route:cache)
- 视图缓存 (view:cache)
- Nginx 静态文件缓存
- Gzip 压缩 (nginx 配置)

## 安全考虑
- 生产环境 APP_DEBUG=false
- 使用 HTTPS (生产环境)
- 定期更新依赖包
- 文件上传限制 (128MB)
- Nginx 安全头配置