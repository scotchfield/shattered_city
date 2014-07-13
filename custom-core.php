<?php

/* load anything we need */
/* should probably take a wp approach and load all php in dir? todo */
require( GAME_CUSTOM_PATH . 'profile.php' );

require( GAME_CUSTOM_PATH . 'casino.php' );
require( GAME_CUSTOM_PATH . 'combat.php' );
require( GAME_CUSTOM_PATH . 'inventory.php' );
require( GAME_CUSTOM_PATH . 'map.php' );
require( GAME_CUSTOM_PATH . 'mech.php' );
require( GAME_CUSTOM_PATH . 'npc.php' );
require( GAME_CUSTOM_PATH . 'predicate.php' );
require( GAME_CUSTOM_PATH . 'select.php' );
require( GAME_CUSTOM_PATH . 'tutorial.php' );
require( GAME_CUSTOM_PATH . 'zone.php' );

$custom_start_page = 'title.php';

$custom_default_action = 'map';


$GLOBALS[ 'mech_update_meta' ] = FALSE;


define( 'sc_meta_type_character',    1 );
define( 'sc_meta_type_inventory',    2 );
define( 'sc_meta_type_npckills',     3 );
define( 'sc_meta_type_mechtypes',    4 );
define( 'sc_meta_type_weapondamage', 5 );

define( 'SC_BURDEN',              1 );
define( 'SC_CHARACTER_AGE',       2 );
define( 'SC_CHARACTER_BIO',       3 );
define( 'SC_CHARACTER_CREDITS',   4 );
define( 'SC_CHARACTER_NAME',      5 );
define( 'SC_CHARACTER_TIP',       6 );
define( 'SC_COMBAT_STATE',        7 );
define( 'SC_COMBAT_UPDATE_TEXT',  8 );
define( 'SC_CURRENT_MECH',        9 );
define( 'SC_CURRENT_ZONE',       10 );
define( 'SC_TUTORIAL_STATUS',    11 );


define( 'sc_game_meta_zonenpc',   1 );
define( 'sc_game_meta_mechtypes', 2 );


function sc_login() {
    global $character;

    ensure_character_meta( $character[ 'id' ], sc_meta_type_character,
                           SC_BURDEN );
    ensure_character_meta( $character[ 'id' ], sc_meta_type_character,
                           SC_CHARACTER_CREDITS );
    ensure_character_meta( $character[ 'id' ], sc_meta_type_character,
                           SC_CHARACTER_TIP );
    ensure_character_meta( $character[ 'id' ], sc_meta_type_character,
                           SC_CURRENT_MECH );
    ensure_character_meta( $character[ 'id' ], sc_meta_type_character,
                           SC_TUTORIAL_STATUS );
}

add_action( 'select_character', 'sc_login' );

function sc_header() {
    global $game, $user, $character;

    $obj = array( '<!DOCTYPE html>',
'<html lang="en">',
'  <head>',
'    <meta charset="utf-8">',
'    <meta http-equiv="X-UA-Compatible" content="IE=edge">',
'    <meta name="viewport" content="width=device-width, initial-scale=1">',
'    <title>Shattered City (' . $game->get_action() . ')</title>',
'    <link rel="stylesheet" href="' . GAME_URL . 'style/bootstrap.min.css">',
'    <link rel="stylesheet" href="' . GAME_CUSTOM_STYLE_URL . 'sc.css">',
'    <link href="http://fonts.googleapis.com/css?' .
         'family=Raleway:400,500" rel="stylesheet" type="text/css">',
'  </head>',
'  <body>',
'    <div id="popup" class="invis"></div>',
'    <div class="navbar navbar-default navbar-fixed-top" role="navigation">',
'      <div class="container">',
'        <div class="navbar-header">',
'          <button type="button" class="navbar-toggle" data-toggle=' .
               '"collapse" data-target=".navbar-collapse">',
'            <span class="sr-only">Toggle navigation</span>',
'            <span class="icon-bar"></span>',
'            <span class="icon-bar"></span>',
'            <span class="icon-bar"></span>',
'          </button>',
'          <a class="navbar-brand" href="' . GAME_URL . '">Shattered City</a>',
'        </div>'
        );

    if ( FALSE != $character ) {
        array_push( $obj,
'        <div class="collapse navbar-collapse">',
'          <ul class="nav navbar-nav">',
'            <li class="dropdown">',
'              <a href="#" class="dropdown-toggle" data-toggle="dropdown">' .
                   'Navigate <b class="caret"></b></a>',
'              <ul class="dropdown-menu">',
'                <li class="dropdown-header">Main Locations</li>',
'                <li><a href="?action=zone&amp;zone_tag=cydonia">' .
                     'Cydonia Heavy Industries</a></li>',
'                <li><a href="?action=zone&amp;zone_tag=minstall">' .
                     'Mech Installations</a></li>',
'                <li><a href="?action=zone&amp;zone_tag=cityhall">' .
                     'City Hall</a></li>',
'                <li><a href="?action=zone&amp;zone_tag=wordtruth">' .
                     'The Word of Truth</a></li>',
'                <li class="divider"></li>',
'                <li class="dropdown-header">Combat Locations</li>',
'                <li><a href="?action=zone&amp;zone_tag=titanrift">' .
                     'Titan\'s Rift</a></li>',
'                <li><a href="?action=zone&amp;zone_tag=epsilon">' .
                     'The Epsilon Rift</a></li>',
'              </ul>',
'            </li>',
'            <li><a href="?action=about">About</a></li>',
'            <li><a href="?action=contact">Contact</a></li>',
'          </ul>',
'          <ul class="nav navbar-nav navbar-right">',
'            <li class="dropdown">',
'              <a href="#" class="dropdown-toggle" data-toggle="dropdown">' .
                   $character[ 'character_name' ] .
                   ' <b class="caret"></b></a>',
'              <ul class="dropdown-menu">',
'                <li><a href="?action=profile">Profile</a></li>',
'                <li><a href="?action=questlog">Quest Log</a></li>',
'                <li><a href="?action=mech">Active Mech</a></li>',
'                <li><a href="?action=inventory">Inventory</a></li>',
'                <li class="divider"></li>',
'                <li><a href="?action=dashboard">Dashboard</a></li>',
'                <li class="divider"></li>',
'                <li><a href="game-setting.php?setting=change_character">' .
                     'Change Character</a></li>',
'                <li><a href="game-logout.php">Log out</a></li>',
'              </ul>',
'            </li>',
'          </ul>',
'        </div><!--/.nav-collapse -->'
            );
    }

    array_push( $obj,
'      </div>',
'    </div>',
'',
'    <div class="container">'
        );

    echo join( "\n", $obj );
}

function sc_footer() {
    global $game, $character;

    $footer_text = '';

    if ( FALSE != $character) {
        $heat = round( ( 100.0 * $character[ 'mech' ][ 'heat' ] ) /
            $character[ 'mech' ][ 'heat_max' ], 2 );

        $footer_text = 'Current mech health: ' .
            $character[ 'mech' ][ 'health' ] . ' / ' .
            $character[ 'mech' ][ 'health_max' ] . '. ' .
            'Current mech heat: ' . $heat . '%.';
    }

    echo '    </div>';
    echo '    <div id="footer"><div class="container">';
    echo '<p class="text-muted">' . $footer_text . '</p>';
    echo '    </div></div>';
    echo '<script src="' . GAME_URL . 'style/popup.js"></script>';
    echo '<script src="' . GAME_URL . 'style/jquery.min.js"></script>';
    echo '<script src="' . GAME_URL . 'style/bootstrap.min.js"></script>' .
         "\n";
    echo '</body></html>';
}

add_action( 'game_header', 'sc_header' );
add_action( 'game_footer', 'sc_footer' );



function sc_tip_print() {
    global $character;

    if ( FALSE == $character ) {
        return;
    }

    $tip = character_meta( sc_meta_type_character, SC_CHARACTER_TIP );

    if ( 0 < strlen( $tip ) ) {
        echo( $tip );
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_CHARACTER_TIP, '' );
    }
}

add_action_priority( 'do_page_content', 'sc_tip_print' );

function sc_about() {
    global $game;

    if ( strcmp( 'about', $game->get_action() ) ) {
       return;
    }

    echo '<h1>BOB SAGET</h1>';
}

function sc_contact() {
    global $game;

    if ( strcmp( 'contact', $game->get_action() ) ) {
       return;
    }

    echo '<h1>OH BOB SAGET</h1>';
}

add_action( 'do_page_content', 'sc_about' );
add_action( 'do_page_content', 'sc_contact' );


function sc_fullcharacter() {
    global $character;

    ensure_character_mech();
}

add_action( 'full_character', 'sc_fullcharacter' );

function sc_mech_save() {
    global $character, $mech_update_meta;

    if ( TRUE == $mech_update_meta ) {
        sc_pack_mech();
    }
}

add_action( 'game_footer', 'sc_mech_save' );



function sc_character_load() {
    global $character;

    $default_obj = array(
    );

    foreach ( $default_obj as $k => $v ) {
        if ( ! isset( $character[ $k ] ) ) {
            $character[ $k ] = $v;
        }
    }
}

add_action( 'character_load', 'sc_character_load' );

function sc_unpack_mech( $s ) {
    $obj = array();

    $keys = array(
        'model', 'maker', 'value', 'armour',
    );
    foreach ( $keys as $k ) {
        $obj[ $k ] = '';
    }

    $s_obj = explode( ';', $s );
    foreach ( $s_obj as $ss ) {
        $ss_obj = explode( '=', $ss );
        $obj[ $ss_obj[ 0 ] ] = $ss_obj[ 1 ];
    }

    return $obj;
}

function sc_pack_mech() {
    global $character;

    $mech_obj = array();
    foreach ( $character[ 'mech' ] as $k => $v ) {
        $mech_obj[] = $k . '=' . $v;
    }

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_CURRENT_MECH, implode( ';', $mech_obj ) );
}

function ensure_character_mech() {
    global $character;

    if ( ! isset( $character[ 'mech' ] ) ) {
        $character[ 'mech' ] = sc_unpack_mech(
            character_meta( sc_meta_type_character, SC_CURRENT_MECH ) );
    }
}

function get_character_mech_items() {
    global $character;

    $id_obj = array();
    $key_obj = sc_mech_get_slots();

    foreach ( array_keys( $key_obj ) as $k ) {
        $id_obj[] = $character[ 'mech' ][ $k ];
    }
    $item_obj = get_items_from_array( $id_obj );

    return $item_obj;
}

function sc_item_popup_str( $item ) {
    return '<a href="#" onmouseover="popup(\'' .
           '<span class=&quot;item_name&quot;>' . $item[ 'name' ] .
           '</span>' .
           '<hr><span>' . $item[ 'description' ] . '</span>' .
           '\')" onmouseout="popout()" class="item">' . $item[ 'name' ] .
           '</a>';
}

function sc_explode_meta( $s ) {
    $meta_obj = array();
    $s_obj = explode( ';', $s );
    foreach ( $s_obj as $x ) {
        $x = explode( '=', $x );
        $meta_obj[ $x[ 0 ] ] = $x[ 1 ];
    }
    return $meta_obj;
}

function sc_checkbox( $name, $value, $label ) {
    return '<div class="clearfix">' .
           '<div class="btn-group form-control no_pad" data-toggle="buttons">'  .
           '<label class="btn btn-primary form-control no_pad">' .
           '<input type="checkbox" name="' . $name .
               '" value="' . $value . '">' . $label .
           '</label></div></div>';
}

function sc_item_string( $item ) {
    return '<a href="#" onmouseover="popup(\'' .
           '<span class=&quot;item_name&quot;>' . $item[ 'name' ] .
           '</span><hr><span>' . $item[ 'description' ] . '</span>' .
           '\')" onmouseout="popout()" class="item">' . $item[ 'name' ] .
           '</a>';
}

function sc_buy_item() {
    global $character;

    $item = $GLOBALS[ 'game_buy_item' ];
    $item_meta = explode_meta( $item[ 'item_meta' ] );

    if ( ! isset( $item_meta[ 'buy' ] ) ) {
        $GLOBALS[ 'game_buy_item' ] = FALSE;
        return;
    }

    $new_credits = intval( character_meta(
        sc_meta_type_character, SC_CHARACTER_CREDITS ) ) -
        intval( $item_meta[ 'buy' ] );

    if ( $new_credits >= 0 ) {
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_CHARACTER_CREDITS, $new_credits );
        update_character_meta( $character[ 'id' ], sc_meta_type_character,
            SC_CHARACTER_TIP, '<h1>Purchased: ' . $item[ 'name' ] .
            ' for ' . $item_meta[ 'buy' ] . ' credits</h1>' );
    } else {
        $GLOBALS[ 'game_buy_item' ] = FALSE;
    }
}

add_action( 'buy_item', 'sc_buy_item' );

function sc_sell_item() {
    global $character;

    $item = $GLOBALS[ 'game_sell_item' ];
    $item_meta = explode_meta( $item[ 'item_meta' ] );

    if ( ! isset( $item_meta[ 'sell' ] ) ) {
        $GLOBALS[ 'game_sell_item' ] = FALSE;
        return;
    }

    $new_credits = intval( character_meta(
        sc_meta_type_character, SC_CHARACTER_CREDITS ) ) +
        intval( $item_meta[ 'sell' ] );

    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_CHARACTER_CREDITS, $new_credits );
    update_character_meta( $character[ 'id' ], sc_meta_type_character,
        SC_CHARACTER_TIP, '<h1>Sold: ' . $item[ 'name' ] .
        ' for ' . $item_meta[ 'sell' ] . ' credits</h1>' );
}

add_action( 'sell_item', 'sc_sell_item' );
