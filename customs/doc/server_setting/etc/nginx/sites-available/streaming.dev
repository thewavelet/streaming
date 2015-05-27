server {
    listen       80;
    server_name  hanmonet.com;
    root       /var/Sites/streaming-bbs.com/;
 
    access_log  /var/log/nginx/streaming.access.log  main;
 
    location / {
        include   /etc/nginx/conf.d/php-fpm;
    }

#    location = /info {
#        allow   127.0.0.1;
#        deny    all;
#        rewrite (.*) /.info.php;
#    }

#    error_page  404     /404.html;
#    error_page  403     /403.html;
}
