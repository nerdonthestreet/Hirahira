<?php

function updateUrl($parameter, $value) {
 $params = $_GET;
 $params[$parameter] = $value;
 return http_build_query($params);
}

?>
