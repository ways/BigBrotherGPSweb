#!/bin/bash

# This script is part of https://github.com/ways/BigBrotherGPSweb
# and is GPL. Copyright 2014 Lars Falk-Petersen

# This will look up your aprox location using ipinfo.io and report
# it to a URL of your choice.

# TODO: use http://www.freedesktop.org/wiki/Software/GeoClue/ instead.

URL="$1"
verbose=""

usage () {
  cat << EOF
    Error: missing input.
    Usage: $0 http://example.com/
EOF
}

# Test input

[ -z $1 ] && {
  usage
  exit 1;
}

# Get info

ipinfo=$( curl --silent http://ipinfo.io )

[ "1" == "${verbose}" ] && \
  echo "${ipinfo}"

lat=$( echo "${ipinfo}" | grep 'loc' | cut -d'"' -f4 | cut -d, -f1 )
lon=$( echo "${ipinfo}" | grep 'loc' | cut -d'"' -f4 | cut -d, -f2 )

[ "1" == "${verbose}" ] && \
  echo $lon, $lat

# Battery
battery=$( /usr/bin/upower -i /org/freedesktop/UPower/devices/battery_BAT0 | grep 'percentage' | cut -d':' -f2 | cut -d',' -f1 | tr -d ' ' )

# Charging
charging=$( /usr/bin/upower -i /org/freedesktop/UPower/devices/battery_BAT0 | grep 'state'|grep -c 'charging' )

status=$( 
  /usr/bin/curl --silent --data \
  "secret=$( hostname )&accuracy=10000&longitude=${lon}&latitude=${lat}&battlevel=${battery}&charging=${charging}&provider=ipinfo.io&bearing=-1&speed=-1" \
  ${URL}
)

#Status
[ "1" == "${verbose}" ] && \
  echo ${status}

# Example data:
#curl ipinfo.io
#{
#  "ip": "178...",
#  "hostname": "hostname...",
#  "city": "Oslo",
#  "region": "Oslo",
#  "country": "NO",
#  "loc": "59.9167,10.7500",
#  "org": "org..."
#}
