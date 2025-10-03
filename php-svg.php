<?php

/* +----------------------------------------------------------------+
 * |  Procedural library to generare SVG tags in PHP code           |
 * +----------------------------------------------------------------+
 */


function svg_eol(string $s, int $pad = 0): string {
  return str_repeat(' ', max(0, $pad)) . $s . PHP_EOL;
}


function svg_rect(float $x, float $y, float $w, float $h, float $rx = 0, float $ry =0): string {
  $ret = ['<rect'];
  if ($x != 0) $ret[] = 'x="' . $x . '"';
  if ($y != 0) $ret[] = 'y="' . $y . '"';
  if ($w != 0) $ret[] = 'width="' . $w . '"';
  if ($h != 0) $ret[] = 'height="' . $h . '"';
  if ($rx != 0) $ret[] = 'rx="' . $rx . '"';
  if ($ry != 0) $ret[] = 'ry="' . $ry . '"';
  $ret[] = '/>';
  return join(' ', $ret);
}


/**
 * Inject $a right after the tag name of an SVG/XML start tag.
 * Examples:
 *   inject_into_svg_tag('<rect width="10"/>', 'fill="red"')
 *     => '<rect fill="red" width="10"/>'
 *   inject_into_svg_tag('<g>', 'transform="translate(5,5)"')
 *     => '<g transform="translate(5,5)">'
 *
 * @param string $s A valid start tag like '<xxx ...>', '<xxx/>', '<xxx />'
 * @param string $a The text to inject after the element name (do not include leading space)
 * @return string The modified tag
 */
function svg_attrs(string $tag, string $attr): string {
    // Basic sanity checks, avoid touching closing tags or comments
    if ($attr === '' || !preg_match('/^<\s*[^!?\/]/', $tag)) {
        return $tag;
    }

    // Insert a single space plus $a right after the element name.
    // Pattern explanation:
    //   ^<\s*           start, '<', optional spaces
    //   [^\s>\/]+       the element name (no spaces, '>' or '/')
    //   \K              reset the starting point of the reported match
    //   (?=[\s>\/])     lookahead for space, '>' or '/' that begins '/>'
    //
    // So we replace at the exact insertion point, preserving the rest of the tag verbatim.
    return preg_replace(
        '/^<\s*[^\s>\/]+\K(?=[\s>\/])/',
        ' ' . trim($attr),
        $tag,
        1
    );
}


/**
 * Build an SVG path for a quarter-circle mask.
 *
 * @param float $x   Top-left x of the square
 * @param float $y   Top-left y of the square
 * @param float $len Side length of the square
 * @param int   $q   Quadrant: 1=TL, 2=TR, 3=BR, 4=BL
 * @return string    SVG path data
 */
function svg_rounded_corner_mask(float $x, float $y, float $len, int $q): string {
  $x0 = $x;
  $y0 = $y;
  $x1 = $x + $len;
  $y1 = $y + $len;

  switch ($q) {
    case 1: // top-left quarter circle
      $ret = sprintf(
        '%.3f %.3f A %.3f %.3f 0 0 1 %.3f %.3f L %.3f %.3f',
        $x0, $y0 + $len,   // start at bottom-left
        $len, $len,        // arc radii
        $x0 + $len, $y0,   // arc end (top-right of this quarter)
        $x0, $y0           // close back to top-left
      );
      break;

    case 2: // top-right quarter circle
      $ret = sprintf(
        '%.3f %.3f A %.3f %.3f 0 0 1 %.3f %.3f L %.3f %.3f',
        $x1 - $len, $y0,   // start at top-left of this quarter
        $len, $len,
        $x1, $y0 + $len,
        $x1, $y0
      );
      break;

    case 3: // bottom-left quarter circle
      $ret = sprintf(
        '%.3f %.3f A %.3f %.3f 0 0 1 %.3f %.3f L %.3f %.3f',
        $x0 + $len, $y1,
        $len, $len,
        $x0, $y1 - $len,
        $x0, $y1
      );
      break;

    case 4: // bottom-right quarter circle
      $ret = sprintf(
        '%.3f %.3f A %.3f %.3f 0 0 1 %.3f %.3f L %.3f %.3f',
        $x1, $y1 - $len,
        $len, $len,
        $x1 - $len, $y1,
        $x1, $y1
      );
      break;

    default:
      $ret = '';
      throw new InvalidArgumentException('Quadrant must be 1, 2, 3, or 4.');
  }
  return '<path d="M ' . $ret . ' Z" />';
}
