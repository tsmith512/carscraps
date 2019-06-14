#!/bin/bash

cd ${0%/*}/..

httrack -O public/mirror --continue -w -iC1 -X0 -q --ext-depth=1 -x -s0 -e $1 "-*" "+https://images.craigslist.org/*" +"*.css" 2>&1 > /dev/null

grep '<title>' public/mirror/$(echo $1 | sed 's/^ht.\+\/\///') | sed 's/<\/\?title>//g'
