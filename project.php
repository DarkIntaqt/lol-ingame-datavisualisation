<?php

$key = 'YOUR API KEY';
$region = 'REGION';

if(!isset($_GET['summonername'])) {
  ?>
  <link rel="stylesheet" href="style.css">
  <h1>Is Ingame?</h1>
  <form class="" action="?" method="get">
    <input type="text" name="summonername" placeholder="Summonername" autocomplete="off" class="summonername">
    <input type="submit" value="Search" class="submit">
  </form>

  <?php
  die();
}



function req($url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  $return = curl_exec($ch);
  curl_close($ch);
  return json_decode($return,true);
}

$versions = file_get_contents('https://ddragon.leagueoflegends.com/api/versions.json');
$versions = json_decode($versions, true);

$latest = $versions['0'];

$json = file_get_contents('https://ddragon.leagueoflegends.com/cdn/'.$latest.'/data/en_US/champion.json');
$json = json_decode($json, true);
$list = $json['data'];


function getChampionInfo($id = 1) {
  global $list;
  foreach ($list as $key => $value) {
    if($list[$key]['key'] == $id) {
      return $list[$key];
    }
  }
  return false;
}

$summs = req('http://ddragon.leagueoflegends.com/cdn/'.$latest.'/data/en_US/summoner.json')['data'];

function getSummInfo($id = 1) {
  global $summs;
  foreach ($summs as $key => $value) {
    if($summs[$key]['key'] == $id) {
      return $summs[$key];
    }
  }
  return false;
}

$username = $_GET['summonername'];
$userdata = req('https://'.$region.'.api.riotgames.com/lol/summoner/v4/summoners/by-name/'.$username.'?api_key='.$key);


//print_r($userdata);

$livegame = req('https://'.$region.'.api.riotgames.com/lol/spectator/v4/active-games/by-summoner/'.$userdata['id'].'?api_key='.$key);


//print_r($livegame);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <title>Is <?php echo $userdata['name']; ?> Ingame?</title>
  </head>
  <body>
    <h1><?php

    if(!empty($userdata['status'])) {
      die('Summoner does not exist!</h1><h2><a href="?">Search for another summoner! (click)</a></h2></body></html>');
    }

    if(!empty($livegame['status'])) {
      die('Summoner is not ingame</h1><h2><a href="?">Search for another summoner! (click)</a></h2></body></html>');
    }

    echo $userdata['name'];

    ?> is ingame!</h1>

    <p><?php

    if(($livegame['gameLength'] + 150) < 0) {
      echo 'in loading screen...';
    } else {
      echo round(($livegame['gameLength'] + 150) / 60).' Minutes Ingame';
    }

     ?></p>


     <div class="team1">
       <?php
       foreach ($livegame['participants'] as $k => $value) {
         if($value['teamId'] == 100) {
          $ranked = req('https://'.$region.'.api.riotgames.com/lol/league/v4/entries/by-summoner/'.$value['summonerId'].'?api_key='.$key);
          if($ranked[0]['queueType'] == "RANKED_SOLO_5x5") {
            $sd = $ranked[0];
            if($ranked[1] != NULL) {
              $flex = $ranked[1];
            } else {
              $flex = array("tier"=>"Unranked","rank"=>"","leaguePoints"=>"0","wins"=>0,"losses"=>0);
            }
          } else {
            $flex = $ranked[0];
            if($ranked[1] != NULL) {
              $sd = $ranked[1];
            } else {
              $sd = array("tier"=>"Unranked","rank"=>"","leaguePoints"=>"0","wins"=>0,"losses"=>0);
            }
          }
          if($ranked == NULL) {
            $sd = array("tier"=>"Unranked","rank"=>"","leaguePoints"=>"0","wins"=>0,"losses"=>0);
            $flex = array("tier"=>"Unranked","rank"=>"","leaguePoints"=>"0","wins"=>0,"losses"=>0);
          }

          $spell1 = getSummInfo($value['spell1Id']);
          $spell2 = getSummInfo($value['spell2Id']);

          echo '<div class="user-1">';
         ?>

          <img src="https://ddragon.leagueoflegends.com/cdn/<?php echo $latest; ?>/img/champion/<?php echo getChampionInfo($value['championId'])['image']['full']; ?>">
          <div class="sp">
            <img src="http://ddragon.leagueoflegends.com/cdn/<?php echo $latest; ?>/img/spell/<?php echo $spell1['image']['full']; ?>" class="spell">
            <img src="http://ddragon.leagueoflegends.com/cdn/<?php echo $latest; ?>/img/spell/<?php echo $spell2['image']['full']; ?>" class="spell">
          </div>
          <h2><?php echo $value['summonerName']; ?></h2>
          <h3><?php echo $sd['tier']. ' '.$sd['rank']; ?></h3>
         <?php
         echo '</div>';
          }
        }
       ?>
     </div>

     <div class="team2">
       <?php
       foreach ($livegame['participants'] as $k => $value) {
         if($value['teamId'] == 200) {
          $ranked = req('https://'.$region.'.api.riotgames.com/lol/league/v4/entries/by-summoner/'.$value['summonerId'].'?api_key='.$key);
          if($ranked[0]['queueType'] == "RANKED_SOLO_5x5") {
            $sd = $ranked[0];
            if($ranked[1] != NULL) {
              $flex = $ranked[1];
            } else {
              $flex = array("tier"=>"Unranked","rank"=>"","leaguePoints"=>"0","wins"=>0,"losses"=>0);
            }
          } else {
            $flex = $ranked[0];
            if($ranked[1] != NULL) {
              $sd = $ranked[1];
            } else {
              $sd = array("tier"=>"Unranked","rank"=>"","leaguePoints"=>"0","wins"=>0,"losses"=>0);
            }
          }
          if($ranked == NULL) {
            $sd = array("tier"=>"Unranked","rank"=>"","leaguePoints"=>"0","wins"=>0,"losses"=>0);
            $flex = array("tier"=>"Unranked","rank"=>"","leaguePoints"=>"0","wins"=>0,"losses"=>0);
          }

          $spell1 = getSummInfo($value['spell1Id']);
          $spell2 = getSummInfo($value['spell2Id']);

          echo '<div class="user-2">';
         ?>

          <img src="https://ddragon.leagueoflegends.com/cdn/<?php echo $latest; ?>/img/champion/<?php echo getChampionInfo($value['championId'])['image']['full']; ?>">
          <div class="sp">
            <img src="http://ddragon.leagueoflegends.com/cdn/<?php echo $latest; ?>/img/spell/<?php echo $spell1['image']['full']; ?>" class="spell">
            <img src="http://ddragon.leagueoflegends.com/cdn/<?php echo $latest; ?>/img/spell/<?php echo $spell2['image']['full']; ?>" class="spell">
          </div>
          <h2><?php echo $value['summonerName']; ?></h2>
          <h3><?php echo $sd['tier']. ' '.$sd['rank']; ?></h3>
         <?php
         echo '</div>';
          }
        }
       ?>
     </div>
     <h2><a href="?">Search for another summoner! (click)</a></h2>

  </body>
</html>