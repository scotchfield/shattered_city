<?php

function sc_inventory_content() {
    global $game, $character;

    if ( strcmp( 'inventory', $game->get_action() ) ) {
       return;
    }

    $item_obj = get_character_items_full( $character[ 'id' ] );

?><div class="row">
  <div class="col-md-6">
    <h3>Inventory</h3>
<ul>
<?php
    $burden = 0;

    foreach ( $item_obj as $item ) {
        echo( '<li><a href="#" onmouseover="popup(\'' .
              '<span class=&quot;item_name&quot;>' . $item[ 'name' ] .
              '</span>' .
              '<hr><span>' . $item[ 'description' ] . '</span>' .
              '\')" onmouseout="popout()" class="item">' . $item[ 'name' ] .
              '</a></li>' );
        $burden += $item[ 'weight' ];
    }

    if ( 0 == count( $item_obj ) ) {
        echo( '<h4>Nothing</h4>' );
    }

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_BURDEN, $burden );
    $character[ 'meta' ][ sc_meta_type_character ][ SC_BURDEN ] = $burden;

?>
</ul>
  </div>
</div>
<?php
}

add_action( 'do_page_content', 'sc_inventory_content' );
