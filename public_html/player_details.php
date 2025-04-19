<!-- TEMPLATE FOR GAME DETAIL PAGES - GETS ID SELECTED ON GAMES PAGE AND USES IT TO QUERY DATABASE FOR ADDITIONAL GAME DETAILS -->

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

    <!-- Bootstrap core CSS -->
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../resources/css/album.css" rel="stylesheet">

    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />

  </head>
  <body>
    <div class="bg-dark text-white">
      <div style='margin-left: 3%; margin-right: 3%'>
        <?php

          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);
          include('db_connection.php');

          // Check if the 'game_id' is passed in the URL
          if (isset($_GET['player_id'])) {
              $player_id = $_GET['player_id'];

              $sql = "SELECT * FROM nhl_players WHERE playerID=$player_id";
              $playerInfo = mysqli_query($conn, $sql);

              while ($row = mysqli_fetch_assoc($playerInfo)) {
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
                  } else {
                    $teamName = 'N/A';
                    $teamLogo = 'N/A';
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
                  # Featured Season
                  $featuredSeason = $row['featuredSeason'];

                  # checks if a value (i.e. # of assists) is an empty string and changes to 0 if so
                  function fillEmptyStats($value) {
                    return $value === '' ? '0' : $value; # LEARN HOW THIS SYNTAX WORKS
                  }

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
                  
                  # Reg Season
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

                  # Playoffs
                  $playoffsCareerAssists = $row['playoffsCareerAssists'];
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

                  $last5Games = $row['last5Games'];
                  $seasonTotals = $row['seasonTotals'];
                  $awardNames = $row['awardNames'];
                  $awardSeasons = $row['awardSeasons'];
                  $currentTeamRoster = $row['currentTeamRoster'];
                  // 'currentTeamId'
                  
            }
          }


          echo "<br>";

          ### Flexbox for player name/number/active/id and headshot/team logo - aligns them side-by-side ###
          echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;'>";
            // Left side: Name and status
            echo "<div>";
              echo "<h1 style='margin: 0'>" . $name . " #" . $sweaterNumber . "</h1>";
              if ($active == 'Yes') {
                echo "<p style='color: green; font-weight: bold; margin: 0'>Active - " . $player_id . "</p>";
              } else {
                echo "<p style='color: red; font-weight: bold; margin: 0'>Not Active - " . $player_id . "</p>";
              }
            echo "</div>";
            // Right side: Headshot and logo
            echo "<div style='display: flex; align-items: center; gap: 5px;'>";
              echo "<img src='" . htmlspecialchars($headshot) . "' alt='headshot' style='height: 90px;'>";
              echo "<img src='" . htmlspecialchars($teamLogo) . "' alt='team logo' style='height: 90px;'>";
            echo "</div>";
          echo "</div>";

              // echo "<p>Current Team: " . $teamName . " <img src='" . htmlspecialchars($teamLogo) . "' alt='N/A' style='height: 65px;'></p>";
          if ($badgesLogos != 'false' and $badgesLogos != '') {
            echo "<img src='" . htmlspecialchars($badgesLogos) . "' alt='badge logo' style='height: 90px;'>";
          } else {
            echo "<p></p>";
          }

          ### Flexbox for hero image and player bio box ###
          echo "<div class='hero-bio-container'>";
            // Left side: Hero image container
            echo "<div>";
                echo "<img src='" . htmlspecialchars($heroImage) . "' alt='heroImage' class='hero-bio-image'>";
            echo "</div>";
            // Right side: Bio box container
            echo "<div class='bio-box'>";
              echo "<h4 style='text-align: center; background-color:#2e5b78'>Player Bio<br></h4>";
              echo "<p style='margin-left: 5%'><b>Height:</b>   " . $heightIn . " / " . $heightCm . " cm</p>";
              echo "<p style='margin-left: 5%'><b>Weight:</b>   " . $weightLb . " lbs / " . $weightKg . " kg</p>";
              echo "<p style='margin-left: 5%'><b>Birthdate:</b>   " . $birthDate . "</p>";
              echo "<p style='margin-left: 5%'><b>Birthplace:</b>   " . $birthCity . ", " . $birthStateProvince . " (" . $birthCountry . ")</p>";
              echo "<p style='margin-left: 5%'><b>Shoots/catches:</b>   " . $shootsCatches . "</p>";
              echo "<p style='margin-left: 5%'><b>Position:</b>   " . $position . "</p>";
              if ($draftYear == 'N/A') {
                echo "<p style='margin-left: 5%'><b>Draft Info: </b>Undrafted</p>";
              } else {
                echo "<p style='margin-left: 5%'><b>Draft Info: </b>" . $draftYear . " Rd. " . $draftRound . " Pick " . $draftPickInRound .
                " (" . $draftOverall . " overall) to " . $draftTeam . "</p>";
              }
              
              echo "<p style='margin-left: 5%'>" . $inHHOF . "</p>";
              # convert from strings to actual arrays to pair awards and years
              if (!empty($awardNames)) {
                $awardNamesArray = json_decode(str_replace("'", '"', $awardNames), true);
                $awardSeasonsArray = json_decode(str_replace("'", '"', $awardSeasons), true);
            
                if (is_array($awardNamesArray) && is_array($awardSeasonsArray)) {
                    for ($i = 0; $i < count($awardNamesArray); $i++) {
                        $award = $awardNamesArray[$i];
                        $seasonsRaw = $awardSeasonsArray[$i];
            
                        // Format 19331934 → 1933–1934
                        $formattedSeasons = array_map(function($s) {
                            return substr($s, 0, 4) . "–" . substr($s, 4);
                        }, $seasonsRaw);
            
                        $seasonString = implode(", ", $formattedSeasons);
                        echo "<p style='margin-left: 5%'><b>Awards:</b><br>" . $award . " (" . $seasonString . ")" . "</p>";
                    }
                } else {
                    echo "<p style='margin-left: 5%'><b>Awards:</b> None</p>";
                }
            } else {
                echo "<p style='margin-left: 5%'><b>Awards:</b> None</p>";
            }
              
            echo "</div>";
          echo "</div><br><br>";

          ### Featured Season Stats ###
            $formatted_featuredSeason_1 = substr($featuredSeason, 0, 4);
            $formatted_featuredSeason_2 = substr($featuredSeason, 4);
            echo "<h3 style='text-align: center'>Featured Season Statistics (" .
             $formatted_featuredSeason_1 . "-" . $formatted_featuredSeason_2 . ")</h3>";
            echo "<table class='player-stats-table'>";
              echo "<tr class='table-header'>";
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
              echo "<tr>";
                echo "<td>" . $featuredSeasonGP . "</td>";
                echo "<td>" . $featuredSeasonGoals . "</td>";
                echo "<td>" . $featuredSeasonAssists . "</td>";
                echo "<td>" . $featuredSeasonPts . "</td>";
                echo "<td>" . $featuredSeasonPlusMinus . "</td>";
                echo "<td>" . $featuredSeasonPIM . "</td>";
                echo "<td>" . $featuredSeasonShots . "</td>";
                $formatted_featuredSeasonShootingPct = (int)$featuredSeasonShootingPct * 100;
                echo "<td>" . number_format($formatted_featuredSeasonShootingPct, 2) . "</td>";
                echo "<td>" . $featuredSeasonPPG . "</td>";
                echo "<td>" . $featuredSeasonPPPoints . "</td>";
                echo "<td>" . $featuredSeasonSHG . "</td>";
                echo "<td>" . $featuredSeasonSHPts . "</td>";
                echo "<td>" . $featuredSeasonGWG . "</td>";
                echo "<td>" . $featuredSeasonOTGoals . "</td>";
              echo "</tr>";
            echo "</table><br><br>";

            ### Career Regular Season Stats ###
            echo "<h3 style='text-align: center'>Career Regular Season Statistics</h3>";
            echo "<table class='player-stats-table'>";
              echo "<tr class='table-header'>";
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
              echo "<tr>";
                echo "<td>" . $regSeasonCareerGP . "</td>";
                echo "<td>" . $regSeasonCareerGoals . "</td>";
                echo "<td>" . $regSeasonCareerAssists . "</td>";
                echo "<td>" . $regSeasonCareerPts . "</td>";
                echo "<td>" . $regSeasonCareerPlusMinus . "</td>";
                echo "<td>" . $regSeasonCareerPIM . "</td>";
                echo "<td>" . $regSeasonCareerShots . "</td>";
                $formatted_regSeasonCareerShootingPct = (int)$regSeasonCareerShootingPct * 100;
                echo "<td>" . number_format($formatted_regSeasonCareerShootingPct, 2) . "</td>";
                echo "<td>" . $regSeasonCareerPPG . "</td>";
                echo "<td>" . $regSeasonCareerPPPoints . "</td>";
                echo "<td>" . $regSeasonCareerSHG . "</td>";
                echo "<td>" . $regSeasonCareerSHPts . "</td>";
                echo "<td>" . $regSeasonCareerGWG . "</td>";
                echo "<td>" . $regSeasonCareerOTGoals . "</td>";
              echo "</tr>";
            echo "</table><br><br>";
            
            ### Career Playoff Stats ###
            echo "<h3 style='text-align: center'>Career Playoff Statistics</h3>";
            echo "<table class='player-stats-table'>";
              echo "<tr class='table-header'>";
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
              echo "<tr>";
                echo "<td>" . $playoffsCareerGP . "</td>";
                echo "<td>" . $playoffsCareerGoals . "</td>";
                echo "<td>" . $playoffsCareerAssists . "</td>";
                echo "<td>" . $playoffsCareerPts . "</td>";
                echo "<td>" . $playoffsCareerPlusMinus . "</td>";
                echo "<td>" . $playoffsCareerPIM . "</td>";
                echo "<td>" . $playoffsCareerShots . "</td>";
                $formatted_playoffsCareerShootingPct = (int)$playoffsCareerShootingPct * 100;
                echo "<td>" . number_format($formatted_playoffsCareerShootingPct, 2) . "</td>";
                echo "<td>" . $playoffsCareerPPG . "</td>";
                echo "<td>" . $playoffsCareerPPPoints . "</td>";
                echo "<td>" . $playoffsCareerSHG . "</td>";
                echo "<td>" . $playoffsCareerSHPts . "</td>";
                echo "<td>" . $playoffsCareerGWG . "</td>";
                echo "<td>" . $playoffsCareerOTGoals . "</td>";
              echo "</tr>";
          echo "</table><br><br>";

            // echo "<p>" . $last5Games . "</p>";
            // echo "<p>" . $seasonTotals . "</p>";
            // echo "<p>" . $currentTeamRoster . "</p>";


        


        ### SEASON-BY-SEASON STATS ###
        $seasonStatsSQL = "SELECT * FROM season_stats WHERE playerId=$player_id ORDER BY seasonSeason ASC";
        $seasonStats = mysqli_query($conn, $seasonStatsSQL);

        echo "<h3 style='text-align: center'>Season-by-Season Statistics</h3>";
        echo "<table style='width: 70%; border: 1px solid #bcd6e7; margin: 0px auto; text-align: center'>";
        echo "<tr style='color: white; font-weight: bold; background-color:#2e5b78'>";
        echo "<th>Season</th>";
        echo "<th>League</th>";
        echo "<th>Team Name</th>";
        echo "<th>Game Type</th>";
        echo "<th>GP</th>";
        echo "<th>G</th>";
        echo "<th>A</th>";
        echo "<th>Pts</th>";
        echo "<th>PIM</th>";
        // echo "<td>Sequence</td>";
        echo "</tr>";

        # initializing variables to store career totals
        $totalGP = $totalG = $totalA = $totalPts = $totalPIM = 0;

        while ($row = mysqli_fetch_assoc($seasonStats)) {
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


            // echo "<td>" . htmlspecialchars($row['seasonGameTypeId']) . "</td>";
            echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonGamesPlayed'])) . "</td>";
            echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonGoals'])) . "</td>";
            echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonAssists'])) . "</td>";
            echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonPoints'])) . "</td>";
            echo "<td>" . htmlspecialchars(fillEmptyStats($row['seasonPIM'])) . "</td>";
            // echo "<td>" . htmlspecialchars($row['seasonSequence']) . "</td>";
            echo "</tr>";

            // Sum totals
            $totalGP   += (int) $row['seasonGamesPlayed'];
            $totalG    += (int) $row['seasonGoals'];
            $totalA    += (int) $row['seasonAssists'];
            $totalPts  += (int) $row['seasonPoints'];
            $totalPIM  += (int) $row['seasonPIM'];
        }

            echo "<tr style='font-weight: bold; border: 1px solid white'>";
              echo "<td colspan='4' rowspan='2' style='vertical-align: middle; background-color: #18314f'>Career Totals</td>";
              echo "<td style='border-left: 1px solid white; background-color: #2e5b78'>GP</td>";
              echo "<td style='background-color: #2e5b78'>G</td>";
              echo "<td style='background-color: #2e5b78'>A</td>";
              echo "<td style='background-color: #2e5b78'>Pts</td>";
              echo "<td style='background-color: #2e5b78'>PIM</td>";
            echo "</tr>";
            echo "<tr style='font-weight: bold; border: 1px solid white'>";
              echo "<td style='border-left: 1px solid white'>" . $totalGP . "</td>";
              echo "<td>" . $totalG . "</td>";
              echo "<td>" . $totalA . "</td>";
              echo "<td>" . $totalPts . "</td>";
              echo "<td>" . $totalPIM . "</td>";
            echo "</tr>";

        echo "</table><br><br>";


        ?>
        <br><br><br><br><br>

      </div>

    </div>

    
    <footer class="bg-body-tertiary">
      <div class="container">
        <p class="float-right">
          <a href="#">Back to top</a>
        </p>
        <p>Copyright &copy; 2025 Connor Young</p>
      </div>
    </footer>

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