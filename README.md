## Setup af Raspberry Pi

This software has been developed to run on a Raspberry Pi 3B+, so it's recommended that you use a device that is at least that powerfull.

### Installer Raspbian

Download a program to flash an image to an SD-card, I prefer [balenaEtcher](https://www.balena.io/etcher/).  
Download an image of [Raspbian](https://www.raspberrypi.org/downloads/raspbian/) with desktop environment.

Flash the image to the SD-card and follow the setup on the Pi.

## Installation

Clone this project to www-dir:  
```bash
sudo mkdir /var/www/html -P
cd /var/www/html
git clone https://github.com/theme1256/UVC-Viewer .
```

Execute the setup script:  
```bash
sudo chmod +x config/setup.sh
sudo config/setup.sh
```

Open http://localhost/ in a browser  
Follow the instructions

## Update

Exectute the update script:  
```bash
sudo chmod +x config/update.sh
```

Or the following commands:  
```bash
sudo git checkout .
sudo git pull
sudo chown www-data:www-data ./* -R
sudo chmod 775 ./* -R
```