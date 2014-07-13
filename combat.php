<?php

function sc_combat_check() {
    global $game, $character;

    if ( FALSE == $character ) {
        return;
    }

    $state = character_meta( sc_meta_type_character, SC_COMBAT_STATE );

    // If a character already has a combat state, don't let them do other
    //     things until it is resolved.
    if ( 0 < strlen( $state ) ) {
        $game->set_action( 'combat' );
    }
}

add_action( 'action_set', 'sc_combat_check' );


function sc_combat_print() {
    global $game, $user, $character;

    if ( strcmp( 'combat', $game->get_action() ) ) {
       return;
    }

    ensure_character_mech();

    $state_obj = array();
    $state_meta_obj = explode( ';',
        character_meta( sc_meta_type_character, SC_COMBAT_STATE ) );

    foreach ( $state_meta_obj as $state ) {
        $state = explode( '=', $state );
        $state_obj[ $state[ 0 ] ] = $state[ 1 ];
    }

    $state_obj[ 'npc' ] = get_npc_by_id( $state_obj[ 'id' ] );
    $state_obj[ 'npc' ][ 'state' ] = sc_explode_meta(
        $state_obj[ 'npc' ][ 'npc_state' ] );

?>
<div class="row">
    <div class="col-xs-2">
      <img src="<?php echo( GAME_CUSTOM_STYLE_URL ); ?>avatars/basemech.png"
          width="150px">
    </div><div class="col-xs-4">
      <h3 class="text-right"><?php echo $character[ 'character_name' ]; ?></h3>
      <p class="text-right lead"><?php
          echo( $character[ 'mech' ][ 'model' ] ); ?></p>
    </div>
    <div class="col-xs-4">
      <h3><?php echo $state_obj[ 'npc' ][ 'npc_name' ]; ?></h3>
      <p class="lead"><?php
          echo $state_obj[ 'npc' ][ 'npc_description' ]; ?></p>
    </div><div class="col-xs-2">
      <img src="<?php echo( GAME_CUSTOM_STYLE_URL ); ?>avatars/stalker.png"
         class="" style="float: right;" width="150px">
    </div>
</div>
<?php

    if ( 0 >= $character[ 'mech' ][ 'health' ] ) {

        echo( '<div class="row"><div class="col-xs-12 text-center">' );
        echo( character_meta(
            sc_meta_type_character, SC_COMBAT_UPDATE_TEXT ) );
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_COMBAT_UPDATE_TEXT, '' );

        echo( '<h4>You have been defeated!</h4>' );
        $character[ 'mech' ][ 'health' ] = 0;

        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_COMBAT_STATE, '' );

        $zone_id = GAME_STARTING_ZONE;
        if ( '' != character_meta( sc_meta_type_character,
                                   SC_CURRENT_ZONE ) ) {
            $zone_id = character_meta( sc_meta_type_character,
                                       SC_CURRENT_ZONE );
        }

        $zone = get_zone( $zone_id );

        echo( '<h4><a href="' . GAME_URL .
              '?action=zone&zone_tag=' .
              $zone[ 'zone_tag' ] . '">Return to ' .
              $zone[ 'zone_title' ] . '.</a></h4></div></div>' );

        sc_pack_mech();

    } else if ( $character[ 'mech' ][ 'heat' ] >=
                $character[ 'mech' ][ 'heat_max' ] ) {

        echo( '<div class="row"><div class="col-xs-12 text-center">' );
        echo( character_meta(
            sc_meta_type_character, SC_COMBAT_UPDATE_TEXT ) );
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_COMBAT_UPDATE_TEXT, '' );

        echo( '<h4>Your mech has overheated!</h4>' );
        $character[ 'mech' ][ 'health' ] = 0;
        $character[ 'mech' ][ 'heat' ] = $character[ 'mech' ][ 'heat_max' ];

        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_COMBAT_STATE, '' );

        $zone_id = GAME_STARTING_ZONE;
        if ( '' != character_meta( sc_meta_type_character,
                                   SC_CURRENT_ZONE ) ) {
            $zone_id = character_meta( sc_meta_type_character,
                                       SC_CURRENT_ZONE );
        }

        $zone = get_zone( $zone_id );

        echo( '<h4><a href="' . GAME_URL .
              '?action=zone&zone_tag=' .
              $zone[ 'zone_tag' ] . '">Return to ' .
              $zone[ 'zone_title' ] . '.</a></h4></div></div>' );

        sc_pack_mech();

    } else if ( 0 >= $state_obj[ 'health' ] ) {

        echo( '<div class="row"><div class="col-xs-12 text-center">' );
        echo( character_meta(
            sc_meta_type_character, SC_COMBAT_UPDATE_TEXT ) );
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_COMBAT_UPDATE_TEXT, '' );

        echo '<h3>You win.</h3>';
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_COMBAT_STATE, '' );

        award_achievement( 1 );

        ensure_character_meta( $character[ 'id' ], sc_meta_type_npckills,
            $state_obj[ 'id' ] );
        $kill_count = intval(
            character_meta( sc_meta_type_npckills, $state_obj[ 'id' ] ) ) + 1;
        update_character_meta( $character[ 'id' ], sc_meta_type_npckills,
            $state_obj[ 'id' ], $kill_count );

        echo '<p>You have defeated your ' . number_with_suffix( $kill_count ) .
             ' ' . $state_obj[ 'npc' ][ 'npc_name' ] . '.</p>';

        if ( isset( $state_obj[ 'npc' ][ 'state' ][ 'credits' ] ) ) {
            $credits = $state_obj[ 'npc' ][ 'state' ][ 'credits' ];
            echo '<p>' . $credits . ' credits awarded.</p>';

            $new_credits = intval( $credits + character_meta(
                sc_meta_type_character, SC_CHARACTER_CREDITS ) );
            update_character_meta( $character[ 'id' ], sc_meta_type_character,
                SC_CHARACTER_CREDITS, $new_credits );
        }

        $zone_id = GAME_STARTING_ZONE;
        if ( '' != character_meta( sc_meta_type_character,
                                   SC_CURRENT_ZONE ) ) {
            $zone_id = character_meta( sc_meta_type_character,
                                       SC_CURRENT_ZONE );
        }

        $zone = get_zone( $zone_id );

        echo '<p><a href="' . GAME_URL . '?action=zone&zone_tag=' .
             $zone[ 'zone_tag' ] . '">Return to ' .
             $zone[ 'zone_title' ] . '.</a><br>' .
             '<a href="' . GAME_URL .
             'game-setting.php?setting=start_combat">' .
             'Find another fight!</a></p>';

        echo( '</div></div>' );

    } else {

        ensure_character_mech();
        $item_obj = get_character_mech_items();
        $key_obj = sc_mech_get_slots();

        $attack_id_obj = array();
        $item_attack_obj = array();
        foreach ( $item_obj as $item ) {
            if ( 0 == strlen( $item[ 'item_meta' ] ) ) {
                continue;
            }

            $item_meta = explode( ';', $item[ 'item_meta' ] );
            foreach ( array_keys( $item_meta ) as $k ) {
                $item_meta_element = explode( '=', $item_meta[ $k ] );
                if ( ! strcmp( $item_meta_element[ 0 ], 'attack' ) ) {
                    $attack_id_obj[] = $item_meta_element[ 1 ];
                    $item_attack_obj[
                        $item[ 'id' ] ] = $item_meta_element[ 1 ];
                }
            }
        }
        $attack_obj = get_attacks_from_array( $attack_id_obj );

?>
<div class="row">
    <div class="col-xs-6">
      <form role="form" method="get" action="game-setting.php">
      <input type="hidden" name="setting" value="mech_attack">
<?php
        foreach ( $key_obj as $k => $v ) {
            $item_id = $character[ 'mech' ][ $k ];
            if ( isset( $item_attack_obj[ $item_id ] ) ) {
                echo( '<div class="clearfix">' .
                      '<div class="btn-group pull-right" ' .
                          'data-toggle="buttons">' .
                      '<label class="btn btn-primary">' .
                      '<input type="checkbox" name="' . $k .
                      '" value="' . $item_attack_obj[ $item_id ] .
                      '">' . $attack_obj[
                          $item_attack_obj[ $item_id ] ][ 'attack_name' ] .
                      ' (' . $v . ')</label></div></div>' );
            }
        }
?>
      <input class="btn btn-default pull-right" type="submit" value="Submit">
      </form>
    </div>
    <div class="col-xs-6">
<?php
        $npc_health = $state_obj[ 'health' ] / $state_obj[
            'npc' ][ 'state' ][ 'health' ];

        if ( 0.15 > $npc_health ) {
            echo '<h4>Your foe is critically injured!</h4>';
        } else if ( 0.5 > $npc_health ) {
            echo '<h4>Your foe is wounded!</h4>';
        } else {
            echo '<h4>Your foe is healthy.</h4>';
        }
?>

    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 text-center">
<?php
        echo( character_meta(
            sc_meta_type_character, SC_COMBAT_UPDATE_TEXT ) );
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_COMBAT_UPDATE_TEXT, '' );
?>
    </div>
  </div>
</div><?php
    }
}

add_action( 'do_page_content', 'sc_combat_print' );


function sc_start_combat_setting( $args ) {
    global $character;

    $state = character_meta( sc_meta_type_character, SC_COMBAT_STATE );

    // Don't let a character into combat if they already have a combat state.
    if ( 0 < strlen( $state ) ) {
        return;
    }

    ensure_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_COMBAT_STATE );
    ensure_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_COMBAT_UPDATE_TEXT );

    $zone_npcs = sc_get_zone_npcs(
        character_meta( sc_meta_type_character, SC_CURRENT_ZONE ) );

    if ( 0 == count( $zone_npcs ) ) {
        return;
    }

    // todo: Not just random.
    $npc_key = array_rand( $zone_npcs );
    $npc = get_npc_by_id( $zone_npcs[ $npc_key ] );

    $new_state_obj = explode( ';', $npc[ 'npc_state' ] );
    $new_state_obj[] = 'id=' . $npc[ 'id' ];

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_COMBAT_STATE, join( ';', $new_state_obj ) );
}

$custom_setting_map[ 'start_combat' ] = 'sc_start_combat_setting';



function sc_abort_combat_setting( $args ) {
    global $user, $character;

    if ( ! is_user_dev( $user ) ) {
        return;
    }

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_COMBAT_STATE, '' );
}

$custom_setting_map[ 'abort_combat' ] = 'sc_abort_combat_setting';


function sc_npc_attack( $npc, $state_obj, &$state_text ) {
    global $character;

    if ( ! isset( $state_obj[ 'attacks' ] ) ) {
        return;
    }

    $attack_id_obj = explode( ',', $state_obj[ 'attacks' ] );
    $attack_obj = get_attacks_from_array( $attack_id_obj );
    $attack_id = array_rand( $attack_obj );
    $attack = $attack_obj[ $attack_id ];

    $attack_meta = array();
    $attack_meta_elements = explode( ';', $attack[ 'attack_meta' ] );
    foreach ( $attack_meta_elements as $k => $v ) {
        $meta = explode( '=', $v );
        $attack_meta[ $meta[ 0 ] ] = $meta[ 1 ];
    }

    $state_text[] = '<p><b>' . $attack[ 'attack_description' ] . '</b><br>';
    $damage = rand( $attack_meta[ 'mindmg' ], $attack_meta[ 'maxdmg' ] );
    $state_text[] = 'You are hit for ' . $damage . ' damage by the ' .
        $attack[ 'attack_name' ] . '!</p>';
    $character[ 'mech' ][ 'health' ] -= $damage;
}

function sc_combat_mech_attack_setting( $args ) {
    global $game, $character;

    $state = character_meta( sc_meta_type_character, SC_COMBAT_STATE );

    if ( 0 == strlen( $state ) ) {
        return;
    }

    $state_obj = array();
    $state_meta_obj = explode( ';', $state );

    foreach ( $state_meta_obj as $state ) {
        $state = explode( '=', $state );
        $state_obj[ $state[ 0 ] ] = $state[ 1 ];
    }

    $npc = get_npc_by_id( $state_obj[ 'id' ] );

    ensure_character_mech();
    $item_obj = get_character_mech_items();
    $key_obj = sc_mech_get_slots();

    $attack_id_obj = array();
    $item_attack_obj = array();
    foreach ( $item_obj as $item ) {
        if ( 0 == strlen( $item[ 'item_meta' ] ) ) {
            continue;
        }

        $item_meta = explode( ';', $item[ 'item_meta' ] );
        foreach ( array_keys( $item_meta ) as $k ) {
            $item_meta_element = explode( '=', $item_meta[ $k ] );
            if ( ! strcmp( $item_meta_element[ 0 ], 'attack' ) ) {
                $attack_id_obj[] = $item_meta_element[ 1 ];
                $item_attack_obj[ $item[ 'id' ] ] = $item_meta_element[ 1 ];
            }
        }
    }
    $attack_obj = get_attacks_from_array( $attack_id_obj );

    $attack_queue = array();
    $state_text = array();

    foreach ( $_GET as $k => $v ) {
        if ( isset( $key_obj[ $k ] ) ) {
            if ( $item_attack_obj[ $character[ 'mech' ][ $k ] ] == $v ) {
                $attack_queue[] = $attack_obj[ $v ];
            }
        }
    }

    while ( ! empty( $attack_queue ) ) {
        $a_key = array_rand( $attack_queue, 1 );
        $attack = $attack_queue[ $a_key ];
        unset( $attack_queue[ $a_key ] );

        $attack_meta = array();
        $attack_meta_elements = explode( ';', $attack[ 'attack_meta' ] );
        foreach ( $attack_meta_elements as $k => $v ) {
            $meta = explode( '=', $v );
            $attack_meta[ $meta[ 0 ] ] = $meta[ 1 ];
        }

        $state_text[] = '<p><b>' . $attack[ 'attack_description' ] .
            '</b><br>';
        $damage = rand( $attack_meta[ 'mindmg' ], $attack_meta[ 'maxdmg' ] );
        $state_text[] = 'Your ' . $attack[ 'attack_name' ] . ' hits for ' .
            $damage . ' damage.</p>';
        $state_obj[ 'health' ] -= $damage;

        ensure_character_meta( $character[ 'id' ], sc_meta_type_weapondamage,
            $attack[ 'id' ] );
        $dmg_count = intval( character_meta(
            sc_meta_type_weapondamage, $attack[ 'id' ] ) ) + $damage;
        update_character_meta( $character[ 'id' ], sc_meta_type_weapondamage,
            $attack[ 'id' ], $dmg_count );
        $character[ 'meta' ][ sc_meta_type_weapondamage ][
            $attack[ 'id' ] ] = $dmg_count;

        if ( isset( $attack_meta[ 'heat' ] ) ) {
            $character[ 'mech' ][ 'heat' ] += intval( $attack_meta[ 'heat' ] );
        }
    }

    sc_npc_attack( $npc, $state_obj, $state_text );

    $new_state_obj = array();
    foreach ( $state_obj as $k => $v ) {
        $new_state_obj[] = $k . '=' . $v;
    }

    sc_pack_mech();

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_COMBAT_UPDATE_TEXT,
        "\n".join( $state_text ) );

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_COMBAT_STATE, join( ';', $new_state_obj ) );
}

$custom_setting_map[ 'mech_attack' ] = 'sc_combat_mech_attack_setting';


function sc_achievement_print( $args ) {
    if ( ! isset( $args[ 'achievement_id' ] ) ) {
        return;
    }

    $achievement = get_achievement( $args[ 'achievement_id' ] );

    echo( '<h1>ACHIEVEMENT AWARDED</h1><h2>' .
          $achievement[ 'achieve_title' ] . '</h2><h3>' .
          $achievement[ 'achieve_text' ] . '</h3>' );
}

add_action( 'award_achievement', 'sc_achievement_print' );



function sc_get_zone_npcs( $id ) {
    global $game;

    $game->ensure_meta();

    if ( ! isset( $game->meta[ sc_game_meta_zonenpc ][ $id ] ) ) {
        return array();
    }

    $npc_obj = explode_meta( $game->meta[ sc_game_meta_zonenpc ][ $id ] );

    return $npc_obj;
}
