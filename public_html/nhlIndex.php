<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="NHL Historical Database - Search for games, players, teams, playoffs and draft information">
    <meta name="author" content="Connor Young">
    <link rel="icon" href="/resources/images/stick_icon.png">

    <title>NHL Database | Historical Game and Player Statistics</title>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="../resources/css/default_v3.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100">
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Hero Section with Tailwind Slideshow -->
<section class="relative min-h-[70vh] overflow-hidden">
    <!-- Slideshow container -->
    <div id="slideshow-container" class="absolute inset-0 z-0">
        <!-- Slides -->
        <div class="slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-100 z-10" 
             style="background-image: url('resources/images/nhl_index_bg.jpg')"></div>
        <div class="slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-0 z-0" 
             style="background-image: url('resources/images/thegoal.jpg')"></div>
        <div class="slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-0 z-0" 
             style="background-image: url('resources/images/bourque.jpg')"></div>
        <div class="slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-0 z-0" 
             style="background-image: url('resources/images/miracle.jpg')"></div>
             
        <!-- Dark overlay for better readability -->
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900/70 to-gray-900/90 z-20"></div>
    </div>

    <!-- Content -->
    <div class="container relative z-30 mx-auto px-4 flex items-center justify-center min-h-[70vh]">
        <div class="text-center max-w-4xl">
            <h1 class="text-5xl font-bold mb-6 text-white drop-shadow-lg">NHL Historical Database</h1>
            <p class="text-xl mb-8 text-white drop-shadow-md">Explore comprehensive NHL statistics, game details, player profiles, and historical records dating back decades.</p>
            
<script>
async function sendMessage() {
  const input = document.getElementById("user-input");
  const message = input.value.trim();
  if (!message) return;

  const chat = document.getElementById("chat-window");
  chat.innerHTML += `<div><strong>You:</strong> ${message}</div>`;

  input.value = "";
  chat.scrollTop = chat.scrollHeight;

  // Show loading indicator
  const loadingId = `loading-${Date.now()}`;
  chat.innerHTML += `<div id="${loadingId}"><strong>Bot:</strong> <em>Thinking...</em></div>`;

  try {
    const response = await fetch("http://localhost:8000/ask", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ question: message })
    });

    const data = await response.json();
    console.log("Response from API:", data);

    // Remove loading indicator
    document.getElementById(loadingId).remove();

    // Use the HTML version of the answer if available, otherwise fall back to plain text
    const answerHtml = data.answer_html || data.answer || data.error || 'Error: No response received.';
    
    // Add the bot response with HTML rendering enabled
    chat.innerHTML += `<div><strong>Bot:</strong> ${answerHtml}</div>`;
  } catch (error) {
    console.error("Error:", error);
    document.getElementById(loadingId).remove();
    chat.innerHTML += `<div><strong>Bot:</strong> Sorry, there was an error processing your request.</div>`;
  }
  
  chat.scrollTop = chat.scrollHeight;
}

// Add event listener for Enter key
document.getElementById("user-input").addEventListener("keypress", function(event) {
  if (event.key === "Enter") {
    event.preventDefault();
    sendMessage();
  }
});
console.log("Response from API:", data);

</script>



            <!-- Search Form -->
            <div class="bg-gray-900/80 p-6 mb-10 rounded-lg border border-gray-700 max-w-3xl mx-auto">
                <h2 class="text-xl font-semibold mb-4">Search the Database</h2>
                <form id="nhl-search" method="GET" action="nhl_games.php" class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="relative w-full md:w-1/3">
                        <select name="search_column" id="nhl-search-column" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                            <option value="season">Season</option>
                            <option value="gameDate">Game Date</option>
                            <option value="easternStartTime">Start Time</option>
                            <option value="gameType">Game Type</option>
                            <option value="team">Team</option>
                            <option value="homeTeamId">Home Team</option>
                            <option value="awayTeamId">Away Team</option>
                            <option value="player" selected>Player Name</option>
                        </select>
                        <!-- <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                            <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd"></path>
                            </svg>
                        </div> -->
                    </div>
                    <input type="text" name="search_term" id="search-term" placeholder="Enter player name, team, date..." required
                        class="w-full md:flex-1 bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md transition-colors duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                </form>
            </div>


            
            <div class="bg-gray-900/80 p-6 mb-10 rounded-lg border border-gray-700 max-w-3xl mx-auto">
  <h2 class="text-xl font-semibold mb-4 text-white">Ask NHL Stats Bot (in development)</h2>

  <div id="chat-window" class="bg-gray-800 text-white p-4 rounded-md h-36 overflow-y-auto border border-gray-700 mb-4 text-sm space-y-3"></div>

  <div class="flex flex-col md:flex-row gap-4 items-stretch">
    <input
      type="text"
      id="user-input"
      placeholder="Enter your question here..."
      class="flex-1 bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
    />
    <button
      onclick="sendMessage()"
      class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md transition-colors duration-200"
    >
      <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
      Ask
    </button>
  </div>
</div>

            
            <!-- Slideshow indicators -->
            <div class="flex justify-center gap-2">
                <button class="w-3 h-3 rounded-full bg-white opacity-100 transition-all duration-200 slide-indicator" data-slide="0"></button>
                <button class="w-3 h-3 rounded-full bg-white opacity-50 transition-all duration-200 slide-indicator" data-slide="1"></button>
                <button class="w-3 h-3 rounded-full bg-white opacity-50 transition-all duration-200 slide-indicator" data-slide="2"></button>
                <button class="w-3 h-3 rounded-full bg-white opacity-50 transition-all duration-200 slide-indicator" data-slide="3"></button>
            </div>
        </div>
    </div>
</section>
    
    <!-- Teams Section -->
    <section class="py-12 bg-gray-800">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-8">NHL Teams</h2>

            <?php include 'team_links_footer.php'; ?>
            
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-16 bg-gray-900">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Explore the Database</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Feature 1: Players -->
                <div class="feature-card rounded-lg p-6 text-center">
                    <div class="bg-blue-900 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Player Database</h3>
                    <p class="text-gray-300 mb-5">Access detailed stats, biographical information, and career history for thousands of NHL players.</p>
                    <a href="nhl_players.php" class="inline-block px-5 py-2 rounded-md text-blue-400 hover:text-white hover:bg-blue-600 border border-blue-500 transition-colors">
                        View All Players
                    </a>
                </div>
                
                <!-- Feature 2: Games -->
                <div class="feature-card rounded-lg p-6 text-center">
                    <div class="bg-blue-900 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Game Database</h3>
                    <p class="text-gray-300 mb-5">Browse through decades of NHL games with play-by-play data, rosters, and detailed statistics.</p>
                    <a href="nhl_games.php" class="inline-block px-5 py-2 rounded-md text-blue-400 hover:text-white hover:bg-blue-600 border border-blue-500 transition-colors">
                        View All Games
                    </a>
                </div>
                
                <!-- Feature 3: Playoffs -->
                <div class="feature-card rounded-lg p-6 text-center">
                    <div class="bg-blue-900 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Playoff History</h3>
                    <p class="text-gray-300 mb-5">Explore historical playoff brackets, series results, and championship outcomes by season.</p>
                    <a href="playoff_results.php?season_id=20232024" class="inline-block px-5 py-2 rounded-md text-blue-400 hover:text-white hover:bg-blue-600 border border-blue-500 transition-colors">
                        View Playoff History
                    </a>
                </div>
                
                <!-- Feature 4: Draft History -->
                <div class="feature-card rounded-lg p-6 text-center">
                    <div class="bg-blue-900 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Draft Database</h3>
                    <p class="text-gray-300 mb-5">Review historical NHL draft picks, prospect details, and team selection history.</p>
                    <a href="draft_history.php?draft_id=63" class="inline-block px-5 py-2 rounded-md text-blue-400 hover:text-white hover:bg-blue-600 border border-blue-500 transition-colors">
                        View Draft History
                    </a>
                </div>
                
                <!-- Feature 5: Season Overview -->
                <div class="feature-card rounded-lg p-6 text-center">
                    <div class="bg-blue-900 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Season Overviews</h3>
                    <p class="text-gray-300 mb-5">View comprehensive season statistics, standings, and league leaders for any NHL season.</p>
                    <a href="season_overview.php?season_id=20232024" class="inline-block px-5 py-2 rounded-md text-blue-400 hover:text-white hover:bg-blue-600 border border-blue-500 transition-colors">
                        View Seasons
                    </a>
                </div>
                
                <!-- Feature 6: About -->
                <!-- <div class="feature-card rounded-lg p-6 text-center">
                    <div class="bg-blue-900 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">About This Project</h3>
                    <p class="text-gray-300 mb-5">Learn more about this NHL database project, data sources, and planned future updates.</p>
                    <a href="about.php" class="inline-block px-5 py-2 rounded-md text-blue-400 hover:text-white hover:bg-blue-600 border border-blue-500 transition-colors">
                        About the Database
                    </a>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="pb-12 bg-gray-800">
        <div class="container mx-auto px-4 max-w-4xl text-center">
            <div class="section-divider mx-auto mb-8"></div>
            <p class="text-lg mb-3">This database is a work in progress.</p>
            <p class="mb-6">For any bugs or feature requests, please reach out at:</p>
            <a href="mailto:connor@connoryoung.com" class="text-blue-400 hover:text-blue-300 text-xl font-semibold">connor@connoryoung.com</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    
    <!-- Search Form Logic -->
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
    <!-- Simplified Slideshow Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all slides and indicators
    const slides = document.querySelectorAll('.slide');
    const indicators = document.querySelectorAll('.slide-indicator');
    let currentSlide = 0;
    
    // Function to show a specific slide
    function showSlide(index) {
        // Hide all slides
        slides.forEach(slide => {
            slide.classList.remove('opacity-100', 'z-10');
            slide.classList.add('opacity-0', 'z-0');
        });
        
        // Show the selected slide
        slides[index].classList.remove('opacity-0', 'z-0');
        slides[index].classList.add('opacity-100', 'z-10');
        
        // Update indicators
        indicators.forEach(dot => {
            dot.classList.remove('opacity-100');
            dot.classList.add('opacity-50');
        });
        indicators[index].classList.remove('opacity-50');
        indicators[index].classList.add('opacity-100');
        
        // Update current slide
        currentSlide = index;
    }
    
    // Add click handlers to indicators
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            resetTimer();
        });
    });
    
    // Auto-advance slides
    function nextSlide() {
        let next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }
    
    // Set up timer
    let slideTimer = setInterval(nextSlide, 5000);
    
    // Reset timer when manually changing slides
    function resetTimer() {
        clearInterval(slideTimer);
        slideTimer = setInterval(nextSlide, 5000);
    }
    
    console.log("Slideshow initialized with " + slides.length + " slides");
});
</script>
</body>
</html>