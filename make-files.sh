#!/usr/bin/env bash

# On Ctrl+C (SIGINT) or SIGTERM, exit the script immediately with 130
set -Eeuo pipefail
trap 'echo; echo "Interrupted, stopping..."; exit 130' INT TERM

# Ensure output directory exists
mkdir -p ./dist

# # Loop from 1 to 54
# for i in {1..54}
# do
#   printf -v n "%02d" "$i"
#   echo "Generating card $n..."
#   php card.php --card="$i" | rsvg-convert -f png -z 2 | magick png:- -background white -flatten -profile ./sRGB.icc -profile ./SWOP2006_Coated3v2.icc ./dist/card-$n.jpg
# done

# Loop from 1 to {however many back there are}
for j in {1..13}
do
  printf -v n "%02d" "$j"
  echo "Generating back $n..."
  php back.php --card="$j" | rsvg-convert -f png -z 2 | magick png:- -background white -flatten -profile ./sRGB.icc -profile ./SWOP2006_Coated3v2.icc ./dist/back-$n.jpg
done

echo "All cards generated!"
