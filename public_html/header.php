<header class="text-white shadow bg-slate-800">
  <div class="mx-auto w-full px-4 py-3 flex justify-between items-center">

    <!-- Hamburger Icon -->
    <button id="mobile-menu-toggle" class="md:hidden focus:outline-none">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    
    <!-- Nav Menu -->
    <nav id="nav-menu" class="hidden md:flex flex-col md:flex-row md:items-center gap-4 mt-4 md:mt-0 text-sm font-medium w-full md:w-auto">

    <p><a href="https://connoryoung.com" class="hover:text-blue-400 hidden md:inline">Home</a></p>
    <p><a href="aboutMe.php" class="hover:text-blue-400 hidden md:inline">About Me</a></p>
    <!-- Golf DB Dropdown -->
    <div class="relative group">
        <a href="#" class="hover:text-blue-400 flex items-center space-x-1">
          <span>Golf DB</span>
          <svg class="w-3 h-3 text-white group-hover:text-blue-400 transition" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.084l3.71-3.854a.75.75 0 011.08 1.04l-4.25 4.417a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
          </svg>
        </a>
        <ul class="absolute hidden group-hover:block bg-slate-800 text-white rounded-md shadow-lg min-w-[180px] z-20 flex flex-col">
          <a href="https://connoryoung.com/golfers.php" class="block px-4 py-2 hover:bg-slate-700">Golfers</a>
          <a href="https://connoryoung.com/events.php" class="block px-4 py-2 hover:bg-slate-700">Event Schedules</a>
        </ul>
      </div>




      <!-- NHL DB dropdown -->
      <div class="relative group">
        <a href="#" class="hover:text-blue-400 flex items-center space-x-1">
          <span>NHL DB</span>
          <svg class="w-3 h-3 text-white group-hover:text-blue-400 transition" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.084l3.71-3.854a.75.75 0 011.08 1.04l-4.25 4.417a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
          </svg>
        </a>

        <!-- Nested team Dropdown -->
        <ul class="absolute hidden group-hover:block bg-slate-800 text-white rounded-md shadow-lg min-w-[180px] z-20 flex flex-col">
          <li><a href="nhlIndex.php" class="block px-4 py-2 hover:bg-slate-700">Game / Player Search (Home)</a></li>
          <li class="relative group/teams">
            <a href="#" class="block px-4 py-2 hover:bg-slate-700">Teams ▸</a>
            <ul class="absolute left-full top-0 hidden group-hover/teams:block bg-slate-800 text-white rounded-md shadow-lg min-w-[180px] z-30 max-h-96 overflow-y-auto flex flex-col">
              <!-- Paste all your team links here as-is -->
              <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=24">Anaheim Ducks</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=53">Arizona Coyotes</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=6">Boston Bruins</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=7">Buffalo Sabres</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=20">Calgary Flames</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=12">Carolina Hurricanes</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=16">Chicago Blackhawks</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=21">Colorado Avalanche</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=29">Columbus Blue Jackets</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=25">Dallas Stars</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=17">Detroit Red Wings</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=22">Edmonton Oilers</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=13">Florida Panthers</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=26">Los Angeles Kings</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=30">Minnesota Wild</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=8">Montreal Canadiens</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=18">Nashville Predators</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=1">New Jersey Devils</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=2">New York Islanders</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=3">New York Rangers</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=9">Ottawa Senators</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=4">Philadelphia Flyers</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=27">Phoenix Coyotes</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=5">Pittsburgh Penguins</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=28">San Jose Sharks</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=55">Seattle Kraken</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=19">St. Louis Blues</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=14">Tampa Bay Lightning</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=10">Toronto Maple Leafs</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=59">Utah Hockey Club</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=23">Vancouver Canucks</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=54">Vegas Golden Knights</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=15">Washington Capitals</a></li>
        <li><a class="block px-4 py-2 hover:bg-slate-700" href="https://connoryoung.com/team_details.php?team_id=52">Winnipeg Jets</a></li>
            </ul>
          </li>
          <li><a href="playoff_results.php?season_id=20232024" class="block px-4 py-2 hover:bg-slate-700">Playoff History</a></li>
          <li><a href="https://connoryoung.com/draft_history.php?draft_id=63" class="block px-4 py-2 hover:bg-slate-700">Draft History</a></li>
        </ul>

        </div>

        <!-- Past Projects dropdown -->
        <div class="relative group">
        <a href="#" class="hover:text-blue-400 flex items-center space-x-1">
          <span>Past Projects</span>
          <svg class="w-3 h-3 text-white group-hover:text-blue-400 transition" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.084l3.71-3.854a.75.75 0 011.08 1.04l-4.25 4.417a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
          </svg>
        </a>
        <ul class="absolute hidden group-hover:block bg-slate-800 text-white rounded-md shadow-lg min-w-[180px] z-20 flex flex-col">
          <a href="nhlLinesProject.php" class="block px-4 py-2 hover:bg-slate-700">NHL Lines</a>
          <a href="nbaFantasyProjections.php" class="block px-4 py-2 hover:bg-slate-700">NBA Fantasy</a>
          <a href="maddenOptimizer.php" class="block px-4 py-2 hover:bg-slate-700">NFL Roster</a>
          <a href="seniorDesign.php" class="block px-4 py-2 hover:bg-slate-700">Sr. Design</a>
          <a href="autonomousRobot.php" class="block px-4 py-2 hover:bg-slate-700">Robot</a>
          <a href="thermistorCleaner.php" class="block px-4 py-2 hover:bg-slate-700">Thermistor</a>
          <a href="waterPump.php" class="block px-4 py-2 hover:bg-slate-700">Water Pump</a>
          <a href="planterBoxes.php" class="block px-4 py-2 hover:bg-slate-700">Planter Boxes</a>
        </ul>
        </div>
      
    </nav>
  </div>

  <!-- Mobile Menu Script -->
  <script>
    const toggleBtn = document.getElementById('mobile-menu-toggle');
    const navMenu = document.getElementById('nav-menu');
    toggleBtn.addEventListener('click', () => {
      navMenu.classList.toggle('hidden');
    });
  </script>
</header>



