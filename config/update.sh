cd /var/www/html/
sudo git checkout .
sudo git pull

sudo chown www-data:www-data /var/www -R
sudo chmod 775 /var/www -R