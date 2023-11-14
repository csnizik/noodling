#!/bin/bash

###################################
# THIS SCRIPT IS A WORK IN PROGRESS
###################################
# USE AT YOUR OWN RISK
###################################
# REMOVE THESE COMMENTS UPON COMPLETION OFTHE WORK
###################################

set -ex

if ! [[ -d "../scripts" ]]
then
    echo "Run this from inside the scripts directory."
    exit 1
fi

# Get this script's path and parent directory.
SCRIPTPATH="$( cd "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )"
PARENTPATH="$(dirname $SCRIPTPATH)"

if [[ -f $PARENTPATH/build/pods_deploy.tar.gz ]]; then
    echo "Deleting last tar file"
    rm $PARENTPATH/build/pods_deploy.tar.gz
fi

cd .. # now just pods root

echo "Packaging tarball..."
tar -cvzf $PARENTPATH/build/pods_deploy.tar.gz  --exclude-from="$PARENTPATH/build/deploy_exclude.txt" *

echo "Done!"

exit 0