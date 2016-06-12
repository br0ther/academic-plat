#!/bin/sh

WEBPATH=/var/www/plat-app

rm -rf $WEBPATH/app/cache/prod/*
rm -rf $WEBPATH/app/cache/dev/*
rm -rf $WEBPATH/app/cache/test/*

