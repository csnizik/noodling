#!/bin/bash

SUPPORTED_ENVS='dev test uat stage prod'
if [ $# -ne 2 ]; then
        echo "Usage - <your_pods_tarball_file_version_number> <environment (dev/test/uat/stage/prod)>"
        exit 1
fi

declare -A envs=( ["dev"]="PODS-Dev.Dev" ["test"]="pods" ["uat"]="pods" ["stage"]="pods" ["prod"]="pods" )
ENVNAME="${envs[$2]}"
echo "PODS folder for deploy is: ${envs[$2]}" 2>&1|tee $LOGFILE

# Designate the log file.
LOGFILE="/app/upload/pods_$1_update.log"
UPGRADE_SCENARIO="false"

# Ensure that the required files have been uploaded to the /app/upload directory
if [[ ! -f /app/upload/pods_$1.tar.gz ]]; then
		echo "The file pods_$1.tar.gz does not exist in the /app/upload directory on this server."  2>&1|tee $LOGFILE
		echo "Please download pods_$1.tar.gz to the /app/upload directory and run this script again."  2>&1|tee $LOGFILE
		exit 1
elif [[ ! -f /app/upload/pods.$2.settings.php ]]; then
		echo "The file pods.$2.settings.php does not exist in the /app/upload directory on this server."  2>&1|tee $LOGFILE
		echo "Please download pods.$2.settings.php to the /app/upload directory and run this script again."  2>&1|tee $LOGFILE
		exit 1
fi

# If there is already a Pods installation, make a backup of the existing settings file.
if [[ -f /app/www/html/"$ENVNAME"/web/sites/default/settings.php ]]; then
        UPGRADE_SCENARIO="true"
		echo "Creating a backup of the existing settings.php file." 2>&1|tee $LOGFILE
		cp -fp /app/www/html/"$ENVNAME"/web/sites/default/settings.php /app/upload/pods."$2".settings.php.bak
else
        echo "Creating $ENVNAME folder in /app/www/html" 2>&1|tee -a $LOGFILE
        mkdir /app/www/html/"$ENVNAME" 2>&1|tee -a $LOGFILE
        wait
fi

# Since the required files exist in the /app/upload directory, we can proceed with installing PODS on the server.
if [[ "$SUPPORTED_ENVS" == *"$2"* ]]; then

        ENVNAME="${envs["$2"]}"

        cd /app/www/html/"$ENVNAME"

        echo "Restoring the PODS tarball file: /app/upload/pods_$1.tar.gz" 2>&1|tee -a $LOGFILE
        tar -xzf /app/upload/pods_$1.tar.gz 2>&1|tee -a $LOGFILE
		wait

        if [[ "$UPGRADE_SCENARIO" == "false" ]]; then
            
            echo "Copying the /app/upload/pods.$2.settings.php file to the settings file..." 2>&1|tee -a $LOGFILE
            cp -fp /app/upload/pods.$2.settings.php /app/www/html/"$ENVNAME"/web/sites/default/settings.php 2>&1|tee -a $LOGFILE
        else
            # Recreate alive file link if needed
            if [[ ! -f /app/httpd/htdocs/Alive1.html ]]; then
                read -r -p "Please put the server back in tier, renaming the alive file back to Alive.html. Press enter when ready to continue:"
                if [[ ! -L /app/www/html/"$ENVNAME"/web/Alive1.html ]]; then
                    echo "recreating alive file symbolic link"
                    ln -s /app/httpd/htdocs/Alive1.html /app/www/html/$ENVNAME/web/Alive1.html 2>&1|tee -a $LOGFILE
                fi
            fi

            echo "copy settings.php from backup made earlier" 2>&1|tee -a $LOGFILE
            cp -fp /app/upload/pods.$2.settings.php.bak /app/www/html/"$ENVNAME"/web/sites/default/settings.php 2>&1|tee -a $LOGFILE
        fi

        echo "Changing application ownership to appadmin..." 2>&1|tee -a $LOGFILE
        chown -R appadmin:appadmin /app/www/html 2>&1|tee -a $LOGFILE

        echo "Running drush to clear and rebuild cache..." 2>&1|tee -a $LOGFILE
        /app/www/html/"$ENVNAME"/vendor/bin/drush cr 2>&1|tee -a $LOGFILE
        wait


        echo "Script completed." 2>&1|tee -a $LOGFILE
        exit 0
fi
echo "Environment argument is not valid" 2>&1|tee -a $LOGFILE
exit 1