<?php
global $semaphore;

$semaphore = true;

include_once '../common.php';
include_once 'trigonometry.php';

function lib_validate_hex($card) {
  return ($card >= 1) && ($card <= 1);
}

function d_dot($pt) {
  printf('<circle cx="%s" cy="%s" r="8" fill="black" />' . PHP_EOL, $pt['x'], $pt['y']);
}

function d_line($p1, $p2, $sw = 16) {
  printf('<line x1="%s" y1="%s" x2="%s" y2="%s" stroke="black" stroke-width="%s" stroke-linecap="round" />' . PHP_EOL,
    $p1['x'], $p1['y'], $p2['x'], $p2['y'], $sw );
}

function d_half_circle($pt, $r, $sw = 21) {
  printf('<path d="M %3$s %2$s A %1$s %1$s 0 0 1 %4$s %2$s" fill="none" stroke="black" stroke-width="%5$s" />' . PHP_EOL,
    $r, $pt['y'], $pt['x']-$r, $pt['x']+$r, $sw);
}

function half_1_hex($card = 1) {
  $w = 750; $h = 1050; // bounding rect
  $r = 125;  // circle radius
  $hw = $w/2; $hh = $h/2; // half-width & -height
  $a1 = [15, 75, 135]; // angles
  $sp = 50; // spacer
  $c = pt($hw, $hh);

  // center circle
  d_half_circle($c, $r+10, 1);
  d_half_circle($c, $r-10, 1);

  // temp - start dots
  for ($i = 0; $i < 3; $i++) {
    $d1 = mv_pt($c, $r + $sp, 180-$a1[$i]);
    // d_dot( $d1 );

    $d2 = mv_pt($d1, $sp, 120-$a1[$i]);
    d_dot( $d2 );

    $d3 = find_edge($d2, 0, 0, $w, $h, 120-$a1[$i]);
    echo '<!-- '; var_dump($d3); echo ' -->' . PHP_EOL;
    d_dot( $d3 );

    $d4 = find_edge($d2, 0, 0, $w, $h, 60-$a1[$i]);
    echo '<!-- '; var_dump($d4); echo ' -->' . PHP_EOL;
    d_dot( $d4 );

    d_line( $d2, $d3, 1 );
    d_line( $d2, $d4, 1 );
  }

  // print('<g>');

  // print('</g>');
}

header('Content-Type: image/svg+xml; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="card.svg"');

?>
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 <?= CARD_CUT_LINE_W ?> <?= CARD_CUT_LINE_H ?>" version="1.1">
<?php

printf('<rect x="0" y="0" width="%s" height="%s" fill="none" stroke="red" stroke-width="1" />',
  CARD_CUT_LINE_W, CARD_CUT_LINE_H );
half_1_hex();

?>
</svg>
