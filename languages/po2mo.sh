#!/bin/sh
#

for file in `find . -name "*.po"` ; do msgfmt -o `echo $file | sed s/\.po/\.mo/` $file ; done
