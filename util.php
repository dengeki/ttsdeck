<?php

function iterable_find($iterable, $f) {
  foreach ($iterable as $k => $v) {
    if ($f($v) === true) return [$k, $v];
  }
  return null;
}

?>
