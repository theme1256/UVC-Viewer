sudo echo "@xset s 0 0" >> ~/.config/lxsession/LXDE-pi/autostart
sudo echo "@xset s noblank" >> ~/.config/lxsession/LXDE-pi/autostart
sudo echo "@xset s noexpose" >> ~/.config/lxsession/LXDE-pi/autostart
sudo echo "@xset dpms 0 0 0" >> ~/.config/lxsession/LXDE-pi/autostart

sudo mkdir ~/.config/autostart
sudo touch ~/.config/autostart/autoChromium.desktop

sudo echo "[Desktop Entry]" >> ~/.config/autostart/autoChromium.desktop
sudo echo "Type=Application" >> ~/.config/autostart/autoChromium.desktop
sudo echo "Exec=/usr/bin/chromium-browser --noerrdialogs --disable-session-crashed-bubble --disable-infobars --kiosk http://localhost/index2.php" >> ~/.config/autostart/autoChromium.desktop
sudo echo "Hidden=false" >> ~/.config/autostart/autoChromium.desktop
sudo echo "X-GNOME-Autostart-enabled=true" >> ~/.config/autostart/autoChromium.desktop
sudo echo "Name[en_US]=AutoChromium" >> ~/.config/autostart/autoChromium.desktop
sudo echo "Name=AutoChromium" >> ~/.config/autostart/autoChromium.desktop
sudo echo "Comment=Start Chromium when GNOME starts" >> ~/.config/autostart/autoChromium.desktop

sudo chown pi:pi ~/.config/autostart/autoChromium.desktop

sudo apt install apache2 -y
sudo a2enmod rewrite
sudo sed -i 's/<Directory /var/www/>\n        Options Indexes FollowSymLinks\n        AllowOverride None/<Directory /var/www/>\n        Options Indexes FollowSymLinks\n        AllowOverride All/g' /etc/apache2/apache.conf
sudo service apache2 restart

sudo apt install php libapache2-mod-php