URL="$1"

wget -O - --post-data 'secret=testname&accuracy=1&longitude=123&latitude=123&battery=100&charging=1' $URL
