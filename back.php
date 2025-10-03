<?php
global $semaphore, $card, $debug;
$semaphore = __FILE__;

include_once 'common.php';
include_once 'php-svg.php';

// **********************************************************************
// ----- General Notes -----
// Backs are:    750 x 1050 (CARD_CUT_LINE_W x CARD_CUT_LINE_H)
// Divisors are: 1, 2, 3, 5, 6, 10, 15, 25, 30, 50, 75, 150
// **********************************************************************

// ---------- Code starts here ---------

$x = file_get_contents(PARTS_FOLDER . "back.json");
$backs = json_decode($x, true);
if (json_last_error() !== JSON_ERROR_NONE) {
  echo "Error decoding JSON: " . json_last_error_msg() . PHP_EOL;
  exit(2);
}
$x = [];
foreach ($backs as $key => $value) {
  if (is_numeric($key) && $key > 0) $x[$key] = $value;
}
$backs = $x;

if (!isset($backs[$card])) {
  echo
    'You must specify a card number in the rage: ' .
    implode(", ", array_keys($backs)) .
    '.' . PHP_EOL;
  echo '<br>Usage: ';
  if ($is_command_line) {
    echo 'php ' . basename(__FILE__) . ' --card=n' . PHP_EOL;
  } else {
    echo basename(__FILE__) . '?card=n' . PHP_EOL;
  }
  exit(1);
}

$fn = isset($backs[$card]['lib']) ? (PARTS_FOLDER.'back-'.$backs[$card]['lib'].'.php') : false;
if (($fn !== false) && file_exists($fn) && is_file($fn)) {
  @include_once $fn;
} else {
  echo "Error: The file '{$fn}' could not be found." . PHP_EOL;
  exit(2);
}
$wantCard = isset($backs[$card]['ofs']) ? $card + $backs[$card]['ofs'] : $card;
$fu = 'lib_validate_' . $backs[$card]['lib'];
if (function_exists($fu)) {
  if (!$fu($wantCard)) {
    echo "Invalid card number ('{$card}') for the `{$fn}` library." . PHP_EOL;
    exit(3);
  }
} else {
  echo "Library `{$fn}` does not have a validation function." . PHP_EOL;
  exit(4);
}
// $isBorderless = isset($backs[$card]['bls']) ? !!$backs[$card]['bls'] : false;
$isMirrored = isset($backs[$card]['mir']) ? !!$backs[$card]['mir'] : false;

header('Content-Type: image/svg+xml; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="card.svg"');

?>
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 <?= CARD_BLEED_W ?> <?= CARD_BLEED_H ?>" version="1.1">
<?php
$fu = 'lib_comment_' . $backs[$card]['lib'];
if (function_exists($fu)) {
  echo '<!-- ';
  $fu($wantCard);
  echo ' -->' . PHP_EOL;
}
$fu = 'lib_style_' . $backs[$card]['lib'];
if (function_exists($fu)) {
  echo '<style>' . PHP_EOL;
  $fu($wantCard);
  echo '</style>' . PHP_EOL;
}
$fu = 'lib_defs_' . $backs[$card]['lib'];
if (function_exists($fu)) {
  echo '<defs>' . PHP_EOL;
  $fu($wantCard);
  echo '</defs>' . PHP_EOL;
}

// ----- Card face -----
$i = (CARD_BLEED_W - CARD_CUT_LINE_W) / 2;
printf('<rect id="print" stroke="none" fill="#FFF" x="%s" y="%s" width="%s" height="%s" rx="%s" />' . PHP_EOL,
  $i, $i,
  CARD_CUT_LINE_W, CARD_CUT_LINE_H,
  ($i * 1.5)
  );

// ----- Back -----

// new $isBorderless handling

function scale_to_safe_line() {
  $ox = (CARD_BLEED_W - CARD_SAFE_W) / 2;
  $oy = (CARD_BLEED_H - CARD_SAFE_H) / 2;
  $sx = round(CARD_SAFE_W / CARD_CUT_LINE_W, 5);
  $sy = round(CARD_SAFE_H / CARD_CUT_LINE_H, 5);
  return sprintf('transform="translate(%s,%s) scale(%s,%s)"', $ox, $oy, $sx, $sy );
}

function scale_to_bleed_line() {
  $enlg = 1.05; // enlarge to encroach on the cut-line
  $ox = (CARD_BLEED_W - (CARD_CUT_LINE_W * $enlg)) / 2;
  $oy = (CARD_BLEED_H - (CARD_CUT_LINE_H * $enlg)) / 2;
  return sprintf('transform="translate(%s,%s) scale(%3$s,%3$s)"', $ox, $oy, $enlg );
}

global $border_scale_function;
if (!isset($border_scale_function)) $border_scale_function = 'scale_to_safe_line';

print('<g id="back" ');
print( $border_scale_function() );
print('>' . PHP_EOL);
for ($i = 0; $i < 2; $i++) {
  printf('  <g id="half_%s"', ($i+1));
  if ($i != 0) {
    printf(' transform="rotate(180 %s %s)"',
      CARD_CUT_LINE_W / 2,
      CARD_CUT_LINE_H / 2
    );
  }
  print('>' . PHP_EOL);
  for ($j = 0; $j < 2; $j++) {
    $q = ($j + 1) + ($i * 2);
    $fu = 'quadrant_' . $q . '_' . $backs[$card]['lib'];
    if (function_exists($fu)) {
      printf('    <g id="quad_%s"', ($q));
      if ($j != 0) {
        if ($isMirrored) {
          printf(' transform="translate(%s,0) scale(-1, 1)"',
            CARD_CUT_LINE_W
          );
        } else {
          printf(' transform="rotate(180 %s %s)"',
            CARD_CUT_LINE_W / 2,
            CARD_CUT_LINE_H / 4
          );
        }
      }
      print('>');
      $fu($wantCard);

      // if ($debug) {
      //   printf('<rect class="DEBUG" fill="none" stroke="#F00D" stroke-width="1" x="%s" y="%s" width="%s" height="%s" stroke-dasharray="6 6" />',
      //     0, 0, CARD_CUT_LINE_W/2, CARD_CUT_LINE_H/2 );
      //   printf('<text class="DEBUG" x="%s" y="%s" text-anchor="middle" dominant-baseline="middle" fill="#F005" font-family="serif" font-size="%s" font-weight="bold">27</text>',
      //     CARD_CUT_LINE_W/4, CARD_CUT_LINE_H/4, CARD_CUT_LINE_W/2 );
      // }

      print('</g>' . PHP_EOL);
    }
  }

  // Halves
  $fu = 'half_' . ($i + 1) . '_' . $backs[$card]['lib'];
  if (function_exists($fu)) {
    $fu($wantCard);
  }

  print('  </g>' . PHP_EOL);
}
echo '</g>' . PHP_EOL;


// ----- Bordered frame -----

function draw_border_frame() {
  global $card;

  $s = 6;
  echo '<g id="border" fill="none" stroke-linecap="round" stroke-linejoin="round">' . PHP_EOL;
  $k = (CARD_CUT_LINE_W - CARD_SAFE_W ) / 2;
  $r = 38 - ($k / 2);
  $wf = 'fill="#FFF" stroke="#FFF" stroke-width="0.5"';
  $ws = sprintf('fill="none" stroke="#FFF" stroke-width="%s"', $k);

  print svg_attrs(svg_rect(
    (CARD_BLEED_W - CARD_CUT_LINE_W + $k) / 2,
    (CARD_BLEED_H - CARD_CUT_LINE_H + $k) / 2,
    CARD_CUT_LINE_W - $k,
    CARD_CUT_LINE_H - $k,
    $r
  ), $ws );

  $x = 38 + 3;
  print svg_attrs(svg_rounded_corner_mask(
    (CARD_BLEED_W - CARD_SAFE_W) / 2,
    (CARD_BLEED_H - CARD_SAFE_H) / 2,
    $x,
    1
  ), $wf );
  print svg_attrs(svg_rounded_corner_mask(
    ((CARD_BLEED_W - CARD_SAFE_W) / 2) + CARD_SAFE_W - $x,
    (CARD_BLEED_H - CARD_SAFE_H) / 2,
    $x,
    2
  ), $wf );
  print svg_attrs(svg_rounded_corner_mask(
    (CARD_BLEED_W - CARD_SAFE_W) / 2,
    ((CARD_BLEED_H - CARD_SAFE_H) / 2) + CARD_SAFE_H - $x,
    $x,
    3
  ), $wf );
  print svg_attrs(svg_rounded_corner_mask(
    ((CARD_BLEED_W - CARD_SAFE_W) / 2) + CARD_SAFE_W - $x,
    ((CARD_BLEED_H - CARD_SAFE_H) / 2) + CARD_SAFE_H - $x,
    $x,
    4
  ), $wf );

  print svg_attrs(svg_rect(
    (CARD_BLEED_W - CARD_SAFE_W + $s) / 2,
    (CARD_BLEED_H - CARD_SAFE_H + $s) / 2,
    CARD_SAFE_W - $s,
    CARD_SAFE_H - $s,
    38
  ), sprintf('class="%s" stroke-width="%s"',
    'cs1',
    $s
  ) );

  $z = $s * 2;
  print svg_attrs(svg_rect(
    ((CARD_BLEED_W - CARD_SAFE_W + $z) / 2) + $s,
    ((CARD_BLEED_H - CARD_SAFE_H + $z) / 2) + $s,
    CARD_SAFE_W - ($z + ($s * 2)),
    CARD_SAFE_H - ($z + ($s * 2)),
    38 - ($s + ($s / 2)),
  ), sprintf('fill="none" stroke="#FFF" stroke-width="%s"',
    $z
  ) );

  echo '</g>' . PHP_EOL;
}

global $border_draw_function; // set to false in the library file if you want to skip this
if (!isset($border_draw_function)) { $border_draw_function = 'draw_border_frame'; } // default
if (!empty($border_draw_function) && !is_null($border_draw_function)) $border_draw_function();

if ($debug) render_debug();

?>
</svg>
