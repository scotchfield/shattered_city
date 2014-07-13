<?php

global $valid_predicates;

array_push( $valid_predicates,
    'sc_predicate_mech_slot'
);

global $valid_functions;

array_push( $valid_functions,
    'sc_progress_character_quest_completed',
    'sc_progress_mech_slot',
    'sc_progress_diff_meta'
);


function sc_render_progress( $complete, $text ) {
    echo( '<h3 class="objective_' );
    if ( $complete ) {
        echo( 'complete' );
    } else {
        echo( 'incomplete' );
    }
    echo( '">' . $text . '</h3>' );
}

function sc_render_progress_number( $complete, $text, $n_done, $n_remain ) {
    echo( '<h3 class="objective_' );
    if ( $complete ) {
        echo( 'complete' );
    } else {
        echo( 'incomplete' );
    }
    echo( '">' . $text . ' (' . $n_done . ' / ' . $n_remain . ')</h3>' );
}

function sc_progress_character_quest_completed( $text, $quest_id ) {
    global $character;

    $completed_quests = get_character_completed_quests();
    $complete = FALSE;

    if ( isset( $completed_quests[ $quest_id ] ) ) {
        $complete = TRUE;
    }

    sc_render_progress( $complete, $text );

    return $complete;
}

function sc_predicate_mech_slot( $slot, $item_id ) {
    global $character;

    ensure_character_mech();
    $complete = FALSE;

    if ( $item_id == $character[ 'mech' ][ $slot ] ) {
        $complete = TRUE;
    }

    return $complete;
}

function sc_progress_mech_slot( $text, $slot, $item_id ) {
    $complete = sc_predicate_mech_slot( $slot, $item_id );

    sc_render_progress( $complete, $text );

    return $complete;
}

function sc_progress_diff_meta( $text, $key_type, $meta_key,
                                $quest_meta_key, $diff ) {
    global $character, $quest_current;

    $active_quests = get_character_active_quests();
    $active_quest = $character[ 'quests' ][
        $active_quests[ $quest_current[ 'id' ] ] ];
    $quest_meta_obj = explode_meta( $active_quest[ 'quest_meta' ] );

    $n = min( $diff, intval( character_meta( $key_type, $meta_key ) ) - intval(
                         $quest_meta_obj[ $quest_meta_key ] ) );
    $complete = $n >= $diff;

    sc_render_progress_number( $complete, $text, $n, $diff );

    return $complete;
}