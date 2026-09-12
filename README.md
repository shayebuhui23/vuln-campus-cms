# vuln-campus-cms

> 校园新闻发布系统漏洞靶场 —— 一个故意存在多种典型 Web 漏洞的 PHP + MySQL 应用，附完整渗透测试报告与修复版源码。

**Vuln-CMS** 是一个 Web 安全教学靶场：原生 PHP（无框架）+ MySQL，模拟一个真实的校园新闻发布系统。仓库同时提供 **漏洞版**（`src/vuln`）与 **修复版**（`src/fixed`）两套源码，以及一份按企业规范编写的轻量化渗透测试报告，可完整复现「漏洞发现 → 利用验证 → 修复 → 复测」全过程。

## ⚠️ 免责声明

本项目仅供 **安全学习与研究** 使用。请勿将所学技术用于任何未授权环境，使用本靶场即表示你同意仅在合法授权的前提下进行测试。

## 漏洞概览

靶场包含 8 个典型 Web 漏洞：SQL 注入、操作系统命令注入、任意文件上传、文件包含、XSS 等，覆盖独立漏洞与组合利用场景。每个漏洞的原理分析、利用过程与 CVSS 定级详见配套报告。

## 快速开始

两种部署方式（详见 [deploy/README.md](deploy/README.md)）：

**方式一：phpStudy 本地部署** —— Apache 2.4 + PHP 7.x + MySQL 5.7，导入 `database.sql` 即可，连接参数开箱即连（默认 3306 端口，其他端口见部署文档的端口配置说明）。

**方式二：Docker 一键部署** —— 仓库根目录自带 `Dockerfile` 与 `docker-compose.yml`，`docker-compose up -d` 后访问 `http://localhost:8099/`，数据库自动初始化。

部署完成后：

1. 浏览器访问 `http://localhost/vuln-campus-cms/src/vuln/`（或 Docker 的 8099 端口）；
2. 按报告第 4 章的步骤逐项复现 8 个漏洞；
3. 用 `src/fixed` 替换 `src/vuln`，按报告第 7 章验证修复效果。

## 目录结构

```
vuln-campus-cms/
├── README.md                       # 本文件
├── report/
│   └── 渗透测试报告v1.2.pdf         # 完整渗透测试报告（含复现截图）
├── src/
│   ├── vuln/                       # 漏洞版源码
│   └── fixed/                      # 修复版源码
├── database.sql                    # 数据库初始化脚本（建库 + 演示数据）
├── Dockerfile / docker-compose.yml # Docker 一键部署
├── index.html                      # 入口跳转页
└── deploy/
    └── README.md                   # 部署文档（含端口配置说明）
```

## 测试账号

演示账号不直接公开，导入 `database.sql` 后即可获得全部测试数据（含管理员与普通用户账号），账号密码可见渗透测试报告。

## 配套报告

完整测试过程（信息收集 → 攻击面分析 → 8 个漏洞的原理 / 测试步骤 / 利用结果 / 修复建议 → 整改与复测，含全部复现截图）见 [`report/渗透测试报告v1.2.pdf`](report/渗透测试报告v1.2.pdf)。

参考：

- [OWASP Top 10](https://owasp.org/Top10/)
- [CVSS 3.1 计算器](https://www.first.org/cvss/calculator/3.1)
- [CWE 官方列表](https://cwe.mitre.org/)
