<?php

function iterable_find($iterable, $f) {
  foreach ($iterable as $k => $v) {
    if (call_user_func($f, $v) === true) return [$k, $v];
  }
  return null;
}

?>
