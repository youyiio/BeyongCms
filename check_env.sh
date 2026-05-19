#!/bin/sh

basepath=$(cd `dirname $0`; pwd)

echo "Current Path: $basepath"

#minimize permissions
chown -R apache:apache $basepath/data/runtime
chown -R apache:apache $basepath/public/upload
