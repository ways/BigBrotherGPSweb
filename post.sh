URL="$1"

wget -O - --post-data 'secret=testname&accuracy=0&longitude=0&latitude=0' $URL
