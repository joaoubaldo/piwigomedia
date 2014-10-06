#!/bin/bash
#
#
if [ "$1" == "" ]; then
    echo "Usage: $0 <version>"
    exit 2
fi

sed -i "s/Version: .*/Version: $1/g" piwigomedia.php
sed -i "s/Stable tag: .*/Stable tag: $1/g" readme.txt
cp -f readme.txt README.md
