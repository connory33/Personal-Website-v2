<?php include('db_connection.php'); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draft History</title>
    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<?php include 'header.php'; ?>
<body>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if 'game_id' is passed in URL
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
    echo "<p>No game ID provided.</p>";
}
?>
<div style='background-color: #343a40'>
<br>
<h1 class="page-title text-center">
    Draft Picks <?php if (!empty($all_picks)) echo htmlspecialchars($all_picks[0]['draftYear']); ?>
</h1><br>

<div class="season-selector w-full max-w-xs mx-auto">
  <label for="season-select" class="block text-sm font-medium">Change Season</label>
  <div class="relative">
    <select id="season-select" class="rounded cursor-pointer transition-colors w-full appearance-none pr-8">
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
          echo "<option value='$year' $selected>$year</option>";
        }
      }
      ?>
    </select>
    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="white" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
      </svg>
    </div>
  </div>
</div>
<br>
    <!-- Search Filter Fields -->
    <div class="flex flex-wrap justify-center items-center gap-4 mb-4 max-w-[75%] mx-auto">
        <input type="text" id="searchByRound" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Round">
        <input type="text" id="searchByTeam" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Team (tricode, e.g., 'NYR')">
        <input type="text" id="searchByPlayer" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Player">
        <input type="text" id="searchByPosition" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Position">
        <input type="text" id="searchByCountry" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Country">
        <input type="text" id="searchByLeague" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Amateur League">
        <input type="text" id="searchByClub" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Amateur Team">
    </div>
<div class="overflow-x-auto w-[90%] mx-auto">
    <!-- Table -->
    <table class='shift-table default-zebra-table text-center' id="draftTable">
        <thead>
            <tr>
                <!-- <th>Draft Year</th> -->
                <th class='border border-slate-600 px-2 py-1'>Round</th>
                <!-- <th>Pick</th> -->
                <th class='border border-slate-600 px-2 py-1'>Overall</th>
                <th class='border border-slate-600 px-2 py-1'>Team</th>
                <!-- <th>Pick History</th> -->
                <th class='border border-slate-600 px-2 py-1'>Name</th>
                <th class='border border-slate-600 px-2 py-1'>Position</th>
                <th class='border border-slate-600 px-2 py-1'>Country</th>
                <th class='border border-slate-600 px-2 py-1'>Height (in.)</th>
                <th class='border border-slate-600 px-2 py-1'>Weight (lbs)</th>
                <th class='border border-slate-600 px-2 py-1'>Amateur League</th>
                <th class='border border-slate-600 px-2 py-1'>Amateur Club Name</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be dynamically generated by JavaScript -->
             
        </tbody>
    </table>
</div>

    <!-- Pagination Controls -->
    <div id="pagination" class="flex justify-center space-x-4 mt-6 text-white">
        <!-- Pagination buttons will be dynamically generated -->
    </div>
    <br>
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

        let currentPage = 1;
        const pageSize = 50; // Number of rows per page

        // Function to render rows dynamically
        function renderTable(data) {
            tableBody.innerHTML = ""; // Clear the table first
            const start = (currentPage - 1) * pageSize;
            const end = start + pageSize;
            const paginatedData = data.slice(start, end);

            paginatedData.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class='border border-slate-600 px-2 py-1'>${row.round}</td>
                    <td class='border border-slate-600 px-2 py-1'>${row.overallPick}</td>
                    <td class='border border-slate-600 px-2 py-1'><a href='team_details.php?team_id=${row.team_id}'><img src="${row.logo}" style='width: 45px' class='mx-auto'></a></td>
                    <td class='border border-slate-600 px-2 py-1'><a href="player_details.php?player_id=${row.playerID}">${row.firstName} ${row.lastName}</a></td>
                    <td class='border border-slate-600 px-2 py-1'>${row.position}</td>
                    <td class='border border-slate-600 px-2 py-1'>${row.country}</td>
                    <td class='border border-slate-600 px-2 py-1'>${row.height}</td>
                    <td class='border border-slate-600 px-2 py-1'>${row.weight}</td>
                    <td class='border border-slate-600 px-2 py-1'><a href='${row.amateurLeagueURL}'>${row.amateurLeague}</a></td>
                    <td class='border border-slate-600 px-2 py-1'>${row.amateurClubName}</td>
                `;
                tableBody.appendChild(tr);
            });
        }

        // Function to render pagination controls
        function renderPagination(data) {
            pagination.innerHTML = ""; // Clear existing pagination controls
            const totalPages = Math.ceil(data.length / pageSize);

            // Previous button
            if (currentPage > 1) {
                const prevButton = document.createElement("button");
                prevButton.textContent = "Previous";
                prevButton.className = "btn btn-secondary";
                prevButton.addEventListener("click", () => {
                    currentPage--;
                    updateTableAndPagination(data);
                });
                pagination.appendChild(prevButton);
            }

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement("button");
                pageButton.textContent = i;
                pageButton.className = `btn ${i === currentPage ? "btn-primary" : "btn-secondary"}`;
                pageButton.addEventListener("click", () => {
                    currentPage = i;
                    updateTableAndPagination(data);
                });
                pagination.appendChild(pageButton);
            }

            // Next button
            if (currentPage < totalPages) {
                const nextButton = document.createElement("button");
                nextButton.textContent = "Next";
                nextButton.className = "btn btn-secondary";
                nextButton.addEventListener("click", () => {
                    currentPage++;
                    updateTableAndPagination(data);
                });
                pagination.appendChild(nextButton);
            }
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
                const matchesTeam = row.triCode?.toLowerCase().includes(teamFilter);
                const matchesLeague = row.amateurLeague?.toLowerCase().includes(leagueFilter);
                const matchesRound = row.round?.toString().toLowerCase().includes(roundFilter);
                const matchesClub = row.amateurClubName?.toLowerCase().includes(clubFilter);
                const matchesPosition = row.position?.toLowerCase().includes(positionFilter);
                const matchesCountry = row.country?.toLowerCase().includes(countryFilter);

                return matchesPlayer && matchesTeam && matchesLeague && matchesRound && matchesClub && matchesPosition && matchesCountry;
            });
}


        // Function to update table and pagination
        function updateTableAndPagination(data) {
            renderTable(data);
            renderPagination(data);
        }

        // Attach event listeners for filtering
        searchByPlayer.addEventListener("keyup", () => {
            currentPage = 1; // Reset to first page on filter change
            const filteredData = filterTable();
            updateTableAndPagination(filteredData);
        });

        searchByTeam.addEventListener("keyup", () => {
            currentPage = 1; // Reset to first page on filter change
            const filteredData = filterTable();
            updateTableAndPagination(filteredData);
        });

        searchByLeague.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByClub.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByRound.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByPosition.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });

        searchByCountry.addEventListener("keyup", () => {
            currentPage = 1;
            const filteredData = filterTable(); 
            updateTableAndPagination(filteredData);
        });


        // Initially render all rows and pagination
        updateTableAndPagination(allPicks);
    });

    // <!-- JavaScript for season selection -->

      document.addEventListener('DOMContentLoaded', function() {
        // Define the mapping from season to draft_id
        const seasonToDraftId = {
            '2024': 63,
            '2023': 62,
            '2022': 60,
            '2021': 59,
            '2020': 58,
            '2019': 32,
            '2018': 9,
            '2017': 44,
            '2016': 19,
            '2015': 57,
            '2014': 31,
            '2013': 4,
            '2012': 41,
            '2011': 14,
            '2010': 47,
            '2009': 23,
            '2008': 3,
            '2007': 38,
            '2006': 5,
            '2005': 36,
            '2004': 13,
            '2003': 46,
            '2002': 22,
            '2001': 56,
            '2000': 33,
            '1999': 10,
            '1998': 42,
            '1997': 18,
            '1996': 53,
            '1995': 28,
            '1994': 6,
            '1993': 37,
            '1992': 15,
            '1991': 50,
            '1990': 25,
            '1989': 1,
            '1988': 34,
            '1987': 12,
            '1986': 43,
            '1985': 20,
            '1984': 54,
            '1983': 30,
            '1982': 7,
            '1981': 39,
            '1980': 16,
            '1979': 52
        };

        const seasonSelect = document.getElementById('season-select');
        if (seasonSelect) {
            seasonSelect.addEventListener('change', function() {
            const draftId = seasonToDraftId[this.value];
            if (draftId) {
                window.location.href = `draft_history.php?draft_id=${draftId}`;
            }
            });
        }
    });

</script>
</div>
<?php include 'footer.php'; ?>
</body>
</html>