#!/bin/bash

cd ${0%/*}

cp htdocs/queue.txt ./queue.txt
echo "" > htdocs/queue.txt

while read -r line || [[ -n "$line" ]]; do
  if [ ! -z "$line" ]
  then
    httrack -O htdocs/mirror --continue -w -iC1 -X0 -q --ext-depth=1 -x -s0 -e $line "-*" "+https://images.craigslist.org/*" +"*.css"
    echo "Downloaded: $line"
  fi
done < queue.txt

cat queue.txt >> expired.txt
rm queue.txt

sed -i '/HTTP-EQUIV="Refresh"/d' htdocs/mirror/index.html
