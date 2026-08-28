#!/bin/bash
# ==============================================================================
# TENDORABUY / DINEFLOW AI — AWS UBUNTU 22.04/24.04 INITIAL SERVER SETUP
# Run this once on a fresh AWS EC2 / Lightsail instance:
#   sudo bash aws_initial_server_setup.sh
# ==============================================================================

set -e

echo "=========================================================="
echo " Starting Tendorabuy Server Provisioning on AWS...        "
echo "=========================================================="

# 1. Update OS packages
echo ">> Updating system packages..."
sudo apt update && sudo apt upgrade -y

# 2. Install Essentials
echo ">> Installing Git, Nginx, Curl, Unzip, Software Properties..."
sudo apt install -y software-properties-common git curl unzip nginx certbot python3-certbot-nginx supervisor htop ufw

# 3. Add PHP PPA and install PHP 8.2 + required extensions
echo ">> Installing PHP 8.2 and extensions..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-curl php8.2-gd php8.2-mbstring \
                    php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl php8.2-soap

# 4. Install Composer
echo ">> Installing Composer..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
fi

# 5. Install MySQL Server (if not using AWS RDS)
echo ">> Checking MySQL..."
if ! command -v mysql &> /dev/null; then
    echo ">> Installing Local MySQL Server..."
    sudo apt install -y mysql-server
    sudo systemctl enable mysql
    sudo systemctl start mysql
fi

# 6. Configure Firewall (UFW)
echo ">> Configuring Firewall..."
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# 7. Create web directory
sudo mkdir -p /var/www/tendorabuy
sudo chown -R $USER:www-data /var/www/tendorabuy

echo "=========================================================="
echo " Server Packages Installed Successfully!                "
echo " Next Steps:                                            "
echo " 1. Clone repository into /var/www/tendorabuy           "
echo " 2. Configure .env file with database credentials       "
echo " 3. Run deployment/scripts/deploy.sh                    "
echo " 4. Run 'sudo certbot --nginx -d tendorabuy.in' for SSL "
echo "=========================================================="
