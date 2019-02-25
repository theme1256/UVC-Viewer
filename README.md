## Setup

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