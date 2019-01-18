sudo mkdir /home/pi/.config/lxsession
sudo mkdir /home/pi/.config/lxsession/LXDE-pi
sudo mkdir /home/pi/.config/autostart

sudo rm /home/pi/.config/lxsession/LXDE-pi/autostart
sudo touch /home/pi/.config/lxsession/LXDE-pi/autostart
sudo echo "@xset s 0 0" >> /home/pi/.config/lxsession/LXDE-pi/autostart
sudo echo "@xset s noblank" >> /home/pi/.config/lxsession/LXDE-pi/autostart
sudo echo "@xset s noexpose" >> /home/pi/.config/lxsession/LXDE-pi/autostart
sudo echo "@xset dpms 0 0 0" >> /home/pi/.config/lxsession/LXDE-pi/autostart

sudo rm /home/pi/.config/autostart/autoChromium.desktop
sudo touch /home/pi/.config/autostart/autoChromium.desktop

sudo echo "[Desktop Entry]" >> /home/pi/.config/autostart/autoChromium.desktop
sudo echo "Type=Application" >> /home/pi/.config/autostart/autoChromium.desktop
sudo echo "Exec=/usr/bin/chromium-browser --noerrdialogs --disable-session-crashed-bubble --disable-infobars --kiosk http://localhost/" >> /home/pi/.config/autostart/autoChromium.desktop
sudo echo "Hidden=false" >> /home/pi/.config/autostart/autoChromium.desktop
sudo echo "X-GNOME-Autostart-enabled=true" >> /home/pi/.config/autostart/autoChromium.desktop
sudo echo "Name[en_US]=AutoChromium" >> /home/pi/.config/autostart/autoChromium.desktop
sudo echo "Name=AutoChromium" >> /home/pi/.config/autostart/autoChromium.desktop
sudo echo "Comment=Start Chromium when GNOME starts" >> /home/pi/.config/autostart/autoChromium.desktop

sudo chown pi:pi /home/pi/.config -R

sudo apt install apache2 -y
sudo a2enmod rewrite
sudo sed -i 's/<Directory \/var\/www\/>\n	Options Indexes FollowSymLinks\n	AllowOverride None/<Directory \/var\/www\/>\n	Options Indexes FollowSymLinks\n	AllowOverride All/g' /etc/apache2/apache2.conf
sudo service apache2 restart

sudo apt install php libapache2-mod-php -y

sudo wget -q -o=tw.deb https://download.teamviewer.com/download/linux/version_13x/teamviewer-host_armhf.deb
sudo dpkg --install tw.deb
sudo rm tw.deb
sudo apt install -f

sudo chown www-data:www-data /var/www -R
sudo chmod 775 /var/www -R