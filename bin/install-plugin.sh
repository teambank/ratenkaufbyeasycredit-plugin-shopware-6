mkdir -p /var/www/shopware/custom/plugins/EasyCreditRatenkauf
cp -r * /var/www/shopware/custom/plugins/EasyCreditRatenkauf
cd /var/www/shopware
php bin/console plugin:refresh
php bin/console plugin:install --activate EasyCreditRatenkauf
