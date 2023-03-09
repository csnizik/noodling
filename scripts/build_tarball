#!/bin/bash

if [ $# -eq 0 ]
  then
    echo "No version supplied: ./build_tarball <version>"
    exit 1
fi

if ! [[ -d "../scripts" ]]
then
    echo "Run this from inside the scripts directory."
    exit 1
fi

echo "Installing patch utility..."
dnf install -y patch

echo "Installing composer..."
wget https://getcomposer.org/installer -O composer-installer.php
php composer-installer.php --filename=composer --install-dir=/usr/local/bin

cd .. # now just pods source

echo "Preparing files for build..."
cd ..
mkdir -p pods/web/modules/custom
cp cig_pods/composer.json pods
cp -r cig_pods/patches/ pods

echo "Copying custom modules..."
cp -r cig_pods/web/modules/custom/* pods/web/modules/custom


echo "Building with composer..."
cd pods
export COMPOSER_ALLOW_SUPERUSER=1
/usr/local/bin/composer install

echo "Packaging tarball..."
cd ..
tar -cz pods/ > cig_pods/scripts/pods_$1.tar.gz

echo "Cleaning up..."
rm -rf cig_pods/scripts/composer-installer.php pods/

echo "Done!"
