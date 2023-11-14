#!/bin/bash

SUPPORTED_ENVS='dev test uat stage prod'
if [ $# -lt 3 ]; then
        echo "Usage - <your_pods_tarball_file_version_number> <environment (dev/test/uat/stage/prod)> <your_drupal_password> [wipe_db]"
        exit 1
fi

if [ $# -eq 4 ] ; then
  if [ "$4" == "wipe_db" ] ; then

    echo "All data will be deleted from the database";
    read -r -p "PODS will be rebuilt. All data will be lost. Are you sure? [Y/n] " input
    case $input in
      [yY][eE][sS]|[yY])
            echo "Rebuilding PODS including the database..."
            ;;
      [nN][oO]|[nN])
            exit 0
            ;;
      *)
            echo "Invalid input..."
            exit 1
            ;;

    esac
    #if here $wipe_db option was confirmed
    WIPE_DB="true"
  else
    echo "Invalid option: $4"
    exit 1
  fi
fi

declare -A envs=( ["dev"]="PODS-Dev.Dev" ["test"]="pods" ["uat"]="pods" ["stage"]="pods" ["prod"]="pods" )
ENVNAME="${envs[$2]}"
echo "PODS folder for deploy is: ${envs[$2]}" 2>&1|tee $LOGFILE


# Designate the log file.
LOGFILE="/app/upload/pods_$1_init.log"
UPGRADE_SCENARIO="false"

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

# Ensure that the required files have been uploaded to the /app/upload directory
if [[ ! -f /app/upload/pods_$1.tar.gz ]]; then
                echo "The file pods_$1.tar.gz does not exist in the /app/upload directory on this server."  2>&1|tee $LOGFILE
                echo "Please download pods_$1.tar.gz to the /app/upload directory and run this script again."  2>&1|tee $LOGFILE
                exit 1
# add check of upgrade option
elif [[ ! -f /app/upload/pods.$2.settings.php ]] && [[ $UPGRADE_SCENARIO == "false" ]]; then
                echo "The file pods.$2.settings.php does not exist in the /app/upload directory on this server."  2>&1|tee $LOGFILE
                echo "Please download pods.$2.settings.php to the /app/upload directory and run this script again."  2>&1|tee $LOGFILE
                exit 1
fi

# Since the required files exist in the /app/upload directory, we can proceed with installing PODS on the server.
if [[ "$SUPPORTED_ENVS" == *"$2"* ]]; then

        ENVNAME="${envs["$2"]}"

        if [[ "UPGRADE_SCENARIO" == "true" ]]; then
            echo "Enter Drupal maintenance mode" 2>&1|tee -a $LOGFILE
            /app/www/html/"$ENVNAME"/vendor/bin/drush sset system.maintenance_mode 1 2>&1|tee -a $LOGFILE
        fi

        cd /app/www/html/"$ENVNAME"

        echo "Extracting the PODS tarball file: /app/upload/pods_$1.tar.gz" 2>&1|tee -a $LOGFILE
        tar -xzf /app/upload/pods_$1.tar.gz 2>&1|tee -a $LOGFILE
                wait

        if [[ "$UPGRADE_SCENARIO" == "false" || $WIPE_DB == "true" ]]; then
            if [ $WIPE_DB == "true" ]; then
              echo "Restoring the backed up settings.php file" 2>&1|tee -a $LOGFILE
              cp -fp /app/upload/pods."$2".settings.php.bak /app/www/html/"$ENVNAME"/web/sites/default/settings.php 2>&1|tee -a $LOGFILE
            else
              echo "Copying the /app/upload/pods.$2.settings.php file to the settings file..." 2>&1|tee -a $LOGFILE
              cp -fp /app/upload/pods.$2.settings.php /app/www/html/"$ENVNAME"/web/sites/default/settings.php 2>&1|tee -a $LOGFILE
            fi
            echo "Running drush site-install command..." 2>&1|tee -a $LOGFILE
            /app/www/html/"$ENVNAME"/vendor/bin/drush site-install -y --existing-config --account-pass=$3 --site-name="NRCS Producer Operations Data System" farm farm.modules="base" 2>&1 | tee -a $LOGFILE
                wait

            # Do we really want to disable aggregation for css and js? 
            # Was this done for a workaround at one time and is no longer needed?
            echo "Running drush config-set for css.preprocess..." 2>&1|tee -a $LOGFILE
            /app/www/html/"$ENVNAME"/vendor/bin/drush  -y config-set  system.performance css.preprocess 0 2>&1|tee -a $LOGFILE
            echo "Running drush config-set for js.preprocess..." 2>&1|tee -a $LOGFILE
            /app/www/html/"$ENVNAME"/vendor/bin/drush -y config-set system.performance js.preprocess 0 2>&1|tee -a $LOGFILE

            echo "Running drush to clear and rebuild cache..." 2>&1|tee -a $LOGFILE
            /app/www/html/"$ENVNAME"/vendor/bin/drush cr 2>&1|tee -a $LOGFILE
                    wait

            echo "Running /app/www/html/"$ENVNAME"/vendor/bin/drush en -y structure_sync farm_map_mapbox cig_pods..." 2>&1|tee -a $LOGFILE
            /app/www/html/"$ENVNAME"/vendor/bin/drush en -y structure_sync farm_map_mapbox cig_pods 2>&1|tee -a $LOGFILE
                    wait

            echo "Importing taxonomies..." 2>&1|tee -a $LOGFILE
            /app/www/html/"$ENVNAME"/vendor/bin/drush import:taxonomies -y --choice=full 2>&1|tee -a $LOGFILE
                    wait
            echo "End of Taxonomy Import" 2>&1|tee -a $LOGFILE

        else
            # Recreate alive file link if needed
            if [[ ! -L /app/www/html/$ENVNAME/web/Alive1.html ]]; then
                echo "recreating alive file symbolic link"
                ln -s /app/httpd/htdocs/Alive1.html /app/www/html/$ENVNAME/web/Alive1.html 2>&1|tee -a $LOGFILE
            fi

            echo "copy settings.php from backup made earlier" 2>&1|tee -a $LOGFILE
            cp -fp /app/upload/pods.$2.settings.php.bak /app/www/html/"$ENVNAME"/web/sites/default/settings.php 2>&1|tee -a $LOGFILE

            echo "Run Drupal updates script" 2>&1|tee -a $LOGFILE
            /app/www/html/"$ENVNAME"/vendor/bin/drush updb 2>&1|tee -a $LOGFILE

            echo "Run config import"
            /app/www/html/"$ENVNAME"/vendor/bin/drush cim 2>&1|tee -a $LOGFILE
        fi

        echo "Changing application ownership to appadmin..." 2>&1|tee -a $LOGFILE
        chown -R appadmin:appadmin /app/www/html/"$ENVNAME" 2>&1|tee -a $LOGFILE

        echo "Running drush to clear and rebuild cache..." 2>&1|tee -a $LOGFILE
        /app/www/html/"$ENVNAME"/vendor/bin/drush cr 2>&1|tee -a $LOGFILE
        wait

        echo "Turn off Drupal maintenance mode" 2>&1|tee -a $LOGFILE
        /app/www/html/"$ENVNAME"/vendor/bin/drush sset system.maintenance_mode 0 2>&1|tee -a $LOGFILE

        echo "Script completed." 2>&1|tee -a $LOGFILE
        exit 0
fi
echo "Environment argument is not valid" 2>&1|tee -a $LOGFILE
exit 1