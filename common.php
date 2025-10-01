<?php
global $semaphore, $card, $debug;

if (!isset($semaphore)) {
  echo 'Not to be called standalone.' . PHP_EOL;
  exit(1);
}

// absolute size (full size, incl. bleed)
const CARD_BLEED_W = 822;
const CARD_BLEED_H = 1122;
// size of backs (??)
const CARD_CUT_LINE_W = 750;
const CARD_CUT_LINE_H = 1050;
// size of safe printable area
const CARD_SAFE_W = 690;
const CARD_SAFE_H = 990;

const COLOR0 = '#010101'; // Just 1-bit, because why not.
const COLOR1 = '#0070B8'; // Spanish Blue
const COLOR2 = '#E60026'; // Spanish Red
const COLOR3 = '#00B050'; // High contrast vs. others, as suggested by ChatGTP
const COLOR4 = '#7851a9'; // Royal Purple

/* ---------- Functions ---------- */

function is_true($val) {
  if (is_bool($val)) {
    return !!$val;
  } else if (is_numeric($val)) {
    return $val > 0;
  } else if (is_string($val)) {
    return filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
  } else
    return boolval($val);
}

function render_debug() {
  print('<g id="debug" class="DEBUG" stroke-width="0.5">');
  printf('<rect id="full_w_bleed" fill="none" stroke="#0F0" x="0" y="0" width="%s" height="%s" />',
    CARD_BLEED_W, CARD_BLEED_H);
  printf('<rect id="cut_line" fill="none" stroke="#00F" x="%s" y="%s" width="%s" height="%s" rx="%s" />',
    (CARD_BLEED_W - CARD_CUT_LINE_W) / 2,
    (CARD_BLEED_H - CARD_CUT_LINE_H) / 2,
    CARD_CUT_LINE_W,
    CARD_CUT_LINE_H,
    38
    );
  printf('<rect id="safe_line" fill="none" stroke="#F00" x="%s" y="%s" width="%s" height="%s" rx="%s" stroke-dasharray="6 6" />',
    (CARD_BLEED_W - CARD_SAFE_W) / 2,
    (CARD_BLEED_H - CARD_SAFE_H) / 2,
    CARD_SAFE_W,
    CARD_SAFE_H,
    0
    );
  print('</g>');
}

/* ---------- Parse command line or html query ---------- */

$card = 0;
$debug = false;

$is_command_line = php_sapi_name() === 'cli' || defined('STDIN');
if ($is_command_line) {
  // command line
  $opts = array_change_key_case( getopt('', ['card:', 'debug::']), CASE_LOWER );
  $opts['debug'] = (isset($opts['debug']) ? ($opts['debug'] === false) ? true : is_true($opts['debug']) : false);
} else {
  // html get or put
  $opts = array_change_key_case( $_REQUEST, CASE_LOWER );
  foreach ($opts as $key => $value) {
    if (is_string($opts[$key]) && ($opts[$key] === '')) $opts[$key] = true;
  }
}
if (isset($opts['debug'])) {
  $debug = is_true($opts['debug']);
}

if (isset($opts['card'])) {
  $i = $opts['card'];
  if (is_numeric($i)) {
    $card = intval($i);
  }
}
