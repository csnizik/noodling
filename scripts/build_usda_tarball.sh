#!/bin/bash
set -ex

if ! [[ -d "../scripts" ]]
then
    echo "Run this from inside the scripts directory."
    exit 1
fi

# Get this script's path and parent directory.
SCRIPTPATH="$( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )"
PARENTPATH="$(dirname $SCRIPTPATH)"

if [[ -f $PARENTPATH/pods.tar.gz ]]; then
    echo "Deleting last tar file"
    rm $PARENTPATH/pods.tar.gz
fi

cd .. # now just pods source

echo "Preparing files for build..."
cd ..


# Delete the folder if it exists from a previous run
if [[ -d pods ]]; then
    echo "Deleting folder form previous attempt"
    rm -rf pods
fi

# Create new empty folders
echo "Creating assembly folder"
mkdir -p pods/web/modules/custom

cp -v $PARENTPATH/composer.json pods
cp -v $PARENTPATH/composer.lock pods

cp -rv $PARENTPATH/patches/ pods
cp -rv $PARENTPATH/config/ pods
cp -rv $PARENTPATH/scripts/ pods

echo "Copying custom modules..."
cp -rv $PARENTPATH/web/modules/custom/* pods/web/modules/custom
cp -rv $PARENTPATH/web/ pods

cd pods

echo "Looking for composer here"
ls -f /usr/local/bin

echo "Building with composer..."

/usr/local/bin/composer install
wait

echo "copy vendor folder"
cp -rv $PARENTPATH/vendor/ pods
echo "Packaging tarball..."
tar -czf $PARENTPATH/pods.tar.gz .

echo "Done!"

exit 0