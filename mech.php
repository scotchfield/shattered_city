<?php

function sc_mech_get_slots() {
    return array(
        'h' => 'Head',
        'ls' => 'Left Shoulder',
        'rs' => 'Right Shoulder',
        'lh' => 'Left Hand',
        'rh' => 'Right Hand',
        'ca' => 'Chest',
        'cb' => 'Chest',
        'cc' => 'Chest',
        'lt' => 'Left Thigh',
        'rt' => 'Right Thigh',
        'lf' => 'Left Foot',
        'rf' => 'Right Foot',
    );
}

function sc_mech_content() {
    global $game, $character;

    if ( strcmp( 'mech', $game->get_action() ) ) {
       return;
    }

    ensure_character_mech();
    $item_obj = get_character_mech_items();
    $key_obj = sc_mech_get_slots();

?>
<h2 class="text-center">Active Mech</h2>
<div class="row">
  <div class="col-md-2"></div>
  <div class="col-md-4">
    <h3 class="text-center">Details</h3>
    <h4><span class="list_key">Model:</span> <span class="list_value"><?php
        echo( $character[ 'mech' ][ 'model' ] ); ?></span></h4>
    <h4><span class="list_key">Maker:</span> <span class="list_value"><?php
        echo( $character[ 'mech' ][ 'maker' ] ); ?></h4>
    <h4><span class="list_key">Health:</span> <span class="list_value"><?php
        echo( $character[ 'mech' ][ 'health' ] ); ?></h4>
    <h4><span class="list_key">Cost:</span> <span class="list_value"><?php
        echo( $character[ 'mech' ][ 'value' ] ); ?></h4>
    <h4><span class="list_key">Armour:</span> <span class="list_value"><?php
        echo( $character[ 'mech' ][ 'armour' ] ); ?></h4>
    <h4><span class="list_key">Heat:</span> <span class="list_value"><?php
    $heat = round( ( 100.0 * $character[ 'mech' ][ 'heat' ] ) /
                   $character[ 'mech' ][ 'heat_max' ] );
    echo( $heat ); ?>%</h4>

<!--    <dl class="dl-horizontal">
      <dt>Model</dt>
      <dd><?php echo $character[ 'mech' ][ 'model' ]; ?>&nbsp;</dd>
      <dt>Maker</dt>
      <dd><?php echo $character[ 'mech' ][ 'maker' ]; ?>&nbsp;</dd>
      <dt>Health</dt>
      <dd><?php echo $character[ 'mech' ][ 'health' ]; ?>&nbsp;</dd>
      <dt>Value</dt>
      <dd><?php echo $character[ 'mech' ][ 'value' ]; ?>&nbsp;</dd>
      <dt>Armour</dt>
      <dd><?php echo $character[ 'mech' ][ 'armour' ]; ?>&nbsp;</dd>
      <dt>Heat</dt>
      <dd><?php echo round( $character[ 'mech' ][ 'heat' ], 2 ); ?>&nbsp;</dd>
    </dl>-->
  </div>
  <div class="col-md-6">
    <h3 class="text-center">Configuration</h3>
    <dl class="dl-horizontal">
<?php
    $last_v = '';
    foreach ( $key_obj as $k => $v ) {
        if ( strcmp( $last_v, $v ) ) {
            echo '<dt>' . $v . '</dt>';
        }
        $last_v = $v;
        echo '<dd>' . sc_item_popup_str(
             $item_obj[ $character[ 'mech' ][ $k ] ] ) . '</dd>';
    }
?>
    </dl>
  </div>
</div>
<?php
}

add_action( 'do_page_content', 'sc_mech_content' );

function sc_mech_dissipate_heat() {
    global $character, $mech_update_meta;

    if ( FALSE == $character ) {
        return;
    }

    ensure_character_mech();

    if ( ! isset( $character[ 'mech' ][ 'heat' ] ) ) {
        return;
    }

    if ( 0 >= $character[ 'mech' ][ 'heat' ] ) {
        return;
    }

    $mech_update_meta = TRUE;

    if ( isset( $character[ 'mech' ][ 'heat_timestamp' ] ) ) {

        $heat_seconds = time() - $character[ 'mech' ][ 'heat_timestamp' ];
        $heat_loss = ( $heat_seconds / 60 ) * 2;
        $character[ 'mech' ][ 'heat' ] = max( 0,
            $character[ 'mech' ][ 'heat' ] - $heat_loss );

    }

    $character[ 'mech' ][ 'heat_timestamp' ] = time();
}

add_action( 'character_load', 'sc_mech_dissipate_heat' );

function sc_mech_regen_health() {
    global $character, $mech_update_meta;

    if ( FALSE == $character ) {
        return;
    }

    ensure_character_mech();

    if ( ! isset( $character[ 'mech' ][ 'health' ] ) ) {
        return;
    }

    if ( $character[ 'mech' ][ 'health' ] >=
         $character[ 'mech' ][ 'health_max' ] ) {
        return;
    }

    $mech_update_meta = TRUE;

    if ( isset( $character[ 'mech' ][ 'health_timestamp' ] ) ) {

        $health_seconds = time() - $character[ 'mech' ][ 'health_timestamp' ];
        $health_gain = floor( $health_seconds / 60 );
        $character[ 'mech' ][ 'health' ] = min(
            $character[ 'mech' ][ 'health_max' ],
            $character[ 'mech' ][ 'health' ] + $health_gain );

    }

    $character[ 'mech' ][ 'health_timestamp' ] = time();
}

add_action( 'character_load', 'sc_mech_regen_health' );


function sc_repair_mech( $args ) {
    global $character;

    ensure_character_mech();

    $damage = $character[ 'mech' ][ 'health_max' ] -
        $character[ 'mech' ][ 'health' ];
    $damage_cost = $damage * 1; // todo: real number...

    $credits = character_meta(
        sc_meta_type_character, SC_CHARACTER_CREDITS );

    if ( $credits >= $damage_cost ) {
        $character[ 'mech' ][ 'health' ] =
            $character[ 'mech' ][ 'health_max' ];
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_CHARACTER_CREDITS, $credits - $damage_cost );

        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_CHARACTER_TIP, '<div class="text-center">Repaired ' .
                $damage . ' points of damage at a cost of ' .
                $damage_cost . ' credits.</div>' );

        sc_pack_mech();
    }

    $GLOBALS[ 'redirect_header' ] = GAME_URL .
        '?action=zone&zone_tag=minstall';
}

$custom_setting_map[ 'repair_mech' ] = 'sc_repair_mech';

function sc_remove_equipment( $args ) {
    global $character;

    ensure_character_mech();

    $credits = character_meta(
        sc_meta_type_character, SC_CHARACTER_CREDITS );

    if ( 100 > $credits ) {
        return;
    }

    if ( ( ! isset( $args[ 'slot' ] ) ) ||
         ( ! isset( $character[ 'mech' ][ $args[ 'slot' ] ] ) ) ) {
        return;
    }

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_CHARACTER_CREDITS, $credits - 100 );

    add_character_item( $character[ 'id' ],
        $character[ 'mech' ][ $args[ 'slot' ] ], '' );
    $character[ 'mech' ][ $args[ 'slot' ] ] = 0;

    sc_pack_mech();

    $GLOBALS[ 'redirect_header' ] = GAME_URL .
        '?action=zone&zone_tag=minstall';
}

$custom_setting_map[ 'remove_equipment' ] = 'sc_remove_equipment';


function sc_add_equipment( $args ) {
    global $character;

    ensure_character_mech();

    if ( ( ! isset( $args[ 'item_id' ] ) ) ||
         ( ! isset( $args[ 'slot' ] ) ) ||
         ( ! isset( $character[ 'mech' ][ $args[ 'slot' ] ] ) ) ) {
        return;
    }

    if ( 0 != $character[ 'mech' ][ $args[ 'slot' ] ] ) {
        return;
    }

    $credits = character_meta(
        sc_meta_type_character, SC_CHARACTER_CREDITS );

    if ( 100 > $credits ) {
        return;
    }

    $item_id = intval( $args[ 'item_id' ] );
    $item_obj = get_character_items_full( $character[ 'id' ] );

    if ( ! isset( $item_obj[ intval( $args[ 'item_id' ] ) ] ) ) {
        return;
    }

    $item = $item_obj[ intval( $args[ 'item_id' ] ) ];
    $item_meta = explode_meta( $item[ 'item_meta' ] );

    if ( ! isset( $item_meta[ 'slot' ] ) ) {
        return;
    }

    $slot_obj = explode( ',', $item_meta[ 'slot' ] );

    if ( ! in_array( $args[ 'slot' ], $slot_obj ) ) {
        return;
    }

    remove_character_item( $character[ 'id' ], $item[ 'id' ] );
    $character[ 'mech' ][ $args[ 'slot' ] ] = $item[ 'item_id' ];
    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_CHARACTER_CREDITS, $credits - 100 );

    sc_pack_mech();

    $GLOBALS[ 'redirect_header' ] = GAME_URL .
        '?action=zone&zone_tag=minstall';
}

$custom_setting_map[ 'add_equipment' ] = 'sc_add_equipment';