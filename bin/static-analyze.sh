#!/usr/bin/env bash

DIR=`dirname $0`

#ln -s `realpath $DIR/../` $SWDIR/custom/plugins/EasyCreditRatenkauf

php $DIR/phpstan-config-generator.php

php $DIR/../vendor/bin/phpstan analyze src
