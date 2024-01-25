<?php

$path = preg_replace( '/wp-content.*$/', '', __DIR__ );
include( $path . 'wp-load.php' );

$attributes = json_decode(json_encode(wc_get_attribute_taxonomies()), true);
sort($attributes);

foreach ($attributes as $key => $attribute) {
    $deleted = wc_delete_attribute($attribute['attribute_id']);
    echo '<pre>';
    print_r(sprintf('Deleting %s - Result %s', $attribute['attribute_label'], $deleted));
    echo '</pre>';


}
