<?

function sc_map_print() {
    global $game, $user, $character;

    if ( strcmp( 'map', $game->get_action() ) ) {
        return;
    }

?>
<h3>State of the City</h3>
<div class="row">
  <div class="col-md-4">
    <h4>Primary Locations</h4>
    <ul>
      <li><a href="?action=zone&zone_tag=cydonia">Cydonia
          Heavy Industries</a></li>
      <li><a href="?action=zone&zone_tag=minstall">Mech Installations</a></li>
    </ul>
    <h4>Goods and Services</h4>
    <ul>
      <li><a href="?action=zone&zone_tag=mequip">Mech
          Equipment Storeroom</a></li>
    </ul>
    <h4>Recreational Activities</h4>
    <ul>
      <li><a href="?action=zone&zone_tag=casino">City Casino</a></li>
    </ul>
  </div>
  <div class="col-md-4">
    <h4>Combat Locations</h4>
    <ul>
      <li><a href="?action=zone&zone_tag=titanrift">Titan's Rift</a></li>
      <li><a href="?action=zone&zone_tag=epsilon">The Epsilon Rift</a></li>
    </ul>
  </div>
  <div class="col-md-4">
    <h4>Helpful Links</h4>
    <ul>
      <li><a href="?action=allquests">Find all available quests</a></li>
    </ul>
  </div>
</div>
<?php
}

add_action( 'do_page_content', 'sc_map_print' );
