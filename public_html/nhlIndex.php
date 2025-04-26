<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <!-- Bootstrap core CSS -->
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- <link href="../resources/css/album.css" rel="stylesheet"> -->

    <link rel="stylesheet" type="text/css" href="../resources/css/default_v3.css">

    <script src="https://cdn.tailwindcss.com"></script>
  </head><!--  -->
  <body>
<!-- Header -->
<?php include 'header.php'; ?>

  <div class="nhlindex-bg-img flex align-center justify-center py-5">
    <div id="nhlindex-content" class="text-center max-w-6xl mx-auto">
      <h2 class="text-2xl font-bold">NHL Historical Database</h2><br>
      <p>
        This site serves as a repository for historical data from the National Hockey League (NHL).<br><br>
        You can search past games by season, date, game type, start time, and teams. You can also search for players by name.
        <br><br>
        Game and player pages feature additional details such as rosters, play-by-play, player bio info, and player stats.<br>
        There are also team-specific pages that detail info and historical stats.
      </p>

        <div class="flex justify-center items-start">
          <form id="nhl-search" method="GET" action="nhl_games.php"
              class="px-4 sm:px-6 py-4 rounded-lg flex flex-col sm:flex-row gap-4 sm:items-center w-full max-w-4xl">
  
          <!-- Dropdown -->
          <select name="search_column"
              class="w-full sm:w-auto flex-1 bg-white text-black text-sm rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="season">Season</option>
              <option value="gameDate">Game Date</option>
              <option value="easternStartTime">Start Time</option>
              <option value="gameType">Game Type</option>
              <option value="team">Team</option>
              <option value="homeTeamId">Home Team</option>
              <option value="awayTeamId">Away Team</option>
              <option value="player">Player Name</option>
          </select>

          <!-- Text input -->
          <input type="text" name="search_term" id="search-term" placeholder="Enter search term" required
              class="w-full sm:flex-2 text-black px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">

          <!-- Submit button -->
          <input type="submit" value="Search"
              class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-md transition-colors duration-200 cursor-pointer">
          </form>
      </div>
        <br>
      <div style="text-align: center; color: white">
          <p>Select any team below to view details:<br>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=24'>ANA</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=53'>ARI</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=6'>BOS</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=7'>BUF</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=20'>CGY</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=12'>CAR</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=16'>CHI</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=21'>COL</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=29'>CBJ</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=25'>DAL</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=17'>DET</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=22'>EDM</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=13'>FLA</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=26'>LAK</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=30'>MIN</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=8'>MTL</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=1'>NJD</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=18'>NSH</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=2'>NYI</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=3'>NYR</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=9'>OTT</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=4'>PHI</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=5'>PIT</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=28'>SJS</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=55'>SEA</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=19'>STL</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=14'>TBL</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=10'>TOR</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=59'>UTA</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=23'>VAN</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=54'>VGK</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=52'>WPG</a><span> |</span>
        <a style='color: white' href='https://connoryoung.com/team_details.php?team_id=15'>WSH</a></p>

        </div>
        <br>
        <p>This database is a work in progress. For any bugs or feature requests, please reach out
          at connor@connoryoung.com.
        </p>
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

      <!-- JS for search form, allowing player to access nhl_players.php and others to nhl_games.php -->
      <script>
        document.getElementById('nhl-search').addEventListener('submit', function (e) {
            const column = document.getElementById('nhl-search-column').value;
            if (column === 'player') {
            this.action = 'nhl_players.php';
            } else {
            this.action = 'nhl_games.php';
            }
        });
    </script>

  </body>
</html>