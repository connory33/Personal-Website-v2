<!-- TEMPLATE FOR GAME DETAIL PAGES - GETS ID SELECTED ON GAMES PAGE AND USES IT TO QUERY DATABASE FOR ADDITIONAL GAME DETAILS -->
<?php include('db_connection.php'); ?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />

  </head>
  <!-- Header -->
  <?php include 'header.php'; ?>
  <body>
    <div class="bg-neutral-700 text-white" style='align-items: flex-start'>
      <div style='margin-left: 10px; margin-right: 10px'>
        <?php

          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);
    ########## GET ALL PLAYER DATA TO USE FOR THE PAGE ##########

          // Check if the 'game_id' is passed in the URL
          if (isset($_GET['player_id'])) {
              $player_id = $_GET['player_id'];

              $sql = "SELECT * 
                      FROM nhl_players 
                      WHERE playerID=$player_id";
              $playerInfo = mysqli_query($conn, $sql);

            ##### Iterate through all player results and assign data to variables #####
              while ($row = mysqli_fetch_assoc($playerInfo)) {
                // print_r($row);
                ### Basic Info ###
                  $name = $row['firstName'] . ' ' . $row['lastName'];
                  $sweaterNumber = $row['sweaterNumber'];
                  $position = $row['position'];
                  $headshot = $row['headshot'];
                  $isActiveRaw = strtolower(trim($row['isActive']));
                  if ($isActiveRaw === 'false') {
                      $active = "No";
                  } else {
                      $active = "Yes";
                  }

                  if ($active == "Yes") {
                    $teamName = $row['fullTeamName'];
                    $teamLogo = $row['teamLogo'];
                    $teamID = $row['currentTeamId'];
                  } else {
                    $teamName = 'N/A';
                    $teamLogo = 'N/A';
                    $teamID = 'N/A';
                  } 
                  
                  $badgesLogos = $row['badgesLogos'];
                  $badgesNames = $row['badgesNames'];
                  $heroImage = $row['heroImage'];

                  if ($row['heightInInches']) {
                    $heightIn = $row['heightInInches'];
                    $heightFt = floor($heightIn/12);
                    $heightInches = ($heightIn % 12);
                    $heightIn = $heightFt . "' " . $heightInches . '"';
                  } else {
                    $heightIn = '?';
                  }
                  if ($row['heightInCentimeters']) {
                    $heightCm = $row['heightInCentimeters'];
                  } else {
                    $heightCm = intval($heightIn*2.54);
                  }
                  if ($row['weightInPounds']) {
                    $weightLb = $row['weightInPounds'];
                  } else {
                    $weightLb = '?';
                  }
                  if ($row['weightInKilograms']) {
                    $weightKg = $row['weightInKilograms'];
                  } else {
                    $weightKg = '?';
                  }
                  
                  $birthDate = date('F j, Y',strtotime($row['birthDate']));
                  $birthCity = $row['birthCity'];
                  $birthStateProvince = $row['birthStateProvince'];
                  $birthCountry = $row['birthCountry'];
                  $shootsCatches = $row['shootsCatches'];
                  if ($row['draftYear'] == '') {
                    $draftYear = 'N/A';
                    $draftTeam = 'N/A';
                    $draftRound = 'N/A';
                    $draftPickInRound = 'N/A';
                    $draftOverall = 'N/A';
                  } else {
                    $draftYear = $row['draftYear'];
                    $draftTeam = $row['draftTeam'];
                    $draftRound = $row['draftRound'];
                    $draftPickInRound = $row['draftPickInRound'];
                    $draftOverall = $row['draftOverall'];
                  }
                  if ($row['inHHOF']) {
                    $inHHOF = '<b>In HOF:</b> Yes';
                  } else {
                    $inHHOF = '<b>In HOF:</b> No';
                  }

                  # checks if a value (i.e. # of assists) is an empty string and changes to 0 if so
                  function fillEmptyStats($value) {
                    return $value === '' ? '0' : $value; # LEARN HOW THIS SYNTAX WORKS
                  }

                ### Featured Season Stats ###
                  $featuredSeason = $row['featuredSeason'];
                  $featuredSeasonGP = fillEmptyStats($row['featuredSeasonGP']);
                  $featuredSeasonAssists = fillEmptyStats($row['featuredSeasonAssists']);
                  $featuredSeasonGWG = fillEmptyStats($row['featuredSeasonGWG']);
                  $featuredSeasonGoals = fillEmptyStats($row['featuredSeasonGoals']);
                  $featuredSeasonOTGoals = fillEmptyStats($row['featuredSeasonOTGoals']);
                  $featuredSeasonPIM = fillEmptyStats($row['featuredSeasonPIM']);
                  $featuredSeasonPlusMinus = fillEmptyStats($row['featuredSeasonPlusMinus']);
                  $featuredSeasonPts = fillEmptyStats($row['featuredSeasonPts']);
                  $featuredSeasonPPG = fillEmptyStats($row['featuredSeasonPPG']);
                  $featuredSeasonPPPoints = fillEmptyStats($row['featuredSeasonPPPoints']);
                  $featuredSeasonShootingPct = fillEmptyStats($row['featuredSeasonShootingPct']);
                  $featuredSeasonSHG = fillEmptyStats($row['featuredSeasonSHG']);
                  $featuredSeasonSHPts = fillEmptyStats($row['featuredSeasonSHPts']);
                  $featuredSeasonShots = fillEmptyStats($row['featuredSeasonShots']);
                    # Goalie
                  $featuredSeasonGAA = fillEmptyStats($row['featuredSeasonGAA']);
                  $featuredSeasonLosses = fillEmptyStats($row['featuredSeasonLosses']);
                  $featuredSeasonSO = fillEmptyStats($row['featuredSeasonSO']);
                  $featuredSeasonTies = fillEmptyStats($row['featuredSeasonTies']);
                  $featuredSeasonWins = fillEmptyStats($row['featuredSeasonWins']);
                  $featuredSeasonGS = fillEmptyStats($row['featuredSeasonGS']);
                  $featuredSeasonGA = fillEmptyStats($row['featuredSeasonGA']);
                  $featuredSeasonSavePct = fillEmptyStats($row['featuredSeasonSavePct']);
                  $featuredSeasonOTLosses = fillEmptyStats($row['featuredSeasonOTLosses']);
                  $featuredSeasonShotsAgainst = fillEmptyStats($row['featuredSeasonShotsAgainst']);
                  
                ### Regular Season Stats ###
                  $regSeasonCareerGP = fillEmptyStats($row['regSeasonCareerGP']);
                  $regSeasonCareerAssists = fillEmptyStats($row['regSeasonCareerAssists']);
                  $regSeasonCareerGWG = fillEmptyStats($row['regSeasonCareerGWG']);
                  $regSeasonCareerGoals = fillEmptyStats($row['regSeasonCareerGoals']);
                  $regSeasonCareerOTGoals = fillEmptyStats($row['regSeasonCareerOTGoals']);
                  $regSeasonCareerPIM = fillEmptyStats($row['regSeasonCareerPIM']);
                  $regSeasonCareerPlusMinus = fillEmptyStats($row['regSeasonCareerPlusMinus']);
                  $regSeasonCareerPts = fillEmptyStats($row['regSeasonCareerPts']);
                  $regSeasonCareerPPG = fillEmptyStats($row['regSeasonCareerPPG']);
                  $regSeasonCareerPPPoints = fillEmptyStats($row['regSeasonCareerPPPoints']);
                  $regSeasonCareerShootingPct = fillEmptyStats($row['regSeasonCareerShootingPct']);
                  $regSeasonCareerSHG = fillEmptyStats($row['regSeasonCareerSHG']);
                  $regSeasonCareerSHPts = fillEmptyStats($row['regSeasonCareerSHPts']);
                  $regSeasonCareerShots = fillEmptyStats($row['regSeasonCareerShots']);
                    # Goalie
                  $regSeasonCareerGAA = fillEmptyStats($row['regSeasonCareerGAA']);
                  $regSeasonCareerLosses = fillEmptyStats($row['regSeasonCareerLosses']);
                  $regSeasonCareerSO = fillEmptyStats($row['regSeasonCareerSO']);
                  $regSeasonCareerTies = fillEmptyStats($row['regSeasonCareerTies']);
                  $regSeasonCareerWins = fillEmptyStats($row['regSeasonCareerWins']);
                  $regSeasonCareerGS = fillEmptyStats($row['regSeasonCareerGS']);
                  $regSeasonCareerGA = fillEmptyStats($row['regSeasonCareerGA']);
                  $regSeasonCareerSavePct = fillEmptyStats($row['regSeasonCareerSavePct']);
                  $regSeasonCareerOTLosses = fillEmptyStats($row['regSeasonCareerOTLosses']);
                  $regSeasonCareerShotsAgainst = fillEmptyStats($row['regSeasonCareerShotsAgainst']);

                ### Playoff Stats ###
                  $playoffsCareerAssists = fillEmptyStats($row['playoffsCareerAssists']);
                  $playoffsCareerGP = fillEmptyStats($row['playoffsCareerGP']);
                  $playoffsCareerAssists = fillEmptyStats($row['playoffsCareerAssists']);
                  $playoffsCareerGWG = fillEmptyStats($row['playoffsCareerGWG']);
                  $playoffsCareerGoals = fillEmptyStats($row['playoffsCareerGoals']);
                  $playoffsCareerOTGoals = fillEmptyStats($row['playoffsCareerOTGoals']);
                  $playoffsCareerPIM = fillEmptyStats($row['playoffsCareerPIM']);
                  $playoffsCareerPlusMinus = fillEmptyStats($row['playoffsCareerPlusMinus']);
                  $playoffsCareerPts = fillEmptyStats($row['playoffsCareerPts']);
                  $playoffsCareerPPG = fillEmptyStats($row['playoffsCareerPPG']);
                  $playoffsCareerPPPoints = fillEmptyStats($row['playoffsCareerPPPoints']);
                  $playoffsCareerShootingPct = fillEmptyStats($row['playoffsCareerShootingPct']);
                  $playoffsCareerSHG = fillEmptyStats($row['playoffsCareerSHG']);
                  $playoffsCareerSHPts = fillEmptyStats($row['playoffsCareerSHPts']);
                  $playoffsCareerShots = fillEmptyStats($row['playoffsCareerShots']);
                    # Goalie
                  $playoffsCareerGAA = fillEmptyStats($row['playoffsCareerGAA']);
                  $playoffsCareerLosses = fillEmptyStats($row['playoffsCareerLosses']);
                  $playoffsCareerSO = fillEmptyStats($row['playoffsCareerSO']);
                  $playoffsCareerTies = fillEmptyStats($row['playoffsCareerTies']);
                  $playoffsCareerWins = fillEmptyStats($row['playoffsCareerWins']);
                  $playoffsCareerGS = fillEmptyStats($row['playoffsCareerGS']);
                  $playoffsCareerGA = fillEmptyStats($row['playoffsCareerGA']);
                  $playoffsCareerSavePct = fillEmptyStats($row['playoffsCareerSavePct']);
                  $playoffsCareerOTLosses = fillEmptyStats($row['playoffsCareerOTLosses']);
                  $playoffsCareerShotsAgainst = fillEmptyStats($row['playoffsCareerShotsAgainst']);
                  

                  $last5Games = $row['last5Games'];
                  $seasonTotals = $row['seasonTotals'];
                  $awardNames = $row['awardNames'];
                  $awardSeasons = $row['awardSeasons'];
                  $currentTeamRoster = $row['currentTeamRoster'];
            }

            $last5GameSQL = "SELECT 
                  player_last_5_games.*,
                  skaters_gamebygame_stats.goals AS skater_goals,
                  skaters_gamebygame_stats.assists AS skater_assists,
                  skaters_gamebygame_stats.points AS skater_points,
                  skaters_gamebygame_stats.plusMinus AS skater_plusMinus,
                  skaters_gamebygame_stats.pim AS skater_pim,
                  skaters_gamebygame_stats.hits AS skater_hits,
                  skaters_gamebygame_stats.sog AS skater_sog,
                  skaters_gamebygame_stats.powerPlayGoals AS skater_ppg,
                  skaters_gamebygame_stats.faceoffWinningPctg AS skater_faceoffWinningPctg,
                  skaters_gamebygame_stats.toi AS skater_toi,
                  skaters_gamebygame_stats.blockedShots AS skater_blockedShots,
                  skaters_gamebygame_stats.shifts AS skater_shifts,
                  skaters_gamebygame_stats.giveaways AS skater_giveaways,
                  skaters_gamebygame_stats.takeaways AS skater_takeaways,
                  goalies_gamebygame_stats.*
              FROM player_last_5_games
              LEFT JOIN skaters_gamebygame_stats 
                  ON player_last_5_games.playerId = skaters_gamebygame_stats.playerId
                  AND player_last_5_games.game_id = skaters_gamebygame_stats.gameID
              LEFT JOIN goalies_gamebygame_stats 
                  ON player_last_5_games.playerId = goalies_gamebygame_stats.playerId
              WHERE player_last_5_games.playerId = '$player_id'";

            $last5GameInfo = mysqli_query($conn, $last5GameSQL);
            
            // echo $last5GameSQL;
          echo "<br>";

    ########## DISPLAY ALL RESULTS ##########

        ### Flexbox for player name/number/active/id and headshot/team logo - aligns them side-by-side ###
          echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;'>";
            // Left side: Name and status
            echo "<div>";
              echo "<h1 class='text-4xl' style='margin-left: 20px; margin-top: 20px'>" . $name . " #" . $sweaterNumber . "</h1>";
              if ($active == 'Yes') {
                echo "<p style='color: green; margin-left: 20px; margin-top: 20px' class='font-medium text-2xl'>Active - " . $player_id . "</p>";
              } else {
                echo "<p style='color: red; font-weight: bold; margin-left: 20px; margin-top: 20px'>Not Active - " . $player_id . "</p>";
              }
            echo "</div>";
            // Right side: Headshot and logo
            echo "<div style='display: flex; align-items: center; gap: 5px;'>";
              if ($badgesLogos != 'false' and $badgesLogos != '') {
                echo "<img src='" . htmlspecialchars($badgesLogos) . "' alt='badge logo' style='height: 110px; margin-right: 15px'>";
              } else {
                echo "<p></p>";
              }
              if ($headshot != 'false' and $headshot != '' and $headshot != 'N/A') {
                echo "<img src='" . htmlspecialchars($headshot) . "' alt='headshot' style='height: 120px; margin-right: 15px; border-radius: 8px'>";
              } else {
                echo "<p></p>";
              }

              if ($teamLogo != 'false' and $teamLogo != '' and $teamLogo != 'N/A') {
                echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($teamID) . "'>" 
                . "<img src='" . htmlspecialchars($teamLogo) . "' alt='team logo' style='height: 120px; margin-right: 15px'>"
                . "</a>";
                          } else {
                echo "<p></p>";
              }
            echo "</div>";
          echo "</div>";

              // echo "<p>Current Team: " . $teamName . " <img src='" . htmlspecialchars($teamLogo) . "' alt='N/A' style='height: 65px;'></p>";
          

        ### Flexbox for hero image and player bio box ###
          echo "<div class='hero-bio-container gap-6'>";
            
              // Left side: Hero image container
            echo "<div>";
            echo "<img style = 'border: 1px solid #bcd6e7' src='" . htmlspecialchars($heroImage) . "' alt='heroImage' class='hero-bio-image'>";
            echo "</div>";

            // Right side: Bio box container
            echo "<div class='bio-box'>";
            echo "<h4 class='bio-header'>Player Bio</h4>";
          
            echo "<div class='bio-body'>";
          
              echo "<p><b>Height:</b> " . $heightIn . " / " . $heightCm . " cm</p>";
              echo "<p><b>Weight:</b> " . $weightLb . " lbs / " . $weightKg . " kg</p>";
              echo "<p><b>Birthdate:</b> " . $birthDate . "</p>";
          
              if ($birthStateProvince == '') {
                echo "<p><b>Birthplace:</b> " . $birthCity . " (" . $birthCountry . ")</p>";
              } else {
                echo "<p><b>Birthplace:</b> " . $birthCity . ", " . $birthStateProvince . " (" . $birthCountry . ")</p>";
              }
          
              echo "<p><b>Shoots/catches:</b> " . $shootsCatches . "</p>";
              echo "<p><b>Position:</b> " . $position . "</p>";
          
              if ($draftYear == 'N/A') {
                echo "<p><b>Draft Info:</b> Undrafted</p>";
              } else {
                echo "<p><b>Draft Info:</b> " . $draftYear . " Rd. " . $draftRound . " Pick " . $draftPickInRound .
                " (#" . $draftOverall . " Ovr.) (" . $draftTeam . ")</p>";
              }
          
              echo "<p>" . $inHHOF . "</p>";
          
              if (!empty($awardNames)) {
                $awardNamesArray = json_decode(str_replace("'", '"', $awardNames), true);
                $awardSeasonsArray = json_decode(str_replace("'", '"', $awardSeasons), true);
          
                if (is_array($awardNamesArray) && is_array($awardSeasonsArray)) {
                  echo "<div class='awards'><b>Awards:</b>";
                    for ($i = 0; $i < count($awardNamesArray); $i++) {
                        $award = $awardNamesArray[$i];
                        $seasonsRaw = $awardSeasonsArray[$i];
          
                        $formattedSeasons = array_map(function($s) {
                            return substr($s, 0, 4) . "–" . substr($s, 4);
                        }, $seasonsRaw);
          
                        $seasonString = implode(", ", $formattedSeasons);
                        echo "<p>" . $award . " (" . $seasonString . ")</p>";
                    }
                  echo "</div>";
                } else {
                    echo "<p><b>Awards:</b> None</p>";
                }
              } else {
                  echo "<p><b>Awards:</b> None</p>";
              }
          
            echo "</div>"; // close bio-body
          echo "</div>"; // close bio-box
          


          echo "</div><br><br>";

      ##### Stat Tables #####

        // Start OUTER WRAPPER
        echo "<div class='flex flex-col lg:flex-row gap-10'>";

        // LEFT COLUMN (top 3 tables stacked)
        echo "<div style='margin-top:14px' class='flex flex-col lg:basis-[43%]'>";
        
        ### Last 5 Games ###
        echo "<h3 class='text-center text-2xl text-white'>Last 5 Games</h3>";      
        echo "<table class='last-5-games-table default-zebra-table'>";
        echo "<colgroup>";
        echo "<col class='last-5-games-id'>";
        echo "<col class='last-5-games-team'>";
        echo "<col class='last-5-games-opponent'>";
        echo "<col class='last-5-games-home-road'>";
        echo "</colgroup>";
        echo "<thead>";
        echo "<tr>";
            echo "<th>Game ID</th>";
            echo "<th>Team</th>";
            echo "<th>Opponent</th>";
            echo "<th>Home/Road</th>";
            echo "<th>Goals</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($row = mysqli_fetch_assoc($last5GameInfo)) {
          $last5_games_id = isset($row['game_id']) ? $row['game_id'] : null;
          $last5_games_date = isset($row['game_date']) ? $row['game_date'] : null;
          $last5_games_team = isset($row['team']) ? $row['team'] : null;
          $last5_games_opponent = isset($row['opponent']) ? $row['opponent'] : null;
          $last5_games_homeRoad = isset($row['homeRoad']) ? $row['homeRoad'] : null;  
          $last5_games_goals = isset($row['skater_goals']) ? $row['skater_goals'] : null;

          echo "<tr>";
          echo "<td>" . $last5_games_id . "</td>";
          echo "<td>" . $last5_games_team . "</td>";
          echo "<td>" . $last5_games_opponent . "</td>";
          echo "<td>" . $last5_games_homeRoad . "</td>";
          echo "<td>" . $last5_games_goals . "</td>";
          echo "</tr>";

    }
    echo "</tbody>";
    echo "</table><br><br>";
  }


          ### Featured Season Stats ###
          $formatted_featuredSeason_1 = substr($featuredSeason, 0, 4);
          $formatted_featuredSeason_2 = substr($featuredSeason, 4);
        echo "<h3 class='text-center text-2xl text-white'>Featured Season Statistics (" .
         $formatted_featuredSeason_1 . "-" . $formatted_featuredSeason_2 . ")</h3>";
        
        if (strtolower($position) == 'g') {
            // GOALIE STATS BLOCK
            echo "<table class='goalie-stats-table default-zebra-table'>";
            echo "<colgroup>";
            echo "<col class='goalie-player-details-gp>";
            echo "<col class='goalie-player-details-w'>";
            echo "<col class='goalie-player-details-l'>";
            echo "<col class='goalie-player-details-gaa'>";
            echo "<col class='goalie-player-details-savepct'>";
            echo "<col class='goalie-player-details-so'>";
            echo "<col class='goalie-player-details-t'>";
            echo "<col class='goalie-player-details-gs'>";
            echo "<col class='goalie-player-details-ga'>";
            echo "<col class='goalie-player-details-otl'>";
            echo "<col class='goalie-player-details-sa'>";
            echo "</colgroup>";
            echo "<thead>";
            echo "<tr>";
                echo "<th>GP</th>";
                echo "<th>W</th>";
                echo "<th>L</th>";
                echo "<th>GAA</th>";
                echo "<th>Save %</th>";
                echo "<th>SO</th>";
                echo "<th>T</th>";
                echo "<th>GS</th>";
                echo "<th>GA</th>";
                echo "<th>OT L</th>";
                echo "<th>SA</th>";
            echo "</tr>";
            echo "</thead>";
        
            echo "<tbody>";
            echo "<tr>";
                echo "<td>$featuredSeasonGP</td>";
                echo "<td>$featuredSeasonWins</td>";
                echo "<td>$featuredSeasonLosses</td>";
                echo "<td>" . number_format($featuredSeasonGAA,2) . "</td>";
                echo "<td>" . number_format($featuredSeasonSavePct,3) . "</td>";
                echo "<td>$featuredSeasonSO</td>";
                echo "<td>$featuredSeasonTies</td>";
                echo "<td>$featuredSeasonGS</td>";
                echo "<td>$featuredSeasonGA</td>";
                echo "<td>$featuredSeasonOTLosses</td>";
                echo "<td>$featuredSeasonShotsAgainst</td>";
            echo "</tr>";
            echo "</tbody>";
            echo "</table><br><br>";
        

            } else {
              // SKATER STATS BLOCK
              echo "<table class='player-stats-table default-zebra-table'>";
                echo "<thead>";
                echo "<tr>";
                  echo "<th style='width: 7%'>GP</th>";
                  echo "<th style='width: 6%'>G</th>";
                  echo "<th style='width: 6%'>A</th>";
                  echo "<th style='width: 7%'>Pts</th>";
                  echo "<th style='width: 7%'>+/-</th>";
                  echo "<th style='width: 7%'>PIM</th>";
                  echo "<th style='width: 7%'>Shots</th>";
                  echo "<th style='width: 9%'>Shot %</th>";
                  echo "<th style='width: 7%'>PPG</th>";
                  echo "<th style='width: 7%'>PP Pts</th>";
                  echo "<th style='width: 7%'>SHG</th>";
                  echo "<th style='width: 9%'>SH Pts</th>";
                  echo "<th style='width: 7%'>GWG</th>";
                  echo "<th style='width: 7%'>OTG</th>"; 
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td>" . $featuredSeasonGP . "</td>";
                  echo "<td>" . $featuredSeasonGoals . "</td>";
                  echo "<td>" . $featuredSeasonAssists . "</td>";
                  echo "<td>" . $featuredSeasonPts . "</td>";
                  echo "<td>" . $featuredSeasonPlusMinus . "</td>";
                  echo "<td>" . $featuredSeasonPIM . "</td>";
                  echo "<td>" . $featuredSeasonShots . "</td>";
                  // echo "<td>" . $featuredSeasonShootingPct . "</td>";
                  $formatted_featuredSeasonShootingPct = $featuredSeasonShootingPct * 100;
                  echo "<td>" . number_format($formatted_featuredSeasonShootingPct, 2) . "</td>";
                  echo "<td>" . $featuredSeasonPPG . "</td>";
                  echo "<td>" . $featuredSeasonPPPoints . "</td>";
                  echo "<td>" . $featuredSeasonSHG . "</td>";
                  echo "<td>" . $featuredSeasonSHPts . "</td>";
                  echo "<td>" . $featuredSeasonGWG . "</td>";
                  echo "<td>" . $featuredSeasonOTGoals . "</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
             }

        ### Career Regular Season Stats ###
            echo "<h3 class='text-center text-2xl text-white'>Career Regular Season Statistics</h3>";
            if (strtolower($position) == 'g') {
              // GOALIE STATS BLOCK
              echo "<table class='goalie-stats-table default-zebra-table'>";
                echo "<thead>";
                echo "<tr>";
                  echo "<th>GP</td>";
                  echo "<th>W</td>";
                  echo "<th>L</td>";
                  echo "<th>GAA</td>";
                  echo "<th>Save %</td>";
                  echo "<th>SO</td>";
                  echo "<th>T</td>";
                  echo "<th>GS</td>";
                  echo "<th>GA</td>";
                  echo "<th>OT L</td>";
                  echo "<th>SA</td>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td>$regSeasonCareerGP</td>";
                  echo "<td>$regSeasonCareerWins</td>";
                  echo "<td>$regSeasonCareerLosses</td>";
                  echo "<td>" . number_format($regSeasonCareerGAA,2) . "</td>";
                  echo "<td>" . number_format($regSeasonCareerSavePct,3) . "</td>";
                  echo "<td>$regSeasonCareerSO</td>";
                  echo "<td>$regSeasonCareerTies</td>";
                  echo "<td>$regSeasonCareerGS</td>";
                  echo "<td>$regSeasonCareerGA</td>";
                  echo "<td>$regSeasonCareerOTLosses</td>";
                  echo "<td>$regSeasonCareerShotsAgainst</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
            } else {
              // SKATER STATS BLOCK
              echo "<table class='player-stats-table default-zebra-table'>";
                echo "<thead>";
                echo "<tr>";
                  echo "<th style='width: 7%'>GP</th>";
                  echo "<th style='width: 6%'>G</th>";
                  echo "<th style='width: 6%'>A</th>";
                  echo "<th style='width: 7%'>Pts</th>";
                  echo "<th style='width: 7%'>+/-</th>";
                  echo "<th style='width: 7%'>PIM</th>";
                  echo "<th style='width: 7%'>Shots</th>";
                  echo "<th style='width: 9%'>Shot %</th>";
                  echo "<th style='width: 7%'>PPG</th>";
                  echo "<th style='width: 7%'>PP Pts</th>";
                  echo "<th style='width: 7%'>SHG</th>";
                  echo "<th style='width: 9%'>SH Pts</th>";
                  echo "<th style='width: 7%'>GWG</th>";
                  echo "<th style='width: 7%'>OTG</th>"; 
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td>" . $regSeasonCareerGP . "</td>";
                  echo "<td>" . $regSeasonCareerGoals . "</td>";
                  echo "<td>" . $regSeasonCareerAssists . "</td>";
                  echo "<td>" . $regSeasonCareerPts . "</td>";
                  echo "<td>" . $regSeasonCareerPlusMinus . "</td>";
                  echo "<td>" . $regSeasonCareerPIM . "</td>";
                  echo "<td>" . $regSeasonCareerShots . "</td>";
                  $formatted_regSeasonCareerShootingPct = $regSeasonCareerShootingPct * 100;
                  echo "<td>" . number_format($formatted_regSeasonCareerShootingPct, 2) . "</td>";
                  echo "<td>" . $regSeasonCareerPPG . "</td>";
                  echo "<td>" . $regSeasonCareerPPPoints . "</td>";
                  echo "<td>" . $regSeasonCareerSHG . "</td>";
                  echo "<td>" . $regSeasonCareerSHPts . "</td>";
                  echo "<td>" . $regSeasonCareerGWG . "</td>";
                  echo "<td>" . $regSeasonCareerOTGoals . "</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
            }
        ### Career Playoff Stats ###
            echo "<h3 class='text-center text-2xl text-white'>Career Playoff Statistics</h3>";
            if (strtolower($position) == 'g') {
              // GOALIE STATS BLOCK
              echo "<table class='goalie-stats-table default-zebra-table'>";
              echo "<thead>";
                echo "<tr>";
                  echo "<th>GP</td>";
                  echo "<th>W</td>";
                  echo "<th>L</td>";
                  echo "<th>GAA</td>";
                  echo "<th>Save %</td>";
                  echo "<th>SO</td>";
                  echo "<th>T</td>";
                  echo "<th>GS</td>";
                  echo "<th>GA</td>";
                  echo "<th>OT L</td>";
                  echo "<th>SA</td>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td>$playoffsCareerGP</td>";
                  echo "<td>$playoffsCareerWins</td>";
                  echo "<td>$playoffsCareerLosses</td>";
                  echo "<td>" . number_format($playoffsCareerGAA,2) . "</td>";
                  echo "<td>" . number_format($playoffsCareerSavePct,3) . "</td>";
                  echo "<td>$playoffsCareerSO</td>";
                  echo "<td>$playoffsCareerTies</td>";
                  echo "<td>$playoffsCareerGS</td>";
                  echo "<td>$playoffsCareerGA</td>";
                  echo "<td>$playoffsCareerOTLosses</td>";
                  echo "<td>$playoffsCareerShotsAgainst</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
            } else {
              // SKATER STATS BLOCK
              echo "<table class='player-stats-table default-zebra-table'>";
                echo "<thead>";
                echo "<tr>";
                  echo "<th style='width: 7%'>GP</th>";
                  echo "<th style='width: 6%'>G</th>";
                  echo "<th style='width: 6%'>A</th>";
                  echo "<th style='width: 7%'>Pts</th>";
                  echo "<th style='width: 7%'>+/-</th>";
                  echo "<th style='width: 7%'>PIM</th>";
                  echo "<th style='width: 7%'>Shots</th>";
                  echo "<th style='width: 9%'>Shot %</th>";
                  echo "<th style='width: 7%'>PPG</th>";
                  echo "<th style='width: 7%'>PP Pts</th>";
                  echo "<th style='width: 7%'>SHG</th>";
                  echo "<th style='width: 9%'>SH Pts</th>";
                  echo "<th style='width: 7%'>GWG</th>";
                  echo "<th style='width: 7%'>OTG</th>"; 
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                  echo "<td>" . $playoffsCareerGP . "</td>";
                  echo "<td>" . $playoffsCareerGoals . "</td>";
                  echo "<td>" . $playoffsCareerAssists . "</td>";
                  echo "<td>" . $playoffsCareerPts . "</td>";
                  echo "<td>" . $playoffsCareerPlusMinus . "</td>";
                  echo "<td>" . $playoffsCareerPIM . "</td>";
                  echo "<td>" . $playoffsCareerShots . "</td>";
                  $formatted_playoffsCareerShootingPct = $playoffsCareerShootingPct * 100;
                  echo "<td>" . number_format($formatted_playoffsCareerShootingPct, 2) . "</td>";
                  echo "<td>" . $playoffsCareerPPG . "</td>";
                  echo "<td>" . $playoffsCareerPPPoints . "</td>";
                  echo "<td>" . $playoffsCareerSHG . "</td>";
                  echo "<td>" . $playoffsCareerSHPts . "</td>";
                  echo "<td>" . $playoffsCareerGWG . "</td>";
                  echo "<td>" . $playoffsCareerOTGoals . "</td>";
                echo "</tr>";
                echo "</tbody>";
              echo "</table><br><br>";
            }

            echo "</div>"; // END LEFT COLUMN

            // RIGHT COLUMN (bottom season-by-season table)
            echo "<div class='flex flex-col gap-8 lg:basis-[57%]'>";

        ### Extra stats to add later ###
            // echo "<p>" . $last5Games . "</p>";
            // echo "<p>" . $seasonTotals . "</p>";
            // echo "<p>" . $currentTeamRoster . "</p>";


        


        
        
      ### Season-by-Season Stats ###
          $seasonStatsSQL = "SELECT * FROM player_season_stats WHERE playerID=$player_id ORDER BY seasonSeason ASC";
          $seasonStats = mysqli_query($conn, $seasonStatsSQL);

          echo "<h3 class='text-center text-2xl'>Season-by-Season Statistics</h3>";
          echo "<table class='player-season-by-season-stats-table default-zebra-table' style='border: 1px solid #bcd6e7'>";
          echo "<thead>";
            echo "<tr>";
            if (strtolower($position) == 'g') {
              echo "<th>Season</th>";
              echo "<th>League</th>";
              echo "<th>Team Name</th>";
              echo "<th>Season Type</th>";
              echo "<th>GP</th>";
              echo "<th>W</th>";
              echo "<th>L</th>";
              echo "<th>GAA</th>";
              echo "<th>Sv %</th>";
            } else {
              echo "<th>Season</th>";
              echo "<th>League</th>";
              echo "<th>Team Name</th>";
              echo "<th>Season Type</th>";
              echo "<th>GP</th>";
              echo "<th>G</th>";
              echo "<th>A</th>";
              echo "<th>Pts</th>";
              echo "<th>PIM</th>";
            }
            echo "</tr>";
          echo "</thead>";
          echo "<tbody>";

          # initializing variables to store career totals
          $totalGP = $totalG = $totalA = $totalPts = $totalPIM = $totalW = $totalL = $totalGAA = $totalSavePct = $count = 0;

          while ($row = mysqli_fetch_assoc($seasonStats)) {
              $count+=1;
              echo "<tr>";
              $formatted_season_1 = substr($row['seasonSeason'], 0, 4);
              $formatted_season_2 = substr($row['seasonSeason'], 4);
              echo "<td>".htmlspecialchars($formatted_season_1)."-".htmlspecialchars($formatted_season_2)."</td>";
              echo "<td>" . htmlspecialchars($row['seasonLeagueAbbrev']) . "</td>";
              echo "<td>" . htmlspecialchars($row['seasonTeamName']) . "</td>";
              $gameType_num = $row['seasonGameTypeId'];
                  if ($gameType_num == 1) {
                      $gameType_text = "Pre.";
                  } elseif ($gameType_num == 2) {
                      $gameType_text = "Reg.";
                  } elseif ($gameType_num == 3) {
                      $gameType_text = "Post.";
                  } else {
                      $gameType_text = "Unknown";
                  }
                  echo "<td>".$gameType_text."</td>";

              echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonGamesPlayed'])) . "</td>";

              if (strtolower($position) == 'g') {
                echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonWins'])) . "</td>";
                echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonLosses'])) . "</td>";
                echo "<td>" . htmlspecialchars(number_format($row['seasonGAA'], 2)) . "</td>";
                echo "<td>" . htmlspecialchars(number_format($row['seasonSavePct'], 3)) . "</td>";
        
                // Tally up goalie stats
                $totalGP       += (int) $row['seasonGamesPlayed'];
                $totalW        += (int) $row['seasonWins'];
                $totalL        += (int) $row['seasonLosses'];
                $totalGAA      += (float) $row['seasonGAA'];
                $totalSavePct  += (float) $row['seasonSavePct'];
                $count++;
            } else {
                echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonGoals'])) . "</td>";
                echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonAssists'])) . "</td>";
                echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonPoints'])) . "</td>";
                echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonPIM'])) . "</td>";
        
                // Tally up skater stats
                $totalGP   += (int) $row['seasonGamesPlayed'];
                $totalG    += (int) $row['seasonGoals'];
                $totalA    += (int) $row['seasonAssists'];
                $totalPts  += (int) $row['seasonPoints'];
                $totalPIM  += (int) $row['seasonPIM'];
            }
        
            echo "</tr>";
          }
              
          if (strtolower($position) == 'g') {
            $avgGAA = $count > 0 ? $totalGAA / $count : 0;
            $avgSavePct = $count > 0 ? $totalSavePct / $count : 0;
        
            echo "<tr>";
              echo "<td colspan='4' rowspan='2' class='career-header'>Career Totals</td>";
              echo "<td class='career-subheader'>GP</td>";
              echo "<td class='career-subheader'>W</td>";
              echo "<td class='career-subheader'>L</td>";
              echo "<td class='career-subheader'>GAA</td>";
              echo "<td class='career-subheader'>Sv %</td>";
            echo "</tr>";
        
            echo "<tr>";
              echo "<td class='career-data'>$totalGP</td>";
              echo "<td class='career-data'>$totalW</td>";
              echo "<td class='career-data'>$totalL</td>";
              echo "<td class='career-data'>" . number_format($avgGAA, 2) . "</td>";
              echo "<td class='career-data'>" . number_format($avgSavePct, 3) . "</td>";
            echo "</tr>";
        } else {
            echo "<tr>";
              echo "<td colspan='4' rowspan='2' class='career-header'>Career Totals</td>";
              echo "<td class='career-subheader'>GP</td>";
              echo "<td class='career-subheader'>G</td>";
              echo "<td class='career-subheader'>A</td>";
              echo "<td class='career-subheader'>Pts</td>";
              echo "<td class='career-subheader'>PIM</td>";
            echo "</tr>";
        
            echo "<tr>";
              echo "<td class='career-data'>$totalGP</td>";
              echo "<td class='career-data'>$totalG</td>";
              echo "<td class='career-data'>$totalA</td>";
              echo "<td class='career-data'>$totalPts</td>";
              echo "<td class='career-data'>$totalPIM</td>";
            echo "</tr>";
        }

              
          echo "</tbody>";
          echo "</table><br><br>";

          echo "</div>"; // END RIGHT COLUMN
          echo "</div>"; // END OUTER WRAPPER


        ?>
        <br><br><br><br><br>

      </div>

    </div>

    
    <?php include 'footer.php'; ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="../js/vendor/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/vendor/holder.min.js"></script>
  </body>
</html>