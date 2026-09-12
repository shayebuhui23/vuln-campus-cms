# 部署文档

两种部署方式任选其一：**方式一 phpStudy 本地部署**（适合逐行调试）、**方式二 Docker 一键部署**（环境干净、免配置）。

## 方式一：phpStudy 本地部署（Windows）

### 1. 环境准备

phpStudy（小皮面板）中启动：

| 服务 | 版本要求 | 说明 |
|---|---|---|
| Apache | 2.4 | Web 服务 |
| PHP | 7.x | 原生 PHP，无需框架与 Composer 依赖 |
| MySQL | 5.7 | 数据库 |

> 注意：源码目录避免使用中文路径——中文路径在 Apache + FCGI 下可能触发 502（本项目已踩过此坑，修复版目录因此由 `已修复` 更名为 `fixed`）。

### 2. 放置源码

将 `src/vuln`（漏洞版）或 `src/fixed`（修复版）复制到 phpStudy 的 Web 根目录：

```
<Web根目录>/vuln-campus-cms/
```

例如 phpStudy 默认：`D:\phpstudy_pro\WWW\vuln-campus-cms\`。根目录的 `index.html` 是入口跳转页，可一并复制。

### 3. 初始化数据库

1. 导入数据库（phpStudy 面板的数据库管理导入，或命令行）：

   ```
   mysql -u root -p < database.sql
   ```

   `database.sql` 会创建 `campus_cms` 库并插入演示数据。

2. **端口配置（重点）**：

   `src/*/includes/db.php` 的默认连接参数为：

   | 参数 | 默认值 | 说明 |
   |---|---|---|
   | DB_HOST | localhost | |
   | DB_USER | root | 靶场设定，弱口令是漏洞场景的一部分 |
   | DB_PASS | root | 同上 |
   | DB_NAME | campus_cms | |
   | DB_PORT | **3306** | phpStudy / XAMPP 的 MySQL 标准端口 |

   **大多数环境无需修改，开箱即连。**

   如果你的 MySQL 端口不是 3306（例如本项目的开发环境使用 Docker 将 MySQL 映射到宿主机 **3307**），两种改法任选：

   - 改 `src/*/includes/db.php`：`$DB_PORT = getenv('DB_PORT') ?: 3306;` 中的 `3306` 改为你的实际端口；
   - 或设置环境变量 `DB_PORT=3307`（代码会优先读取环境变量，Docker 部署正是靠这个机制）。

### 4. 验证部署

浏览器访问：

```
http://localhost/vuln-campus-cms/src/vuln/
```

能正常打开首页、登录页（login.php）即部署成功。测试账号随 `database.sql` 内置，账号密码见渗透测试报告。

## 方式二：Docker 部署

仓库根目录自带 `Dockerfile` + `docker-compose.yml`（PHP + Apache + MySQL 5.7），`database.sql` 会在容器首次启动时自动导入，无需手动配置：

```
docker-compose up -d
```

访问 `http://localhost:8099/`（web 服务映射在 8099 端口）。

数据库连接信息由 `docker-compose.yml` 通过环境变量注入（`DB_HOST=mysql`、`DB_PORT=3306`），代码无需修改。MySQL 容器同时映射到宿主机 3307 端口，方便用 Navicat 等工具连接调试（避免与宿主机已有 MySQL 冲突——本项目开发环境的 3307 端口即由此而来）。

## 切换修复版

用 `src/fixed` 覆盖 `src/vuln` 后刷新即可。修复对照与逐项复测结论见 `report/渗透测试报告v1.2.pdf` 第 7 章。

## 常见问题

| 现象 | 原因 | 处理 |
|---|---|---|
| 页面 502 / 无法访问 | 源码路径含中文 | 改为纯英文路径 |
| 数据库连接失败 | 端口不一致 | 见上文「端口配置」，核对实际端口 |
| 页面显示 SQL 报错 | 正常现象（漏洞版故意关闭了错误屏蔽） | 这本身就是调试信息泄露问题（P2 加固项） |
