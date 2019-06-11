#!/bin/bash

cd ${0%/*}/..

cp public/queue.txt var/queue.txt
echo "" > public/queue.txt

while read -r line || [[ -n "$line" ]]; do
  if [ ! -z "$line" ]
  then
    httrack -O public/mirror --continue -w -iC1 -X0 -q --ext-depth=1 -x -s0 -e $line "-*" "+https://images.craigslist.org/*" +"*.css"
    echo "Downloaded: $line"
  fi
done < var/queue.txt

cat public/queue.txt >> public/expired.txt
rm var/queue.txt

sed -i '/HTTP-EQUIV="Refresh"/d' public/mirror/index.html

grep -ri '<title>' public/mirror/ | grep -v "external.html" | grep -v "index.html" > public/index.txt
