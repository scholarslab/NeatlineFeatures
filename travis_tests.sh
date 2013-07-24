#! /usr/bin/env bash

if [ -z $PLUGIN_DIR ]; then
PLUGIN_DIR=`pwd`
fi

if [ -z $OMEKA_DIR ]; then
export OMEKA_DIR=`pwd`/omeka
  echo "omeka_dir set"
fi

echo "Plugin Directory: $PLUGIN_DIR"
echo "Omeka Directory: $OMEKA_DIR"

cd tests/ && phpunit --configuration phpunit_travis.xml --coverage-text
ec1=$?

echo
echo "Feature Tests"
cd ..
export OMEKA_HOST=http://localhost
export OMEKA_USER=features
export OMEKA_PASSWD=features
export OMEKA_MYSQL=null
# cucumber --profile default --tags @current
cucumber --profile default
ec2=$?

# This fails if either test failed.
[[ "$ec1" -eq "0" && "$ec2" -eq "0" ]]

