<?php

function sc_profile_content() {
    global $game, $character;

    if ( strcmp( 'profile', $game->get_action() ) ) {
       return;
    }

    ensure_character_achievements();
    ensure_character_mech();

?><div class="row">
  <div class="col-md-6">
    <h3>Character</h3>

    <dl class="dl-horizontal">
      <dt>Name</dt>
      <dd><?php echo character_meta(
          sc_meta_type_character, SC_CHARACTER_NAME ); ?>&nbsp;</dd>
      <dt>Age</dt>
      <dd><?php echo character_meta(
          sc_meta_type_character, SC_CHARACTER_AGE ); ?>&nbsp;</dd>
      <dt>Biography</dt>
      <dd><?php echo character_meta(
          sc_meta_type_character, SC_CHARACTER_BIO ); ?>&nbsp;</dd>
      <dt>Credits</dt>
      <dd><?php echo character_meta(
          sc_meta_type_character, SC_CHARACTER_CREDITS ); ?>&nbsp;</dd>
    </dl>

    <h3>Achievements</h3>
<?php
    if ( 0 == count( $character[ 'achievements' ] ) ) {
        echo( '<h4>None yet!</h4>' );
    } else {
        echo( '<dl class="dl-horizontal">' );
        foreach ( $character[ 'achievements' ] as $achieve ) {
            echo( '<dt>' . $achieve[ 'achieve_title' ] . '</dt><dd>' .
                  $achieve[ 'achieve_text' ] . '</dd><dd>' .
                  date( 'F j, Y, g:ia', $achieve[ 'timestamp' ] ) .
                  '</dd>' );
        }
        echo( '</dl>' );
    }
?>
  </div>
  <div class="col-md-6">
    <h3>Mech</h3>

    <dl class="dl-horizontal">
      <dt>Model</dt>
      <dd><?php echo $character[ 'mech' ][ 'model' ]; ?>&nbsp;</dd>
      <dt>Maker</dt>
      <dd><?php echo $character[ 'mech' ][ 'maker' ]; ?>&nbsp;</dd>
      <dt>Value</dt>
      <dd><?php echo $character[ 'mech' ][ 'value' ]; ?>&nbsp;</dd>
      <dt>Armour</dt>
      <dd><?php echo $character[ 'mech' ][ 'armour' ]; ?>&nbsp;</dd>
    </dl>
  </div>
</div><?php
}

add_action( 'do_page_content', 'sc_profile_content' );


function sc_questlog_content() {
    global $game, $character;

    if ( strcmp( 'questlog', $game->get_action() ) ) {
       return;
    }

    ensure_character_quests();

    $quest_ids = array();
    foreach ( $character[ 'quests' ] as $q ) {
        if ( 0 == $q[ 'completed' ] ) {
            $quest_ids[] = intval( $q[ 'quest_id' ] );
        }
    }
    $quest_obj = get_quests_by_ids( $quest_ids );

    echo( '<div class="row text-center"><h2>Quest Log</h2>' );

    foreach ( $quest_obj as $quest ) {
        echo( '<h4><a href="' . GAME_URL . '?action=npc&id=' .
              $quest[ 'npc_id' ] . '&quest_id=' . $quest[ 'id' ] . '">' .
              $quest[ 'name' ] . '</a></h4><h5>' .
              $quest[ 'start_text' ] . '</h5>' );
    }

    if ( 0 == count( $quest_obj ) ) {
        echo( '<h4>No quests at this time!</h4>' );
    }

    echo( '</div>' );
}

add_action( 'do_page_content', 'sc_questlog_content' );