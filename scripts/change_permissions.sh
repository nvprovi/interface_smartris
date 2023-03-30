#!/bin/bash

# Fix user rights
# sudo usermod -a -G apache ec2-user
sudo chown -R ec2-user /home/ec2-user
sudo chmod 2775 /home/ec2-user/swr_websocket_client
find /home/ec2-user/swr_websocket_client -type d -exec sudo chmod 2775 {} \;
find /home/ec2-user/swr_websocket_client -type f -exec sudo chmod 0664 {} \;
sudo chmod 777 /home/ec2-user/swr_websocket_client/resources/local_storage.db
sudo ln -sf /usr/share/zoneinfo/Europe/Berlin /etc/localtime
sudo systemctl restart php-fpm
sudo crontab -r -u root
sudo crontab -r -u ec2-user
echo "*/1 * * * * php /home/ec2-user/swr_websocket_client/public/run_websocket_client.php >/dev/null 2>&1"  | crontab -
# echo "*/1 * * * * php /var/www/html/cronjobs/cronjob_check_websocket_connect.php >/dev/null 2>&1"  | crontab -
# crontab -l | { cat; echo "0 4 * * * php /var/www/html/cronjobs/renew_subscription.php >/dev/null 2>&1"; } | crontab -
# crontab -l | { cat; echo "*/10 * * * * php /var/www/html/cronjobs/check_service_status.php >/dev/null 2>&1"; } | crontab -