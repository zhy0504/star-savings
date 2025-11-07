#!/bin/bash

# 星星存折项目部署脚本
# 用法: ./scripts/deploy.sh [环境] [镜像标签]

set -e

# 配置
PROJECT_NAME="star-savings"
GHCR_OWNER="${GHCR_OWNER:-$(git config --get remote.origin.url | sed 's/.*\/\([^\/]*\)\.git/\1/')}"
GHCR_REGISTRY="${GHCR_REGISTRY:-ghcr.io}"
ENVIRONMENT="${1:-production}"
IMAGE_TAG="${2:-latest}"
CONTAINER_PREFIX="star"

# 颜色输出
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 日志函数
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查Docker是否安装
check_docker() {
    if ! command -v docker &> /dev/null; then
        log_error "Docker未安装或不在PATH中"
        exit 1
    fi

    if ! command -v docker-compose &> /dev/null; then
        log_error "docker-compose未安装或不在PATH中"
        exit 1
    fi

    log_success "Docker环境检查通过"
}

# 拉取最新镜像
pull_images() {
    log_info "拉取Docker镜像: ${GHCR_REGISTRY}/${GHCR_OWNER}/${PROJECT_NAME}:${IMAGE_TAG}"

    if docker pull "${GHCR_REGISTRY}/${GHCR_OWNER}/${PROJECT_NAME}:${IMAGE_TAG}"; then
        log_success "镜像拉取成功"
    else
        log_error "镜像拉取失败"
        exit 1
    fi
}

# 备份当前数据
backup_data() {
    log_info "备份当前数据..."

    BACKUP_DIR="./backups/$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$BACKUP_DIR"

    # 备份数据库
    if [ -f "./backend/storage/app/database.sqlite" ]; then
        cp "./backend/storage/app/database.sqlite" "$BACKUP_DIR/"
        log_success "数据库备份完成"
    fi

    # 备份存储文件
    if [ -d "./backend/storage/app/public" ]; then
        cp -r "./backend/storage/app/public" "$BACKUP_DIR/"
        log_success "存储文件备份完成"
    fi

    log_success "数据备份完成: $BACKUP_DIR"
}

# 停止当前服务
stop_services() {
    log_info "停止当前服务..."

    if docker-compose -f docker-compose.yml ps | grep -q "Up"; then
        docker-compose -f docker-compose.yml down
        log_success "服务已停止"
    else
        log_warning "没有运行中的服务"
    fi
}

# 更新配置文件
update_config() {
    log_info "更新配置文件..."

    # 创建生产环境配置
    if [ "$ENVIRONMENT" = "production" ]; then
        # 备份当前配置
        [ -f "docker-compose.yml" ] && cp docker-compose.yml docker-compose.yml.backup

        # 更新镜像标签
        sed -i.bak "s|image: ${PROJECT_NAME}:.*|image: ${GHCR_REGISTRY}/${GHCR_OWNER}/${PROJECT_NAME}:${IMAGE_TAG}|g" docker-compose.yml

        log_success "生产环境配置已更新"
    fi
}

# 启动新服务
start_services() {
    log_info "启动新服务..."

    # 等待几秒钟
    sleep 5

    # 启动服务
    if docker-compose -f docker-compose.yml up -d; then
        log_success "服务启动成功"
    else
        log_error "服务启动失败"
        exit 1
    fi

    # 等待服务完全启动
    log_info "等待服务完全启动..."
    sleep 30
}

# 健康检查
health_check() {
    log_info "执行健康检查..."

    # 检查容器状态
    if ! docker-compose -f docker-compose.yml ps | grep -q "Up"; then
        log_error "容器未正常启动"
        docker-compose -f docker-compose.yml logs
        exit 1
    fi

    # 检查API健康状态
    local max_attempts=10
    local attempt=1

    while [ $attempt -le $max_attempts ]; do
        if curl -f -s http://localhost:8080/api/health > /dev/null 2>&1; then
            log_success "API健康检查通过"
            break
        else
            log_warning "API健康检查失败，重试中... ($attempt/$max_attempts)"
            sleep 10
            ((attempt++))
        fi
    done

    if [ $attempt -gt $max_attempts ]; then
        log_error "API健康检查最终失败"
        docker-compose -f docker-compose.yml logs
        exit 1
    fi

    # 检查前端页面
    if curl -f -s http://localhost:8080 > /dev/null 2>&1; then
        log_success "前端页面访问正常"
    else
        log_warning "前端页面可能需要认证，这是正常的"
    fi
}

# 清理旧镜像
cleanup() {
    log_info "清理旧的Docker镜像..."

    # 删除旧的镜像
    docker image prune -f

    # 删除未使用的卷
    docker volume prune -f

    log_success "清理完成"
}

# 显示部署信息
show_deployment_info() {
    log_success "🎉 部署完成！"
    echo ""
    echo "📋 部署信息:"
    echo "   环境: $ENVIRONMENT"
    echo "   镜像: ${GHCR_REGISTRY}/${GHCR_OWNER}/${PROJECT_NAME}:${IMAGE_TAG}"
    echo "   访问地址: http://localhost:8080"
    echo "   API地址: http://localhost:8080/api"
    echo ""
    echo "🔧 管理命令:"
    echo "   查看日志: docker-compose logs -f"
    echo "   停止服务: docker-compose down"
    echo "   重启服务: docker-compose restart"
    echo ""
    echo "📊 监控命令:"
    echo "   容器状态: docker-compose ps"
    echo "   资源使用: docker stats"
}

# 回滚函数
rollback() {
    log_error "部署失败，正在回滚..."

    # 停止当前服务
    docker-compose -f docker-compose.yml down || true

    # 恢复配置文件
    if [ -f "docker-compose.yml.backup" ]; then
        mv docker-compose.yml.backup docker-compose.yml
        log_success "配置文件已恢复"
    fi

    # 启动之前的服务
    docker-compose -f docker-compose.yml up -d

    log_error "回滚完成，请检查服务状态"
}

# 主函数
main() {
    log_info "开始部署星星存折项目..."
    log_info "环境: $ENVIRONMENT"
    log_info "镜像: ${GHCR_REGISTRY}/${GHCR_OWNER}/${PROJECT_NAME}:${IMAGE_TAG}"

    # 设置错误处理
    trap 'rollback' ERR

    # 执行部署步骤
    check_docker
    pull_images
    backup_data
    stop_services
    update_config
    start_services
    health_check
    cleanup
    show_deployment_info

    # 移除错误处理
    trap - ERR

    log_success "✅ 部署成功完成！"
}

# 显示帮助信息
show_help() {
    echo "星星存折项目部署脚本"
    echo ""
    echo "用法:"
    echo "  $0 [环境] [镜像标签]"
    echo ""
    echo "参数:"
    echo "  环境      部署环境 (production|staging|development, 默认: production)"
    echo "  镜像标签  Docker镜像标签 (默认: latest)"
    echo ""
    echo "示例:"
    echo "  $0 production latest"
    echo "  $0 staging main-abc123def"
    echo "  $0 development v1.0.0"
    echo ""
    echo "环境变量:"
    echo "  GHCR_OWNER        GitHub Container Registry仓库所有者"
    echo "  GHCR_REGISTRY     GitHub Container Registry地址 (默认: ghcr.io)"
    echo ""
}

# 脚本入口
case "${1:-}" in
    -h|--help)
        show_help
        exit 0
        ;;
    *)
        main "$@"
        ;;
esac