# GitHub Actions 配置说明

## 🚀 自动化CI/CD工作流

本项目使用GitHub Actions实现自动化构建、测试和部署。

## 📋 工作流文件

### 1. `docker-build.yml` - 主要构建工作流
- **触发条件**: push到main/master/develop分支、PR、发布版本
- **功能**: 代码质量检查、Docker多平台构建、集成测试、生产部署
- **支持平台**: linux/amd64, linux/arm64

### 2. `security-scan.yml` - 安全扫描工作流
- **触发条件**: push到分支、PR、定时任务（每周一）
- **功能**: 依赖安全扫描、Docker漏洞扫描、敏感信息检查

## 🔧 环境变量配置

### GitHub Container Registry (GHCR) 配置

本项目使用 **GitHub Container Registry (GHCR)** 而不是 Docker Hub，有以下优势：

- ✅ **无需额外配置** - 使用 GitHub 自带的认证
- ✅ **统一管理** - 代码和镜像在同一个平台
- ✅ **免费使用** - 公开仓库免费，私有仓库有额度
- ✅ **自动认证** - 使用 GITHUB_TOKEN 自动认证

### 无需额外配置！

GitHub Actions 会自动使用以下认证信息：
- **Registry**: `ghcr.io`
- **Username**: `${{ github.actor }}` (触发工作流用户)
- **Password**: `${{ secrets.GITHUB_TOKEN }}` (GitHub自动提供)

### 手动拉取镜像的认证方法

如果需要在本地或其他服务器手动拉取镜像：

1. **创建 GitHub Personal Access Token**:
   - GitHub Settings → Developer settings → Personal access tokens → Tokens (classic)
   - 选择权限：`read:packages`, `write:packages`
   - 复制生成的 Token

2. **登录 GHCR**:
   ```bash
   echo YOUR_GITHUB_TOKEN | docker login ghcr.io -u YOUR_GITHUB_USERNAME --password-stdin
   ```

3. **拉取镜像**:
   ```bash
   docker pull ghcr.io/YOUR_USERNAME/star-savings:latest
   ```

### 可选的Secrets

| Secret名称 | 说明 | 用途 |
|-----------|------|------|
| `SSH_PRIVATE_KEY` | 服务器SSH私钥 | 生产环境部署 |
| `SERVER_HOST` | 生产服务器地址 | SSH连接 |
| `SLACK_WEBHOOK` | Slack通知URL | 部署通知 |

## 🏷️ 镜像标签策略

### GitHub Container Registry 地址：
```
ghcr.io/YOUR_USERNAME/star-savings:TAG
```

### 自动生成的标签：
- `ghcr.io/YOUR_USERNAME/star-savings:main-abc123def` - main分支的提交
- `ghcr.io/YOUR_USERNAME/star-savings:develop-xyz789` - develop分支的提交
- `ghcr.io/YOUR_USERNAME/star-savings:pr-123` - Pull Request
- `ghcr.io/YOUR_USERNAME/star-savings:v1.0.0` - 版本标签
- `ghcr.io/YOUR_USERNAME/star-savings:latest` - 最新版本（仅main分支）

## 🚀 部署流程

### 自动触发
```bash
git add .
git commit -m "feat: 添加新功能"
git push origin main
# 自动触发GitHub Actions构建和部署
```

### 手动触发
1. 进入GitHub仓库Actions页面
2. 选择 "🐳 Docker Build & Deploy" 工作流
3. 点击 "Run workflow"

## 📊 构建状态

查看构建状态：
- GitHub Actions页面
- Docker Hub仓库页面
- 仓库徽章（可添加到README）

## 🔍 测试环境

每次PR都会触发完整的测试流程：
- 代码质量检查
- 前端构建测试
- Docker配置验证
- 集成测试

## 🛡️ 安全扫描

- **依赖扫描**: 检查npm包的已知漏洞
- **镜像扫描**: Trivy扫描Docker镜像漏洞
- **敏感信息扫描**: 检查代码中的密码、密钥等

## 🚨 故障排除

### 常见问题

1. **GitHub Container Registry认证失败**
   ```
   Error: authentication required
   ```
   - 检查仓库是否为公开仓库
   - 确保GITHUB_TOKEN有packages权限
   - 检查GitHub仓库的包权限设置

2. **镜像推送失败**
   ```
   Error: denied: permission_denied
   ```
   - 确保仓库所有者正确
   - 检查GitHub包权限设置
   - 确认镜像名称格式正确

3. **构建失败**
   ```
   Error: failed to solve: process "/bin/sh -c npm install" didn't complete
   ```
   - 检查前端依赖是否正确
   - 确认package.json没有语法错误

4. **测试失败**
   ```
   API健康检查失败，状态码: 401
   ```
   - 可能是因为nginx认证配置导致的
   - 检查 `.htpasswd` 文件是否正确

### 调试技巧

1. **查看详细日志**
   - 在GitHub Actions页面点击具体的job
   - 查看每个步骤的详细输出

2. **本地复现**
   ```bash
   # 本地构建测试
   docker build -t ghcr.io/YOUR_USERNAME/star-savings:test .

   # 本地运行测试
   docker compose -f docker-compose.yml up -d
   curl http://localhost:8080/api/health
   ```

3. **使用act本地测试GitHub Actions**
   ```bash
   # 安装act
   curl https://raw.githubusercontent.com/nektos/act/master/install.sh | sudo bash

   # 本地运行工作流
   act -j docker-build
   ```

## 📈 优化建议

1. **缓存优化**
   - 利用GitHub Actions缓存
   - 使用Docker层缓存

2. **构建时间优化**
   - 并行构建多平台镜像
   - 优化Dockerfile减少层数

3. **安全优化**
   - 定期更新依赖
   - 使用最小化的基础镜像

## 🔄 工作流定制

根据项目需求可以修改：

1. **触发条件**: 修改 `on:` 部分
2. **构建矩阵**: 修改 `strategy.matrix`
3. **部署目标**: 修改 `deploy-production` job
4. **通知方式**: 添加邮件、Slack等通知

## 📚 参考资源

- [GitHub Actions文档](https://docs.github.com/en/actions)
- [GitHub Container Registry文档](https://docs.github.com/en/packages/working-with-a-github-packages-registry/working-with-the-container-registry)
- [Docker Buildx文档](https://docs.docker.com/buildx/)
- [Trivy安全扫描](https://github.com/aquasecurity/trivy)
- [GHCR最佳实践](https://docs.github.com/en/packages/working-with-a-github-packages-registry/working-with-the-container-registry)