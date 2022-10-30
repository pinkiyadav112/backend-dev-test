#!/bin/zsh

# Initialize a wordpress recipe using the latest WordPress version
lando init \
  --source remote \
  --remote-url https://wordpress.org/latest.tar.gz \
  --recipe wordpress \
  --webroot wordpress \
  --name my-first-wordpress-app

# Start it up
lando start

# List information about this app
lando info

# Create a WordPress config file
lando wp config create \
  --dbname=wordpress \
  --dbuser=wordpress \
  --dbpass=wordpress \
  --dbhost=database \
  --path=wordpress

# Install WordPress
lando wp core install \
  --url=https://my-first-wordpress-app.lndo.site/ \
  --title="My First Wordpress App" \
  --admin_user=admin \
  --admin_password=password \
  --admin_email=admin@my-first-wordpress-app.lndo.site \
  --path=wordpress