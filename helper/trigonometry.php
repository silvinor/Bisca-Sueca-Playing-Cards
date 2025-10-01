<?php

function pt(float $x, float $y): array {
  return ['x' => $x, 'y' => $y];
}

function mv_pt($pt, float $d, int $a) {
  $r = deg2rad($a);
  $x = $pt['x'] + ($d * cos($r));
  $y = $pt['y'] - ($d * sin($r));
  return ['x' => $x, 'y' => $y];
}

function find_edge(array $pt, int $x1, int $y1, int $x2, int $y2, int $a): ?array {
  // Normalise rectangle to [xmin, xmax] × [ymin, ymax]
  $xmin = min($x1, $x2);
  $xmax = max($x1, $x2);
  $ymin = min($y1, $y2);
  $ymax = max($y1, $y2);

  $x0 = (float)$pt['x'];
  $y0 = (float)$pt['y'];

  // Direction vector using your angle convention (0° right, counter-clockwise positive)
  $r  = deg2rad($a);
  $dx = cos($r);
  $dy = -sin($r);  // screen coords: up is negative y

  $eps = 1e-9;
  $candidates = [];

  // Helper to push a candidate if within segment bounds and forward (t >= 0)
  $push = function(float $t, float $x, float $y, string $edge) use (&$candidates, $xmin, $xmax, $ymin, $ymax, $eps) {
    if ($t < -$eps) return; // behind the start
    // Clamp tiny numeric noise onto the edge
    if ($x < $xmin - $eps || $x > $xmax + $eps) return;
    if ($y < $ymin - $eps || $y > $ymax + $eps) return;
    $candidates[] = ['t' => max(0.0, $t), 'x' => $x, 'y' => $y, 'e' => $edge];
  };

  // Intersect with left/right edges: x = xmin or xmax
  if (abs($dx) > $eps) {
    $tL = ($xmin - $x0) / $dx;
    $yL = $y0 + $tL * $dy;
    $push($tL, $xmin, $yL, 'x1');

    $tR = ($xmax - $x0) / $dx;
    $yR = $y0 + $tR * $dy;
    $push($tR, $xmax, $yR, 'x2');
  }

  // Intersect with top/bottom edges: y = ymin or ymax
  if (abs($dy) > $eps) {
    $tT = ($ymin - $y0) / $dy;
    $xT = $x0 + $tT * $dx;
    $push($tT, $xT, $ymin, 'y1');

    $tB = ($ymax - $y0) / $dy;
    $xB = $x0 + $tB * $dx;
    $push($tB, $xB, $ymax, 'y2');
  }

  if (!$candidates) {
    return null; // no intersection in forward direction
  }

  // Choose the nearest forward intersection (smallest non-negative t)
  usort($candidates, function($a, $b) {
    if ($a['t'] == $b['t']) return 0;
    return ($a['t'] < $b['t']) ? 1 * -1 : 1;
  });

  // Return without 't' in the public result, but include which edge was hit
  $hit = $candidates[0];
  return ['x' => $hit['x'], 'y' => $hit['y'], 'e' => $hit['e']];
}
