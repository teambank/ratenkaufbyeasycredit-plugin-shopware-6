set -e

export APP_ENV=prod
export APP_URL=$SW6SETUP_URL

[ -f .env ] && rm .env
[ -f install.lock ] && rm install.lock

echo "cloning shopware 6 production @ $SW6SETUP_VERSION"
git clone --depth 1 --branch=v$SW6SETUP_VERSION https://github.com/shopware/production /tmp/shopware
cp -rv /tmp/shopware/. /var/www/shopware/

rm -r /var/www/html && ln -s /var/www/shopware/public /var/www/html

cd /var/www/shopware
composer install

echo "system:setup ..." 
echo $SW6SETUP_DB_URL
./bin/console system:setup --database-url=$SW6SETUP_DB_URL -n
echo "APP_URL=$SW6SETUP_URL" >> .env
echo "COMPOSER_HOME=/root/.composer" >> .env

echo "system:install ..." 
./bin/console system:install --create-database -n

./bin/console sales-channel:create:storefront --url $SW6SETUP_URL
./bin/console user:create -a admin -pa123456

echo "theme:change ..." 
./bin/console theme:change --all Storefront
./bin/console framework:demodata
./bin/console dal:refresh:index

chown -R www-data: var/ config/ public
