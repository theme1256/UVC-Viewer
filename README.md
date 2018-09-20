Based on X project by Y

## Setup

Install apache and php on the Raspberry Pi3  
```bash
sudo apt install apache2 php libapache2-mod-php
```
Clone this project to www-dir  
```bash
cd /var/www/html
git clone http://project.url:project .
```
On the Pi, goto http://localhost/  
Follow the instructions

## Setup auto-launch Chromium and view streams

Copy `config/autoChromium.desktop` to `~/.config/autostart/autoChromium.desktop`  
or  
execute `config/setup.sh`