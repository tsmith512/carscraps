#!/bin/bash

cd ${0%/*}/..

httrack -O public/mirror --continue -w -iC1 -X0 -q --ext-depth=1 -x -s0 -e $1 "-*" "+https://images.craigslist.org/*" +"*.css"

sed -i '/HTTP-EQUIV="Refresh"/d' public/mirror/index.html

grep -ri '<title>' public/mirror/ | grep -v "external.html" | grep -v "index.html"
