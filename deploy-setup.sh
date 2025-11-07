#!/bin/bash
# Star Savings 部署初始化脚本
# 用于在服务器上创建必要的目录结构和数据库文件

set -e

echo "======================================"
echo "Star Savings 部署初始化"
echo "======================================"

# 设置部署目录（可通过环境变量覆盖）
DEPLOY_DIR="${DEPLOY_DIR:-/root/docker/star}"
# 设置访问端口（可通过环境变量覆盖）
PORT="${PORT:-8080}"

echo "1. 创建必要的目录结构..."
mkdir -p ${DEPLOY_DIR}/backend/storage/app
mkdir -p ${DEPLOY_DIR}/backend/storage/framework/sessions
mkdir -p ${DEPLOY_DIR}/backend/storage/framework/views
mkdir -p ${DEPLOY_DIR}/backend/storage/framework/cache
mkdir -p ${DEPLOY_DIR}/backend/storage/logs
echo "✓ 目录结构创建完成"

echo "2. 创建空的数据库文件..."
touch ${DEPLOY_DIR}/backend/storage/app/database.sqlite
echo "✓ 数据库文件创建完成"

echo "3. 设置正确的权限..."
# www-data 用户的 UID 通常是 33
chown -R 33:33 ${DEPLOY_DIR}/backend/storage
chmod -R 775 ${DEPLOY_DIR}/backend/storage
echo "✓ 权限设置完成"

echo "======================================"
echo "初始化完成！"
echo "======================================"
echo ""
echo "接下来的步骤："
echo "1. 确保 docker-compose.yml、nginx.conf 和 .htpasswd 文件已放置在 ${DEPLOY_DIR}"
echo "2. 进入部署目录: cd ${DEPLOY_DIR}"
echo "3. 启动服务: docker compose up -d"
echo "4. 查看日志: docker compose logs -f"
echo ""
echo "访问地址: http://你的服务器IP:${PORT}"
echo "默认认证: 用户名 admin，密码 star123"
echo ""
echo "提示："
echo "- 如需修改端口，请编辑 docker-compose.yml 中的端口映射"
echo "- 如需修改密码，请使用: htpasswd -c .htpasswd admin"
