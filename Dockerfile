FROM php:8.1-apache

# 安装mysqli扩展(PHP官方镜像默认不带,需要手动装)
RUN docker-php-ext-install mysqli

# 允许显示错误信息,方便教学时直接看到PHP报错(生产环境不建议这样配置)
# 同时显式指定default_charset,避免中文乱码问题
# 显式关闭zlib.output_compression,避免PHP对输出内容做gzip压缩
# 导致某些环境下浏览器未能正确解压而显示乱码
RUN { \
    echo 'display_errors = On'; \
    echo 'error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE'; \
    echo 'default_charset = "UTF-8"'; \
    echo 'zlib.output_compression = Off'; \
    } > /usr/local/etc/php/conf.d/lab.ini

# 同样在Apache层面禁用mod_deflate(如果基础镜像意外启用了这个模块),
# 确保Apache本身也不会对响应内容做gzip压缩
RUN a2dismod deflate 2>/dev/null || true

# 允许.htaccess等常规Apache配置生效(如果以后要加访问控制会用到)
RUN a2enmod rewrite

# 仓库结构中 src/vuln 为漏洞版（Docker 部署默认跑漏洞版；
# 想跑修复版把下面两行的 src/vuln 换成 src/fixed）
COPY src/vuln/ /var/www/html/

# uploads目录需要Web服务器有写入权限,文件上传功能才能正常工作
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html/uploads

EXPOSE 80