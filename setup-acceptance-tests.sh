#!/bin/bash
chown -R $USER /usr/local
chown -R $USER:$GROUP ~/.npm
chown -R $USER:$GROUP ~/.cache
curl -sL https://deb.nodesource.com/setup_7.x | sudo -E bash -
install-package nodejs
curl https://npmjs.org/install.sh | sudo sh
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
apt-get update && install-package yarn
npm install -g bower
yarn
bower install --allow-root
npm rebuild node-sass
npm install -g gulp-cli
npm install --save-dev gulp
gulp
cp .resbeat.testing.localhost.env .env
rm -rf /var/www/html
ln -s /home/runner/resbeat-repo/public /var/www/html
chmod -R 777 database
chmod -R 777 storage
chmod -R +x vendor/bin
