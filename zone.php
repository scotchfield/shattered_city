<?php

function sc_zone_set() {
    global $game, $character;

    if ( strcmp( 'zone', $game->get_action() ) ) {
        return;
    }

    if ( ! ( isset( $_GET[ 'zone_id' ] ) || isset( $_GET[ 'zone_tag' ] ) ) ) {
        return;
    }

    $zone_id = 0;
    if ( isset( $_GET[ 'zone_tag' ] ) ) {
        $zone = get_zone_by_tag( $_GET[ 'zone_tag' ] );
        if ( FALSE != $zone ) {
            $zone_id = $zone[ 'id' ];
        }
    } else {
        $zone_id = intval( $_GET[ 'zone_id' ] );
    }

    if ( '' != character_meta( sc_meta_type_character, SC_CURRENT_ZONE ) ) {
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_CURRENT_ZONE, $zone_id );
    } else {
        add_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_CURRENT_ZONE, $zone_id );
    }

    $character[ 'meta' ][ sc_meta_type_character ][ SC_CURRENT_ZONE ] =
        $zone_id;
}

add_action( 'action_set', 'sc_zone_set' );

function sc_zone_content() {
    global $game, $character;

    if ( strcmp( 'zone', $game->get_action() ) ) {
       return;
    }

    $zone_id = GAME_STARTING_ZONE;
    if ( '' != character_meta( sc_meta_type_character, SC_CURRENT_ZONE ) ) {
        $zone_id = character_meta( sc_meta_type_character, SC_CURRENT_ZONE );
    }

    $zone = get_zone( $zone_id );
    $zone_transitions = get_zone_transitions( $zone_id );
    $zone[ 'meta' ] = explode_meta( $zone[ 'zone_meta' ] );

    $npc_obj = array();
    if ( isset( $zone[ 'meta' ][ 'npcs' ] ) ) {
        $npc_id_obj = explode( ',', $zone[ 'meta' ][ 'npcs' ] );
        foreach ( $npc_id_obj as $npc_id ) {
            $npc_obj[ $npc_id ] = get_npc_by_id( $npc_id );
        }
    }

    echo( '<div class="row"><div class="col-xs-8">' .
          '<h3>' . $zone[ 'zone_title' ] . '</h3>' .
          '<p class="lead">' . $zone[ 'zone_description' ] . '</p>' .
          '</div><div class="col-xs-4">' );

    if ( count( $npc_obj ) > 0 ) {
        echo( '<h4 class="text-right">Others here</h4><ul>' );
        foreach ( $npc_obj as $zn ) {
            echo( '<li class="text-right"><a href="' . GAME_URL .
                  '?action=npc&amp;id=' .
                  $zn[ 'id' ] . '">' . $zn[ 'npc_name' ] .
                  '</a></li>' );
        }
        echo( '</ul>' );
    }

    echo '<h4 class="text-right">Go somewhere else</h4><ul>';
    foreach ( $zone_transitions as $zt ) {
        echo '<li class="text-right"><a href="' . GAME_URL .
             '?action=zone&amp;zone_tag=' .
             $zt[ 'zone_tag' ] . '">' . $zt[ 'zone_title' ] .
             '</a></li>';
    }
    echo '</ul>';

    echo( '</div></div>' );

    if ( ! strcmp( 'combat', $zone[ 'zone_type' ] ) ) {
        if ( ! strcmp( 'titanrift', $zone[ 'zone_tag' ] ) ) {
?>
<div class="row">
  <div class="col-md-12 text-center">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_atowers.png" width="150">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_unknown.png" width="150">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_unknown.png" width="150">
  </div>
  <div class="col-md-12 text-center">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_unknown.png" width="150">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_core.png" width="150">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_unknown.png" width="150">
  </div>
  <div class="col-md-12 text-center">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_unknown.png" width="150">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_unknown.png" width="150">
    <img src="<?php echo( GAME_CUSTOM_STYLE_URL );
         ?>titanrift_unknown.png" width="150">
  </div>
</div>
<?php
        }
        echo '<h3 class="text-center">' .
             '<a href="game-setting.php?setting=start_combat">' .
             'Start combat!</a></h3>';
    } else if ( ! strcmp( 'store', $zone[ 'zone_type' ] ) ) {
        $item_obj = get_zone_items_full( $zone[ 'id' ] );

        echo( '<div class="row"><div class="col-xs-6">' .
              '<h3>Items for sale</h3>' );

        if ( 0 == count( $item_obj ) ) {
            echo( '<h4>Nothing</h4>' );
        } else {
            echo( '<ul>' );
            foreach ( $item_obj as $item ) {
                $item_meta = explode_meta( $item[ 'item_meta' ] );
                echo( '<li>' . sc_item_string( $item ) .
                      ' (<a href="game-setting.php?setting=buy_item' .
                      '&zone_tag=' . $zone[ 'zone_tag' ] . '&item_id=' .
                      $item[ 'id' ] . '">buy: ' . $item_meta[ 'buy' ] .
                      ' credits</a>)</li>' );
            }
            echo( '</ul>' );
        }

        echo( '</div><div class="col-xs-6"><h3>Sell your items</h3>' );

        $item_obj = get_character_items_full( $character[ 'id' ] );

        if ( 0 == count( $item_obj ) ) {
            echo( '<h4>Nothing</h4>' );
        } else {
            echo( '<ul>' );
            foreach ( $item_obj as $item ) {
                $item_meta = explode_meta( $item[ 'item_meta' ] );

                if ( ! isset( $item_meta[ 'sell' ] ) ) {
                    continue;
                }

                echo( '<li>' . sc_item_string( $item ) .
                      ' (<a href="game-setting.php?setting=sell_item' .
                      '&zone_tag=' . $zone[ 'zone_tag' ] . '&item_id=' .
                      $item[ 'id' ] . '">sell: ' . $item_meta[ 'sell' ] .
                      ' credits</a>)</li>' );
            }
            echo( '</ul>' );
        }

        echo( '</div></div>' );
    } else if ( ! strcmp( 'mech_modify', $zone[ 'zone_type' ] ) ) {
        sc_zone_mechmodify();
    } else if ( ! strcmp( 'casino', $zone[ 'zone_type' ] ) ) {
        sc_zone_casino();
    }

}

add_action( 'do_page_content', 'sc_zone_content' );

function sc_zone_mechmodify() {
    global $character;

    ensure_character_mech();
    $item_obj = get_character_mech_items();
    $key_obj = sc_mech_get_slots();

    echo( '<h3>Current mech: ' .
          $character[ 'mech' ][ 'model' ] . '</h3>' );
    $damage_cost = $character[ 'mech' ][ 'health_max' ] -
        $character[ 'mech' ][ 'health' ];
    echo( '<p><a href="game-setting.php?setting=repair_mech">' .
          'Repair all damage (' . $damage_cost . ' credits)</a></p>' );

?>
<div class="row">
  <div class="col-xs-6">
    <h3>Configuration</h3>
    <dl class="dl-horizontal">
<?php
    $last_v = '';
    foreach ( $key_obj as $k => $v ) {
        if ( strcmp( $last_v, $v ) ) {
            echo '<dt>' . $v . '</dt>';
        }
        $last_v = $v;
        echo '<dd>' . sc_item_popup_str(
             $item_obj[ $character[ 'mech' ][ $k ] ] );
        if ( 0 < $item_obj[ $character[ 'mech' ][ $k ] ][ 'id' ] ) {
             echo( ' <small>(<a href="game-setting.php?' .
                   'setting=remove_equipment&amp;slot=' . $k .
                   '">remove</a>)</small></dd>' );
        } else {
            echo( '</dd>' );
        }
    }
?>
    </dl>
  </div>
  <div class="col-xs-6">
    <h3>Install Hardware</h3>
    <dl class="dl-horizontal">
<?php
    $item_obj = get_character_items_full( $character[ 'id' ] );

/*
    $name_obj = array();
    foreach ( $item_obj as $k => $v ) {
        $name_obj[ $k ] = $v[ 'name' ];
    }
    array_multisort( $name_obj, SORT_ASC, $item_obj );
*/

    $slot_obj = array();
    foreach ( array_keys( $key_obj ) as $k ) {
        $slot_obj[ $k ] = array();
    }
    foreach ( $item_obj as $item ) {
        $item_meta = explode_meta( $item[ 'item_meta' ] );
        if ( isset( $item_meta[ 'slot' ] ) ) {
            $slot = explode( ',', $item_meta[ 'slot' ] );
            foreach ( $slot as $s ) {
                $slot_obj[ $s ][] = $item[ 'id' ];
            }
        }
    }

    $last_v = '';
    foreach ( $key_obj as $k => $v ) {
        if ( 0 == count( $slot_obj[ $k ] ) ) {
            continue;
        }

        if ( 0 != $character[ 'mech' ][ $k ] ) {
            continue;
        }

        echo( '<h4 class="text-right">' . $v . '</h4>' );
        $last_v = $v;

        echo( '<div class="row">' .
              '<form class="form-inline" role="form" ' .
                  'method="get" action="game-setting.php">' .
              '<input type="hidden" name="setting" ' .
                  'value="add_equipment">' .
              '<input type="hidden" name="slot" value="' . $k . '">' .
              '<div class="col-xs-8">' .
              '<select name="item_id" class="form-control">' );
        foreach ( $slot_obj[ $k ] as $item_id ) {
            echo( '<option value="' . $item_id . '">' .
                  $item_obj[ $item_id ][ 'name' ] . '</option>' );
        }
        echo( '</select></div>' .
              '<div class="col-xs-4">' .
              '<button type="submit" class="btn btn-default pull-right">' .
                  'Submit</button></div>' .
              "</form></div>\n" );
    }
?>
    </dl>
  </div>
</div>
<?php
}

function sc_zone_casino() {
?>
<div class="row">
  <div class="col-xs-6 text-center">
    <h4>Your credits:
<?php echo( character_meta( sc_meta_type_character,
                            SC_CHARACTER_CREDITS ) ); ?></h4>
  </div>
  <div class="col-xs-6 text-center">
    <h4>Your player status:
      Bronze Tier
    </h4>
  </div>
</div>

<?php
    if ( isset( $_GET[ 'roulette' ] ) ) {
?>
<form method="get" action="game-setting.php" role="form">
<input type="hidden" name="setting" value="casino_roulette">
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"></div>
  <div class="col-xs-3"><?php
    echo sc_checkbox( '0', 1, '0' ); ?></div>
  <div class="col-xs-3"><?php
    echo sc_checkbox( '00', 1, '00' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
    echo sc_checkbox( '1st12', 1, '1st 12' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '1', 1, '1' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '2', 1, '2' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '3', 1, '3' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
    echo sc_checkbox( '2nd12', 1, '2nd 12' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '4', 1, '4' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '5', 1, '5' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '6', 1, '6' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
    echo sc_checkbox( '3rd12', 1, '3rd 12' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '7', 1, '7' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '8', 1, '8' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '9', 1, '9' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
    echo sc_checkbox( '1to18', 1, '1 to 18' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '10', 1, '10' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '11', 1, '11' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '12', 1, '12' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
    echo sc_checkbox( '19to36', 1, '19 to 36' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '13', 1, '13' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '14', 1, '14' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '15', 1, '15' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
    echo sc_checkbox( 'even', 1, 'Even' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '16', 1, '16' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '17', 1, '17' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '18', 1, '18' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
    echo sc_checkbox( 'odd', 1, 'Odd' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '19', 1, '19' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '20', 1, '20' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '21', 1, '21' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
//    echo sc_checkbox( 'red', 1, 'Red' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '22', 1, '22' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '23', 1, '23' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '24', 1, '24' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-1"></div>
  <div class="col-xs-4"><?php
//    echo sc_checkbox( 'black', 1, 'Black' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '25', 1, '25' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '26', 1, '26' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '27', 1, '27' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-5"></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '28', 1, '28' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '29', 1, '29' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '30', 1, '30' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-5"></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '31', 1, '31' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '32', 1, '32' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '33', 1, '33' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-5"></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '34', 1, '34' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '35', 1, '35' ); ?></div>
  <div class="col-xs-2"><?php
    echo sc_checkbox( '36', 1, '36' ); ?></div>
  <div class="col-xs-1"></div>
</div>
<div class="row">
  <div class="col-xs-12 text-center">
    <button type="submit" name="bet" value="100">100 credits</button>
  </div>
</div>
</form>
<?php
    } else {
        echo( '<h3 class="text-center">' . '<a href="' . GAME_URL .
              '?action=zone&zone_tag=casino&roulette">' .
              'Play roulette</a></h3>' );
    }

}

function sc_setting_roulette() {
    global $character;

    $GLOBALS[ 'redirect_header' ] = GAME_URL .
        '?action=zone&zone_tag=casino&roulette';

    $valid_bets = array( 100 );
    if ( ! in_array( intval( $_GET[ 'bet' ] ), $valid_bets ) ) {
        return;
    }

    $_GET[ 'bet' ] = intval( $_GET[ 'bet' ] );

    $bet_obj = array(
        '1st12' => 3,
        '2nd12' => 3,
        '3rd12' => 3,
        '1to18' => 2,
        '19to36' => 2,
        'even' => 2,
        'odd' => 2,
        'red' => 2,
        'black' => 2,
        '00' => 36,
    );
    // Note: Using -1 instead of double-zero.

    for ( $i = -1; $i <= 35; $i++ ) {
        $bet_obj[ "$i" ] = 36;
    }

    $bet_count = 0;
    foreach ( array_keys( $_GET ) as $k ) {
        if ( isset( $bet_obj[ $k ] ) ) {
            $bet_count += 1;
        }
    }

    $bet_cost = $bet_count * intval( $_GET[ 'bet' ] );

    if ( $bet_cost > character_meta( sc_meta_type_character,
                                     SC_CHARACTER_CREDITS ) ) {
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_CHARACTER_TIP, '<h3>You don\'t have enough credits!</h3>' );
        return;
    }

    $st = '<div class="text-center">' .
        '<h3>You make a payment of ' . $bet_cost . ' credits, place your ' .
        'bets, and watch.</h3>';

    $n = rand( -1, 36 );
    if ( -1 == $n ) {
        $n_str = '00';
    } else {
        $n_str = "$n";
    }

    $st = $st . '<h3>The wheel spins and spins before finally landing ' .
        'on...</h3>' . '<h2>' . $n_str . '!</h2>';

    $award = 0;
    foreach ( array_keys( $_GET ) as $k ) {
        if ( isset( $bet_obj[ $k ] ) ) {
            if ( $n == $k ) {
                $award += $_GET[ 'bet' ] * $bet_obj[ $k ];
            } else {
                if ( ! strcmp( $k, '00' ) && ( -1 == $n ) ) {
                    $award += $_GET[ 'bet' ] * $bet_obj[ $n ];
                } else if ( ! strcmp( $k, '1st12' ) &&
                     ( $n >= 1 ) && ( $n <= 12 ) ) {
                    $award += $_GET[ 'bet' ] * $bet_obj[ '1st12' ];
                } else if ( ! strcmp( $k, '2nd12' ) &&
                     ( $n >= 13 ) && ( $n <= 24 ) ) {
                    $award += $_GET[ 'bet' ] * $bet_obj[ '2nd12' ];
                } else if ( ! strcmp( $k, '3rd12' ) &&
                     ( $n >= 25 ) && ( $n <= 36 ) ) {
                    $award += $_GET[ 'bet' ] * $bet_obj[ '3rd12' ];
                } else if ( ! strcmp( $k, '1to18' ) &&
                     ( $n >= 1 ) && ( $n <= 18 ) ) {
                    $award += $_GET[ 'bet' ] * $bet_obj[ '1to18' ];
                } else if ( ! strcmp( $k, '19to36' ) &&
                     ( $n >= 19 ) && ( $n <= 36 ) ) {
                    $award += $_GET[ 'bet' ] * $bet_obj[ '19to36' ];
                } else if ( ! strcmp( $k, 'even' ) && ( 0 == ( $n % 2 ) ) ) {
                    $award += $_GET[ 'bet' ] * $bet_obj[ 'even' ];
                } else if ( ! strcmp( $k, 'odd' ) && ( 1 == ( $n % 2 ) ) ) {
                    $award += $_GET[ 'bet' ] * $bet_obj[ 'odd' ];
                }
            }
        }
    }

    if ( 0 < $award ) {
        $st = $st . '<h3>You win!  The croupier awards you ' . $award .
            ' credits!</h3>';
    } else {
        $st = $st . '<h3>Unfortunately none of your bets were winners. ' .
            'Care to try again?</h3>';
    }

    $st = $st . '</div>';

    $new_credits = character_meta( sc_meta_type_character,
        SC_CHARACTER_CREDITS ) - $bet_cost + $award;
    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_CHARACTER_CREDITS, $new_credits );

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_CHARACTER_TIP, $st );
}

$custom_setting_map[ 'casino_roulette' ] = 'sc_setting_roulette';

