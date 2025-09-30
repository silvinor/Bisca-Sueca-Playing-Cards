<?php
global $semaphore;

include_once 'common.php';

function lib_validate_casino($card) {
  return ($card >= 1) && ($card <= 2);
}

function lib_comment_casino($card = 1) {
  echo "Casino style backs, card {$card}.";
}

function lib_style_casino($card = 1) {
?>
  .cf1 { fill: <?= ($card % 2 !== 0) ? COLOR1 : COLOR2 ?>; }
  .cs1 { stroke: <?= ($card % 2 !== 0) ? COLOR1 : COLOR2 ?>; }
<?php
}

function lib_defs_casino($card = 1) {
?>
  <g id="p" class="cf1">
    <path d="M2,75 L0,75 L0,73 L2,75 Z M6,63 L3,66 L9,72 L12,69 L6,63 Z M69,63 L63,69 L66,72 L72,66 L69,63 Z M11,58 L8,61 L14,67 L17,64 L11,58 Z M64,58 L58,64 L61,67 L67,61 L64,58 Z M16,53 L13,56 L19,62 L22,59 L16,53 Z M59,53 L53,59 L56,62 L62,56 L59,53 Z M21,48 L18,51 L24,57 L27,54 L21,48 Z M54,48 L48,54 L51,57 L57,51 L54,48 Z M26,43 L23,46 L29,52 L32,49 L26,43 Z M49,43 L43,49 L46,52 L52,46 L49,43 Z M31,38 L28,41 L34,47 L37,44 L31,38 Z M44,38 L38,44 L41,47 L47,41 L44,38 Z M38,35 L37,35 L35,37 L35,38 L37,40 L38,40 L40,38 L40,37 L38,35 Z M41,28 L38,31 L44,37 L47,34 L41,28 Z M34,28 L28,34 L31,37 L37,31 L34,28 Z M46,23 L43,26 L49,32 L52,29 L46,23 Z M29,23 L23,29 L26,32 L32,26 L29,23 Z M51,18 L48,21 L54,27 L57,24 L51,18 Z M24,18 L18,24 L21,27 L27,21 L24,18 Z M56,13 L53,16 L59,22 L62,19 L56,13 Z M19,13 L13,19 L16,22 L22,16 L19,13 Z M61,8 L58,11 L64,17 L67,14 L61,8 Z M14,8 L8,14 L11,17 L17,11 L14,8 Z M66,3 L63,6 L69,12 L72,9 L66,3 Z M9,3 L3,9 L6,12 L12,6 L9,3 Z M75,0 L75,2 L73,0 L75,0 Z M75,73 L75,75 L73,75 L75,73 Z M75,68 L74,68 L68,74 L68,75 L7,75 L7,74 L1,68 L0,68 L0,7 L1,7 L7,1 L7,0 L68,0 L68,1 L74,7 L75,7 L75,68 Z M0,0 L2,0 L0,2 L0,0 Z" />
  </g>
  <g id="b">
<?php
  $k = 75;
  for ($i = 0; $i < ((CARD_CUT_LINE_W/$k)/2); $i++) {
    for ($j = 0; $j < ((CARD_CUT_LINE_H/$k)/2); $j++) {
      printf('    <use href="#p" transform="translate(%s,%s)" />' . PHP_EOL,
        ($i * $k),
        ($j * $k)
      );
    }
  }
?>
  </g>
<?php
}

function quadrant_ea_casino() {
  echo '<use href="#b" />';
}

function quadrant_1_casino($card = 1) { quadrant_ea_casino(); }
function quadrant_2_casino($card = 1) { quadrant_ea_casino(); }
function quadrant_3_casino($card = 1) { quadrant_ea_casino(); }
function quadrant_4_casino($card = 1) { quadrant_ea_casino(); }
