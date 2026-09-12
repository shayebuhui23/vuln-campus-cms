-- ============================================================
-- 校园新闻发布系统 - 数据库初始化脚本
-- 用于AWD漏洞防御教学，请勿用于真实生产环境
-- ============================================================

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS campus_cms DEFAULT CHARACTER SET utf8mb4;
USE campus_cms;

-- 管理员/用户表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    email VARCHAR(100),
    phone VARCHAR(20),
    real_name VARCHAR(50),
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, role, email, phone, real_name) VALUES
('admin', 'Adm1n@2026Secret', 'admin', 'admin@campus.edu.cn', '13800000001', '系统管理员'),
('zhangwei', 'zhangwei123', 'user', 'zhangwei@campus.edu.cn', '13800000002', '张伟'),
('linna', 'linna123', 'user', 'linna@campus.edu.cn', '13800000003', '林娜'),
('wangqiang', 'wangqiang123', 'user', 'wangqiang@campus.edu.cn', '13800000004', '王强'),
('chenjing', 'chenjing123', 'teacher', 'chenjing@campus.edu.cn', '13800000005', '陈静老师');

-- 新闻表
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(50),
    category VARCHAR(50),
    views INT DEFAULT 0,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO news (title, content, author, category, views) VALUES
('2026年春季运动会圆满举行', '本届运动会于4月15日在学校田径场顺利举行，共有来自全校12个院系的3000余名学生参与...', '校团委', '校园动态', 1520),
('图书馆新增电子阅览室', '为了给同学们提供更好的学习环境，图书馆三楼新增电子阅览室，配备50台高性能电脑...', '图书馆', '通知公告', 890),
('计算机学院举办网络安全知识竞赛', '本次竞赛旨在提升同学们的网络安全意识，吸引了200余名学生报名参加...', '计算机学院', '院系新闻', 2340),
('食堂三楼新增地方特色菜窗口', '为丰富同学们的就餐选择，三楼食堂新增川菜、粤菜等地方特色菜窗口...', '后勤处', '生活服务', 670),
('关于期末考试安排的通知', '各位同学，本学期期末考试将于第18周开始，具体安排请查看教务系统...', '教务处', '通知公告', 4520),
('校园歌手大赛决赛名单公布', '经过初赛、复赛的层层选拔，共有10组选手进入决赛，决赛将于本周六晚举行...', '学生会', '校园动态', 1890),
('心理健康教育中心开展团体辅导活动', '为帮助同学们缓解学业压力，心理健康教育中心本周将开展系列团体辅导活动...', '心理中心', '通知公告', 430),
('机器人协会在省级比赛中获得一等奖', '我校机器人协会代表队在本次省级机器人大赛中表现优异，荣获一等奖...', '学生社团联合会', '院系新闻', 1120);

-- 留言板表(用于XSS演示)
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    news_id INT,
    nickname VARCHAR(50),
    content TEXT,
    create_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO comments (news_id, nickname, content) VALUES
(1, '路过的同学', '运动会办得很成功，希望明年能有更多项目！'),
(1, '大二学生', '田径场的加油声真的很燃'),
(3, '安全爱好者', '希望学校多举办这类竞赛，很有意义'),
(5, '着急的学生', '请问期末考试安排能不能再详细一点');

-- 公告文件表(用于文件包含漏洞演示,存储的是文件名而非内容)
CREATE TABLE IF NOT EXISTS notice_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(100) NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

INSERT INTO notice_files (filename, display_name) VALUES
('welcome.txt', '新生欢迎词'),
('rules.txt', '校园管理规定'),
('holiday.txt', '放假通知');
