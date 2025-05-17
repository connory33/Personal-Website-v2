<?php include('db_connection.php'); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHL Draft History</title>
    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        nhl: {
                            dark: '#131A24',
                            darkblue: '#1C2333',
                            medium: '#263044',
                            accent: '#00E6FF',
                            accent2: '#45CC8F',
                            text: '#FFFFFF',
                            muted: '#8A97B1'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'inner-highlight': 'inset 0 1px 0 0 rgba(255, 255, 255, 0.1)',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #131A24;
            color: #FFFFFF;
        }
        
        .bg-gradient-nhl {
            background: linear-gradient(180deg, #1C2333 0%, #131A24 100%);
        }
        
        .filter-input:focus {
            border-color: #00E6FF;
            box-shadow: 0 0 0 3px rgba(0, 230, 255, 0.3);
            outline: none;
        }
        
        .pagination-button {
            transition: all 0.2s ease;
        }
        
        .pagination-button:hover {
            transform: translateY(-2px);
        }
        
        .table-container {
            scrollbar-width: thin;
            scrollbar-color: #45CC8F #263044;
        }
        
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        .table-container::-webkit-scrollbar-track {
            background: #1C2333;
            border-radius: 10px;
        }
        
        .table-container::-webkit-scrollbar-thumb {
            background: #45CC8F;
            border-radius: 10px;
        }
        
        .draft-table th {
            position: sticky;
            top: 0;
            background-color: #1C2333;
            z-index: 10;
        }
        
        .draft-table tr:hover td {
            background-color: rgba(0, 230, 255, 0.05);
        }
        
        /* Animation for page loading */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.4s ease-in-out forwards;
        }
        
        /* Table borders */
        .border-cell {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .filters-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<?php include 'header.php'; ?>

<body class="bg-nhl-dark">

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if 'draft_id' is passed in URL
if (isset($_GET['draft_id'])) {
    $draft_id = $_GET['draft_id'];

    $sql = "SELECT draft_history.*, nhl_teams.id as team_id, nhl_teams.triCode as triCode, nhl_teams.teamLogo as logo, league_pages.* from 
            draft_history 
            LEFT JOIN nhl_teams ON draft_history.teamID = nhl_teams.id
            LEFT JOIN league_pages on draft_history.amateurLeague = league_pages.leagueName
            WHERE draftID = '$draft_id'
            ORDER BY round, pickInRound";

    $result = mysqli_query($conn, $sql);

    // Store all rows in a PHP array
    $all_picks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $all_picks[] = [
            'draftYear' => $row['draftYear'],
            'round' => $row['round'],
            'pickInRound' => $row['pickInRound'],
            'overallPick' => $row['overallPick'],
            'teamID' => $row['teamID'],
            'pickHistory' => $row['teamPickHistory'],
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
            'position' => $row['position'],
            'country' => $row['country'],
            'height' => $row['height'],
            'weight' => $row['weight'],
            'amateurLeague' => $row['amateurLeague'],
            'amateurClubName' => $row['amateurClubName'],
            'triCode' => $row['triCode'],
            'logo' => $row['logo'],
            'team_id' => $row['team_id'],
            'playerID' => $row['playerId'],
            'amateurLeagueName' => $row['leagueName'],
            'amateurLeagueURL' => $row['homepageURL'],
            'selectableRounds' => $row['selectableRounds']
        ];
    }

    // Pass data to JavaScript as JSON
    echo "<script>const allPicks = " . json_encode($all_picks) . ";</script>";

} else {
    echo "<div class='text-center p-8 text-red-400 font-medium'>No draft ID provided.</div>";
}
?>

<!-- Main content -->
<div class="bg-gradient-nhl py-8 px-4 animate-fade-in">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2 tracking-tight">
                NHL Draft <?php if (!empty($all_picks)) echo htmlspecialchars($all_picks[0]['draftYear']); ?>
            </h1>
            <p class="text-nhl-muted text-lg">
                Complete draft history and player selections
            </p>
        </div>
        
        <!-- Season Selector -->
        <div class="max-w-xs mx-auto mb-8">
            <label for="season-select" class="block text-sm font-medium text-nhl-muted mb-2">Select Draft Year</label>
            <div class="relative">
                <select id="season-select" class="block w-full rounded-lg border-0 py-3 pl-4 pr-10 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent text-lg font-medium cursor-pointer">
                  <?php
                  // Determine the current year from the data if available
                  $current_draft_year = !empty($all_picks) ? $all_picks[0]['draftYear'] : date("Y");
                  
                  // Generate options for years with available data
                  $current_year = date("Y");
                  for ($i = 0; $i < 46; $i++) {
                    $year = $current_year - $i;
                    // Only include years that we have in our mapping (1979-2024)
                    if ($year >= 1979 && $year <= $current_year) {
                      $selected = ($year == $current_draft_year) ? "selected" : "";
                      echo "<option value='$year' $selected>$year NHL Draft</option>";
                    }
                  }
                  ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-nhl-accent">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
            </div>
        </div>

        <!-- Search Filters -->
        <div class="mb-8 bg-nhl-darkblue rounded-xl p-6 shadow-lg shadow-inner-highlight border border-nhl-medium/50">
            <h2 class="text-xl font-semibold text-white mb-4">Filter Draft Picks</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 filters-container">
                <div>
                    <label for="searchByRound" class="block text-sm font-medium text-nhl-muted mb-1">Round</label>
                    <input type="text" id="searchByRound" class="filter-input w-full rounded-lg border-0 py-2 px-3 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent transition-all duration-200" placeholder="e.g. 1">
                </div>
                <div>
                    <label for="searchByTeam" class="block text-sm font-medium text-nhl-muted mb-1">Team</label>
                    <input type="text" id="searchByTeam" class="filter-input w-full rounded-lg border-0 py-2 px-3 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent transition-all duration-200" placeholder="e.g. NYR">
                </div>
                <div>
                    <label for="searchByPlayer" class="block text-sm font-medium text-nhl-muted mb-1">Player Name</label>
                    <input type="text" id="searchByPlayer" class="filter-input w-full rounded-lg border-0 py-2 px-3 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent transition-all duration-200" placeholder="e.g. Connor">
                </div>
                <div>
                    <label for="searchByPosition" class="block text-sm font-medium text-nhl-muted mb-1">Position</label>
                    <input type="text" id="searchByPosition" class="filter-input w-full rounded-lg border-0 py-2 px-3 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent transition-all duration-200" placeholder="e.g. C">
                </div>
                <div>
                    <label for="searchByCountry" class="block text-sm font-medium text-nhl-muted mb-1">Country</label>
                    <input type="text" id="searchByCountry" class="filter-input w-full rounded-lg border-0 py-2 px-3 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent transition-all duration-200" placeholder="e.g. CAN">
                </div>
                <div>
                    <label for="searchByLeague" class="block text-sm font-medium text-nhl-muted mb-1">Amateur League</label>
                    <input type="text" id="searchByLeague" class="filter-input w-full rounded-lg border-0 py-2 px-3 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent transition-all duration-200" placeholder="e.g. NCAA">
                </div>
                <div>
                    <label for="searchByClub" class="block text-sm font-medium text-nhl-muted mb-1">Amateur Club</label>
                    <input type="text" id="searchByClub" class="filter-input w-full rounded-lg border-0 py-2 px-3 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent transition-all duration-200" placeholder="e.g. Michigan">
                </div>
                <div class="flex items-end">
                    <button id="clearFilters" class="w-full rounded-lg bg-nhl-medium hover:bg-nhl-accent/20 py-2 px-4 text-white font-medium transition-all duration-200 border border-nhl-accent/30">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Results Information -->
        <div id="resultsInfo" class="text-white text-sm mb-3 flex justify-between items-center">
            <span id="totalResults">Showing all draft picks</span>
            <span id="pageInfo"></span>
        </div>

        <!-- Draft Table -->
        <div class="bg-nhl-darkblue rounded-xl shadow-lg p-1 mb-6 table-container overflow-x-auto border border-nhl-medium/50">
            <table id="draftTable" class="draft-table w-full text-left">
                <thead>
                    <tr class="text-nhl-accent text-sm uppercase border-b border-nhl-medium/50">
                        <th class="px-3 py-3 rounded-tl-lg">Rd</th>
                        <th class="px-3 py-3">#</th>
                        <th class="px-3 py-3">Team</th>
                        <th class="px-3 py-3">Player</th>
                        <th class="px-3 py-3">Pos</th>
                        <th class="px-3 py-3">Country</th>
                        <th class="px-3 py-3">Ht</th>
                        <th class="px-3 py-3">Wt</th>
                        <th class="px-3 py-3">Amateur League</th>
                        <th class="px-3 py-3 rounded-tr-lg">Amateur Team</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be dynamically generated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div id="pagination" class="flex flex-wrap justify-center gap-2 mt-6 pb-8">
            <!-- Pagination buttons will be dynamically generated -->
        </div>
    </div>
</div>

<script>
    // JavaScript to dynamically filter and paginate table rows
    document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.querySelector("#draftTable tbody");
        const searchByPlayer = document.getElementById("searchByPlayer");
        const searchByTeam = document.getElementById("searchByTeam");
        const searchByRound = document.getElementById("searchByRound");
        const searchByClub = document.getElementById("searchByClub");
        const searchByLeague = document.getElementById("searchByLeague");
        const searchByPosition = document.getElementById("searchByPosition");
        const searchByCountry = document.getElementById("searchByCountry");
        const pagination = document.getElementById("pagination");
        const clearFilters = document.getElementById("clearFilters");
        const resultsInfo = document.getElementById("totalResults");
        const pageInfo = document.getElementById("pageInfo");

        let currentPage = 1;
        const pageSize = 25; // Number of rows per page

        // Function to render rows dynamically
        function renderTable(data) {
            tableBody.innerHTML = ""; // Clear the table first
            
            if (data.length === 0) {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td colspan="10" class="px-6 py-8 text-center text-nhl-muted italic">No draft picks match your search criteria.</td>
                `;
                tableBody.appendChild(tr);
                return;
            }
            
            const start = (currentPage - 1) * pageSize;
            const end = Math.min(start + pageSize, data.length);
            const paginatedData = data.slice(start, end);

            paginatedData.forEach((row, index) => {
                const tr = document.createElement("tr");
                
                // Add zebra striping and hover effect
                tr.className = index % 2 === 0 ? "bg-nhl-darkblue" : "bg-nhl-medium/30";
                tr.classList.add("border-b", "border-nhl-medium/20");
                
                // First-round picks get special highlighting
                if (row.round === "1") {
                    tr.className += " border-l-4 border-nhl-accent2";
                }
                
                tr.innerHTML = `
                    <td class="px-3 py-2.5 font-semibold">${row.round}</td>
                    <td class="px-3 py-2.5 font-semibold">${row.overallPick}</td>
                    <td class="px-3 py-2.5">
                        <a href='team_details.php?team_id=${row.team_id}' class="block w-10 h-10 mx-auto transition-transform hover:scale-110">
                            <img src="${row.logo}" alt="${row.triCode}" class="w-full h-full object-contain">
                        </a>
                    </td>
                    <td class="px-3 py-2.5 font-medium">
                        <a href="player_details.php?player_id=${row.playerID}" class="hover:text-nhl-accent transition-colors">
                            ${row.firstName} ${row.lastName}
                        </a>
                    </td>
                    <td class="px-3 py-2.5">${row.position || '-'}</td>
                    <td class="px-3 py-2.5">${row.country || '-'}</td>
                    <td class="px-3 py-2.5">${row.height || '-'}</td>
                    <td class="px-3 py-2.5">${row.weight || '-'}</td>
                    <td class="px-3 py-2.5">
                        ${row.amateurLeagueURL ? 
                            `<a href='${row.amateurLeagueURL}' class="hover:text-nhl-accent transition-colors">${row.amateurLeague || '-'}</a>` : 
                            (row.amateurLeague || '-')}
                    </td>
                    <td class="px-3 py-2.5">${row.amateurClubName || '-'}</td>
                `;
                tableBody.appendChild(tr);
            });
            
            // Update results information
            resultsInfo.textContent = data.length === allPicks.length ? 
                `Showing all ${data.length} draft picks` : 
                `Showing ${data.length} of ${allPicks.length} draft picks`;
                
            pageInfo.textContent = `Page ${currentPage} of ${Math.ceil(data.length / pageSize)}`;
        }

        // Function to render pagination controls
        function renderPagination(data) {
            pagination.innerHTML = ""; // Clear existing pagination controls
            const totalPages = Math.ceil(data.length / pageSize);
            
            if (totalPages <= 1) {
                return; // Don't show pagination if there's only one page
            }

            // First page button
            const firstButton = document.createElement("button");
            firstButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M9.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            `;
            firstButton.className = `pagination-button flex items-center justify-center w-10 h-10 rounded ${currentPage === 1 ? 'bg-nhl-medium/30 text-nhl-muted cursor-not-allowed' : 'bg-nhl-medium text-white shadow hover:bg-nhl-accent/20'}`;
            firstButton.disabled = currentPage === 1;
            if (currentPage !== 1) {
                firstButton.addEventListener("click", () => {
                    currentPage = 1;
                    updateTableAndPagination(data);
                });
            }
            pagination.appendChild(firstButton);

            // Previous button
            const prevButton = document.createElement("button");
            prevButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            `;
            prevButton.className = `pagination-button flex items-center justify-center w-10 h-10 rounded ${currentPage === 1 ? 'bg-nhl-medium/30 text-nhl-muted cursor-not-allowed' : 'bg-nhl-medium text-white shadow hover:bg-nhl-accent/20'}`;
            prevButton.disabled = currentPage === 1;
            if (currentPage !== 1) {
                prevButton.addEventListener("click", () => {
                    currentPage--;
                    updateTableAndPagination(data);
                });
            }
            pagination.appendChild(prevButton);

            // Page numbers
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
            
            // Adjust if we're near the end
            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            // Add ellipsis if needed
            if (startPage > 1) {
                const ellipsis = document.createElement("span");
                ellipsis.textContent = "...";
                ellipsis.className = "flex items-center justify-center w-10 h-10 text-nhl-muted";
                pagination.appendChild(ellipsis);
            }

            // Page buttons
            for (let i = startPage; i <= endPage; i++) {
                const pageButton = document.createElement("button");
                pageButton.textContent = i;
                pageButton.className = `pagination-button flex items-center justify-center w-10 h-10 rounded ${i === currentPage ? 'bg-nhl-accent text-nhl-darkblue font-bold' : 'bg-nhl-medium text-white shadow hover:bg-nhl-accent/20'}`;
                pageButton.addEventListener("click", () => {
                    currentPage = i;
                    updateTableAndPagination(data);
                });
                pagination.appendChild(pageButton);
            }

            // Add ellipsis if needed
            if (endPage < totalPages) {
                const ellipsis = document.createElement("span");
                ellipsis.textContent = "...";
                ellipsis.className = "flex items-center justify-center w-10 h-10 text-nhl-muted";
                pagination.appendChild(ellipsis);
            }

            // Next button
            const nextButton = document.createElement("button");
            nextButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            `;
            nextButton.className = `pagination-button flex items-center justify-center w-10 h-10 rounded ${currentPage === totalPages ? 'bg-nhl-medium/30 text-nhl-muted cursor-not-allowed' : 'bg-nhl-medium text-white shadow hover:bg-nhl-accent/20'}`;
            nextButton.disabled = currentPage === totalPages;
            if (currentPage !== totalPages) {
                nextButton.addEventListener("click", () => {
                    currentPage++;
                    updateTableAndPagination(data);
                });
            }
            pagination.appendChild(nextButton);

            // Last page button
            const lastButton = document.createElement("button");
            lastButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 6.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-3.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            `;
            lastButton.className = `pagination-button flex items-center justify-center w-10 h-10 rounded ${currentPage === totalPages ? 'bg-nhl-medium/30 text-nhl-muted cursor-not-allowed' : 'bg-nhl-medium text-white shadow hover:bg-nhl-accent/20'}`;
            lastButton.disabled = currentPage === totalPages;
            if (currentPage !== totalPages) {
                lastButton.addEventListener("click", () => {
                    currentPage = totalPages;
                    updateTableAndPagination(data);
                });
            }
            pagination.appendChild(lastButton);
        }

        function filterTable() {
            const playerFilter = searchByPlayer.value.toLowerCase();
            const teamFilter = searchByTeam.value.toLowerCase();
            const leagueFilter = searchByLeague.value.toLowerCase();
            const roundFilter = searchByRound.value.toLowerCase();
            const clubFilter = searchByClub.value.toLowerCase();
            const positionFilter = searchByPosition.value.toLowerCase();
            const countryFilter = searchByCountry.value.toLowerCase();

            return allPicks.filter(row => {
                const fullName = `${row.firstName} ${row.lastName}`.toLowerCase();
                const matchesPlayer = fullName.includes(playerFilter);
                const matchesTeam = (row.triCode || '').toLowerCase().includes(teamFilter);
                const matchesLeague = (row.amateurLeague || '').toLowerCase().includes(leagueFilter);
                const matchesRound = (row.round || '').toString().toLowerCase().includes(roundFilter);
                const matchesClub = (row.amateurClubName || '').toLowerCase().includes(clubFilter);
                const matchesPosition = (row.position || '').toLowerCase().includes(positionFilter);
                const matchesCountry = (row.country || '').toLowerCase().includes(countryFilter);

                return matchesPlayer && matchesTeam && matchesLeague && matchesRound && matchesClub && matchesPosition && matchesCountry;
            });
        }

        // Function to update table and pagination
        function updateTableAndPagination(data) {
            renderTable(data);
            renderPagination(data);
            // Scroll to top of the table on page change
            window.scrollTo({
                top: document.querySelector(".table-container").offsetTop - 100,
                behavior: "smooth"
            });
        }

        // Filter inputs event listeners
        const filterInputs = [searchByPlayer, searchByTeam, searchByLeague, searchByRound, searchByClub, searchByPosition, searchByCountry];
        
        filterInputs.forEach(input => {
            input.addEventListener("input", () => {
                currentPage = 1; // Reset to first page on filter change
                const filteredData = filterTable();
                updateTableAndPagination(filteredData);
            });
        });
        
        // Clear filters button
        clearFilters.addEventListener("click", () => {
            filterInputs.forEach(input => {
                input.value = "";
            });
            currentPage = 1;
            updateTableAndPagination(allPicks);
        });

        // Initially render all rows and pagination
        updateTableAndPagination(allPicks);
    });

    // Season selection handler
    document.addEventListener('DOMContentLoaded', function() {
        // Define the mapping from season to draft_id
        const seasonToDraftId = {
            '2024': 63, '2023': 62, '2022': 60, '2021': 59, '2020': 58,
            '2019': 32, '2018': 9,  '2017': 44, '2016': 19, '2015': 57,
            '2014': 31, '2013': 4,  '2012': 41, '2011': 14, '2010': 47,
            '2009': 23, '2008': 3,  '2007': 38, '2006': 5,  '2005': 36,
            '2004': 13, '2003': 46, '2002': 22, '2001': 56, '2000': 33,
            '1999': 10, '1998': 42, '1997': 18, '1996': 53, '1995': 28,
            '1994': 6,  '1993': 37, '1992': 15, '1991': 50, '1990': 25,
            '1989': 1,  '1988': 34, '1987': 12, '1986': 43, '1985': 20,
            '1984': 54, '1983': 30, '1982': 7,  '1981': 39, '1980': 16,
            '1979': 52
        };

        const seasonSelect = document.getElementById('season-select');
        if (seasonSelect) {
            seasonSelect.addEventListener('change', function() {
                const draftId = seasonToDraftId[this.value];
                if (draftId) {
                    // Visual feedback for selection
                    seasonSelect.classList.add('ring-nhl-accent2');
                    setTimeout(() => {
                        window.location.href = `draft_history.php?draft_id=${draftId}`;
                    }, 200);
                }
            });
        }
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>