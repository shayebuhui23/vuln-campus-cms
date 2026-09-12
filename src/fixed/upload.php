<?php
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ?> 的文件,
         * 命名为 shell.php,上传成功后直接访问
         * http://目标地址/uploads/shell.php
         * 就能通过POST传入cmd参数执行任意PHP代码(俗称"一句话木马")
         * ============================================================
         */
        $upload_dir = __DIR__ . '/uploads/';
        $filename = $file['name']; // 直接用用户提供的原始文件名,没有做任何处理
        // 白名单校验:只允许这几种图片后缀
		$allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		if (!in_array($ext, $allowed_ext)) {
			die('不允许上传该类型的文件，只能上传jpg/jpeg/png/gif格式的图片');
		}

		// 修复1: 内容校验,用getimagesize确认文件真的是图片,
		// 伪造成图片后缀的脚本/图片马会因为内容不是合法图片被拦下
		$img_info = @getimagesize($file['tmp_name']);
		if ($img_info === false) {
			die('文件内容不是有效的图片，已拒绝上传');
		}

		// 修复2: 随机重命名,不再使用用户提供的原始文件名。
		// 文件名完全由服务器生成,同时消除Windows尾点绕过
		// (如 shell.php. 靠落盘时去尾点还原成shell.php)这类文件名层面的绕过
		$new_name = date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
		$target_path = $upload_dir . $new_name;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $message = '上传成功！文件保存路径: /uploads/' . $new_name;
            $message_type = 'success';
            $uploaded_path = 'uploads/' . $new_name;
        } else {
            $message = '上传失败';
            $message_type = 'error';
        }
    } else {
        $message = '上传出错，错误代码: ' . $file['error'];
        $message_type = 'error';
    }
}

$page_title = '上传头像';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:500px;">
  <h2>📤 上传个人头像</h2>
  <p style="font-size:13px;color:#999;">支持jpg/png格式图片，用于个人资料展示</p>

  <?php if ($message): ?>
    <div class="flash <?php echo $message_type; ?>">
      <?php echo $message; ?>
      <?php if ($uploaded_path): ?>
        <br><a href="<?php echo htmlspecialchars($uploaded_path); ?>" target="_blank">点击查看上传的文件</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    <label>选择文件</label>
    <input type="file" name="avatar">
    <button class="btn" type="submit" style="margin-top:10px;">上传</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
