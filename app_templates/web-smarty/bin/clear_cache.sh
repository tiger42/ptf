#!/bin/sh

BASE_DIR=`cd "$(dirname "$0")" && pwd`/../var

rm -f ${BASE_DIR}/autoload_cache.php
rm -f ${BASE_DIR}/smarty/cache/*
rm -f ${BASE_DIR}/smarty/templates_c/*
