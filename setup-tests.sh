#!/bin/bash
sudo apt-get update -qq
sudo add-apt-repository -y ppa:ondrej/php
install-package php7.0 php7.0-curl php7.0-sqlite3 php7.0-mbstring php7.0-dom git-core curl build-essential openssl libssl-dev apt-transport-https
sudo a2dismod php5
sudo a2enmod php7.0
sudo a2enmod rewrite
sudo a2enmod env
sudo sh -c "echo '<Directory /var/www/>' >> /etc/apache2/sites-enabled/000-default.conf"
sudo sh -c "echo '  Options Indexes FollowSymLinks' >> /etc/apache2/sites-enabled/000-default.conf"
sudo sh -c "echo '  AllowOverride All' >> /etc/apache2/sites-enabled/000-default.conf"
sudo sh -c "echo '  Require all granted' >> /etc/apache2/sites-enabled/000-default.conf"
sudo sh -c "echo '</Directory>' >> /etc/apache2/sites-enabled/000-default.conf"
source ~/.phpbrew/bashrc
sudo service apache2 restart
composer self-update -n
composer install --prefer-source -n
sudo sh -c "echo '127.0.0.1 resbeat.testing.localhost' >> /etc/hosts"
sudo sh -c "echo '127.0.0.1 resbeat.localhost' >> /etc/hosts"
sudo sh -c "echo '127.0.0.1 localhost:8000' >> /etc/hosts"
sudo sh -c "echo '127.0.0.1 localhost' >> /etc/hosts"
