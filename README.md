# Installation

```
git clone https://github.com/vsepohui/ruzkey
cd ruzkey && php setup.txt
```


# How to Setup local mirror on Debian:


```
sudo apt install nginx php-fpm
```

Edit /etc/nginx/sites-enabled/default  (need sudo!):

```
server {
	listen 80 default_server;

    client_max_body_size 8M;

    root /home/trunaev/ruzkey;
    index index.php;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }


}
```

Reload nginx:

```
sudo /etc/init.d/nginx/reload
```

Open browser:

```
http://127.0.0.1/
```
