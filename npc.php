<?php

function sc_npc_content() {
    global $game, $character;

    if ( strcmp( 'npc', $game->get_action() ) ) {
       return;
    }

    $zone_id = GAME_STARTING_ZONE;
    if ( '' != character_meta( sc_meta_type_character, SC_CURRENT_ZONE ) ) {
        $zone_id = character_meta( sc_meta_type_character, SC_CURRENT_ZONE );
    }

    $zone = get_zone( $zone_id );

    $npc = get_npc_by_id( $_GET[ 'id' ] );

    echo( '<div class="row"><div class="col-xs-8">' .
          '<h3>' . $npc[ 'npc_name' ] . '</h3>' .
          '<p class="lead">' . $npc[ 'npc_description' ] . '</p>' .
          '</div><div class="col-xs-4">' );

    echo( '<h4 class="text-right">Go somewhere else</h4><ul>' .
          '<li class="text-right"><a href="' . GAME_URL .
          '?action=zone&amp;zone_tag=' .
          $zone[ 'zone_tag' ] . '">' . $zone[ 'zone_title' ] .
          '</a></li></ul></div>' );

    echo( '</div><div class="row text-center">' );

    $quest_obj = get_available_quests_by_npc( $npc[ 'id' ] );
    $quest_id = 0;
    if ( isset( $_GET[ 'quest_id' ] ) ) {
        $quest_id = intval( $_GET[ 'quest_id' ] );
    }

    if ( isset( $quest_obj[ $quest_id ] ) ) {

        ensure_character_quests();
        $character_quests = get_character_completed_quests();

        $quest = $quest_obj[ $quest_id ];

        if ( isset( $_GET[ 'quest_complete' ] ) ) {
            $show_complete = FALSE;
            $time_valid = time() - 60;
            foreach ( $character[ 'quests' ] as $q ) {
                if ( ( $q[ 'quest_id' ] == $quest_id ) &&
                     ( $q[ 'completed' ] > $time_valid ) ) {
                    $show_complete = TRUE;
                }
            }

            if ( $show_complete ) {
                echo( '<h4>' . $quest[ 'end_text' ] . '</h4>' );
            }

            echo( '<h4><a href="' . GAME_URL . '?action=npc&id=' .
                  $npc[ 'id' ] . '">Back to the available quests</a></h4>' );

        } else if ( ! isset( $character_quests[ $quest_id ] ) ) {

            $current_quest_state = get_character_active_quest( $quest_id );

            $quest[ 'start_text' ] = str_replace(
                "\n", '<br>', $quest[ 'start_text' ] );

            echo( '<h2>' . $quest[ 'name' ] .
                  '</h2><hr width="50%"><h4>' .
                  $quest[ 'start_text' ] . '</h4>' );

            if ( FALSE == $current_quest_state ) {
                echo( '<h3><a href="' . GAME_URL .
                      'game-setting.php?setting=quest_accept&id=' .
                      $quest[ 'id' ] . '">Accept the Quest</a> or ' .
                      '<a href="' . GAME_URL . '?action=npc&id=' .
                      $npc[ 'id' ] . '">Ignore the Quest</a></h3>' );
            } else {

                $quest_meta_obj = explode_meta(
                    $current_quest_state[ 'quest_meta' ] );

                $quest_complete = character_quest_progress( $quest );

                // todo: remove eval
                //eval( $quest[ 'quest_progress' ] );

                // todo: remove eval
                /*$quest_complete = eval( $quest[ 'quest_complete' ] );*/

                if ( $quest_complete ) {
                    echo( '<h2><a href="' . GAME_URL .
                          'game-setting.php?setting=quest_complete&id=' .
                          $quest_id . '">Complete the quest!</a></h2>' );
                }
            }

        }

    } elseif ( count( $quest_obj ) > 0 ) {

        $completed_quests = get_character_completed_quests();

        $quest_count = 0;

        echo( '<h3>Quests available</h3>' );
        foreach ( $quest_obj as $quest ) {
            if ( isset( $completed_quests[ $quest[ 'id' ] ] ) ) {
                continue;
            }

            $quest_count += 1;
            echo( '<h4><a href="' . GAME_URL . '?action=npc&id=' .
                  $npc[ 'id' ] . '&quest_id=' . $quest[ 'id' ] .
                  '">' . $quest[ 'name' ] . '</a></h4>' );
        }

        if ( 0 == $quest_count ) {
            echo( '<h4>None at this time</h4>' );
        }

    }

    echo( '</div>' );
}

add_action( 'do_page_content', 'sc_npc_content' );



