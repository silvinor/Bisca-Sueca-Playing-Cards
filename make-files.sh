#!/usr/bin/env bash

# On Ctrl+C (SIGINT) or SIGTERM, exit the script immediately with 130
set -Eeuo pipefail
trap 'echo; echo "Interrupted, stopping..."; exit 130' INT TERM

# Ensure output directory exists
mkdir -p ./dist

# Loop from 1 to 54
for i in {1..54}
do
  echo "Generating card $i..."
  php card.php --card="$i" | rsvg-convert -f png -z 2 | magick png:- -background white -flatten -profile ./sRGB.icc -profile ./SWOP2006_Coated3v2.icc ./dist/card-$i.jpg
done

# Loop from 1 to 8
for j in {1..8}
do
  echo "Generating back $j..."
  php back.php --card="$j" | rsvg-convert -f png -z 2 | magick png:- -background white -flatten -profile ./sRGB.icc -profile ./SWOP2006_Coated3v2.icc ./dist/back-$j.jpg
done

echo "All cards generated!"
