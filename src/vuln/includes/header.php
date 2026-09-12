<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>校园新闻发布系统</title>
<style>
  * { box-sizing: border-box; }
  body { margin:0; font-family:"Microsoft Yahei",sans-serif; background:#f0f2f5; color:#333; }
  .topbar { background:#2c5cc5; color:#fff; padding:16px 30px; display:flex; justify-content:space-between; align-items:center; }
  .topbar .brand { font-size:20px; font-weight:bold; }
  .topbar .nav a { color:#fff; margin-left:20px; text-decoration:none; font-size:14px; }
  .topbar .nav a:hover { text-decoration:underline; }
  .container { max-width:1000px; margin:24px auto; padding:0 16px; }
  .card { background:#fff; border-radius:8px; padding:20px 24px; box-shadow:0 1px 4px rgba(0,0,0,0.08); margin-bottom:20px; }
  .card h2 { margin-top:0; font-size:18px; border-left:4px solid #2c5cc5; padding-left:10px; }
  table { width:100%; border-collapse:collapse; font-size:14px; }
  table th, table td { padding:10px 8px; border-bottom:1px solid #eee; text-align:left; }
  table th { color:#888; }
  .btn { display:inline-block; padding:8px 18px; background:#2c5cc5; color:#fff; border:none; border-radius:5px; text-decoration:none; font-size:14px; cursor:pointer; }
  .btn.secondary { background:#eee; color:#333; }
  input[type=text], input[type=password], textarea, select {
    width:100%; padding:9px 10px; border:1px solid #ddd; border-radius:5px; font-size:14px; margin-bottom:12px;
  }
  label { font-size:13px; color:#666; }
  .flash { padding:10px 14px; border-radius:6px; margin-bottom:16px; font-size:14px; }
  .flash.error { background:#fdecea; color:#c0392b; }
  .flash.success { background:#eafaf1; color:#27ae60; }
  .news-item { border-bottom:1px solid #eee; padding:14px 0; }
  .news-item a { color:#2c5cc5; text-decoration:none; font-size:16px; font-weight:bold; }
  .news-item .meta { color:#999; font-size:12px; margin-top:4px; }
  .footer-note { text-align:center; color:#999; font-size:12px; margin:30px 0; }
</style>
</head>
<body>
<div class="topbar">
  <div class="brand">🎓 校园新闻发布系统</div>
  <div class="nav">
    <a href="index.php">首页</a>
    <a href="search.php">搜索新闻</a>
    <a href="upload.php">上传头像</a>
    <a href="tools.php">系统工具</a>
    <a href="notice.php">公告查看</a>
    <?php if (isset($_SESSION['uid'])): ?>
      <a href="profile.php?id=<?php echo $_SESSION['uid']; ?>">我的资料</a>
      <a href="logout.php">退出</a>
    <?php else: ?>
      <a href="login.php">登录</a>
    <?php endif; ?>
  </div>
</div>
<div class="container">
