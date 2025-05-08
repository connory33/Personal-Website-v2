import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random


def debug_list_lengths():
    print(f"Length of teamID: {len(teamID)}")
    print(f"Length of playerID: {len(playerID)}")
    print(f"Length of headshotURLs: {len(headshotURLs)}")
    print(f"Length of firstName: {len(firstName)}")
    print(f"Length of lastName: {len(lastName)}")
    print(f"Length of positionCode: {len(positionCode)}")
    print(f"Length of seasonID: {len(seasonID)}")
    print(f"Length of seasonGamesPlayed: {len(seasonGamesPlayed)}")
    print(f"Length of seasonGoals: {len(seasonGoals)}")
    print(f"Length of seasonAssists: {len(seasonAssists)}")
    print(f"Length of seasonPoints: {len(seasonPoints)}")
    print(f"Length of seasonPlusMinus: {len(seasonPlusMinus)}")
    print(f"Length of seasonShots: {len(seasonShots)}")
    print(f"Length of seasonShootingPct: {len(seasonShootingPct)}")
    print(f"Length of seasonAvgTOI: {len(seasonAvgTOI)}")
    print(f"Length of seasonAvgShifts: {len(seasonAvgShifts)}")
    print(f"Length of seasonFOWinPct: {len(seasonFOWinPct)}")
    print(f"Length of seasonGS: {len(seasonGS)}")
    print(f"Length of seasonWins: {len(seasonWins)}")
    print(f"Length of seasonLosses: {len(seasonLosses)}")
    print(f"Length of seasonTies: {len(seasonTies)}")
    print(f"Length of seasonOTLosses: {len(seasonOTLosses)}")
    print(f"Length of seasonGAA: {len(seasonGAA)}")
    print(f"Length of seasonSavePct: {len(seasonSavePct)}")
    print(f"Length of seasonSA: {len(seasonSA)}")
    print(f"Length of seasonSaves: {len(seasonSaves)}")
    print(f"Length of seasonGA: {len(seasonGA)}")
    print(f"Length of seasonSO: {len(seasonSO)}")
    print(f"Length of seasonTOI: {len(seasonTOI)}")

url = "https://api.nhle.com/stats/rest/en/team"
response = requests.get(url)
team_data = response.json()

teamID = []; franchiseID = []; teamName = []; teamLeagueId = []; teamTriCode = []
team_data = team_data['data']
for team in team_data:
    teamID.append(team['id'])
    franchiseID.append(team['franchiseId'])
    teamName.append(team['fullName'])
    teamLeagueId.append(team['leagueId'])
    teamTriCode.append(team['triCode'])


# print(team_data['data'])

team_df = pd.DataFrame({'id': teamID, 'franchiseId': franchiseID, 'fullName': teamName, 'leagueId': teamLeagueId, 'triCode': teamTriCode})
# print(team_df)

# team_df.to_csv("C:/Users/conno/OneDrive/Documents/Personal Website/public_html/resources/data/team_data.csv", index=False)

teamID = []; playerID = []; headshotURLs = []; firstName = []; lastName = []; positionCode = []

### 1. CURRENT/LATEST STATS FOR EACH TEAM ###
latestGamesPlayed = []; latestGoals = []; latestAssists = []; latestPoints = []; latestPlusMinus = []
latestPIM = []; latestPPG = []; latestSHG = []; latestGWG = []; latestOTG = []; latestShots = []
latestShootingPct = []; latestAvgTOI = []; latestAvgShifts = []; latestFOWinPct = []; latestGS = []
latestWins = []; latestLosses = []; latestTies = []; latestOTLosses = []; latestGAA = []; latestSavePct = []
latestSA = []; latestSaves = []; latestGA = []; latestSO = []; latestTOI = []

### 2. SEASON STATS FOR EACH TEAM (EVERY SEASON) ###
seasonID = []
seasonGamesPlayed = []; seasonGoals = []; seasonAssists = []; seasonPoints = []; seasonPlusMinus = []
seasonPIM = []; seasonPPG = []; seasonSHG = []; seasonGWG = []; seasonOTG = []; seasonShots = []
seasonShootingPct = []; seasonAvgTOI = []; seasonAvgShifts = []; seasonFOWinPct = []; seasonGS = []
seasonWins = []; seasonLosses = []; seasonTies = []; seasonOTLosses = []; seasonGAA = []; seasonSavePct = []
seasonSA = []; seasonSaves = []; seasonGA = []; seasonSO = []; seasonTOI = []

### 3. HISTORICAL STATS (EACH SEASON) FOR EACH TEAM ###
histGamesPlayed = []; histGoals = []; histAssists = []; histPoints = []; histPlusMinus = []
histPIM = []; histPPG = []; histSHG = []; histGWG = []; histOTG = []; histShots = []
histShootingPct = []; histAvgTOI = []; histAvgShifts = []; histFOWinPct = []; histGS = []
histWins = []; histLosses = []; histTies = []; histOTLosses = []; histGAA = []; histSavePct = []
histSA = []; histSaves = []; histGA = []; histSO = []; histTOI = []



##### ITERATE THROUGH EACH TEAM AND GET CURRENT, SEASON, AND HISTORICAL STATS FOR EACH PLAYER #####

team_df_all_seasons = []

for team_id, teamTriCode in zip(team_df['id'], team_df['triCode']):

#     ### 1. CURRENT/LATEST STATS FOR EACH TEAM ###
#     latest_stats_url = f"https://api-web.nhle.com/v1/club-stats/{teamTriCode}/now"
#     print(f"Fetching stats for {teamTriCode} ({team_id})")

#     try:
#         response = requests.head(latest_stats_url, allow_redirects=True)
#         if response.status_code == 404:
#             print("Page not found (404).", teamTriCode)
#             latest_team_stats = {}
#         else:
#             latest_team_stats = requests.get(latest_stats_url).json()
#             latestSeason = latest_team_stats['season'] 
#             latest_stats_keys = [
#                     ('playerId', playerID),
#                     ('headshot', headshotURLs),
#                     ('gamesPlayed', latestGamesPlayed),
#                     ('goals', latestGoals),
#                     ('assists', latestAssists),
#                     ('points', latestPoints),
#                     ('plusMinus', latestPlusMinus),
#                     ('penaltyMinutes', latestPIM),
#                     ('gamesStarted', latestGS),
#                     ('wins', latestWins),
#                     ('losses', latestLosses),
#                     ('ties', latestTies),
#                     ('overtimeLosses', latestOTLosses),
#                     ('goalsAgainstAverage', latestGAA),
#                     ('savePercentage', latestSavePct),
#                     ('shotsAgainst', latestSA),
#                     ('saves', latestSaves),
#                     ('goalsAgainst', latestGA),
#                     ('shutouts', latestSO),
#                     ('timeOnIce', latestTOI),
#                     ('powerPlayGoals', latestPPG),
#                     ('shorthandedGoals', latestSHG),
#                     ('gameWinningGoals', latestGWG),
#                     ('overtimeGoals', latestOTG),
#                     ('shots', latestShots),
#                     ('shootingPctg', latestShootingPct),
#                     ('avgTimeOnIcePerGame', latestAvgTOI),
#                     ('avgShiftsPerGame', latestAvgShifts),
#                     ('faceoffWinPctg', latestFOWinPct)
#                 ]

#             # Skater Stats
#             for skater in latest_team_stats['skaters']:
#                 if 'firstName' in skater and 'lastName' in skater:
#                     teamID.append(team_id)
#                     firstName.append(skater['firstName']['default'])
#                     lastName.append(skater['lastName']['default'])
#                     positionCode.append(skater['positionCode'])
#                 for key, target_list in latest_stats_keys:
#                     if key in skater:
#                         target_list.append(skater[key])
#                     else:
#                         target_list.append(np.nan)

#             # Goalie Stats
#             for goalie in latest_team_stats['goalies']:
#                 if 'firstName' in goalie and 'lastName' in goalie:
#                     teamID.append(team_id)
#                     firstName.append(goalie['firstName']['default'])
#                     lastName.append(goalie['lastName']['default'])
#                 positionCode.append('G') # manually add positionCode since not in JSON
#                 for key, target_list in latest_stats_keys:
#                     if key in goalie:
#                         target_list.append(goalie[key])
#                     else:
#                         target_list.append(np.nan)
                
#     except requests.RequestException as e:
#         print("Error checking URL:", e)

#     team_latest_stats_df = pd.DataFrame([teamID, playerID, headshotURLs, firstName, lastName, positionCode, latestGamesPlayed,
#                                           latestGoals, latestAssists, latestPoints, latestPlusMinus, latestShots,
#                                           latestShootingPct, latestAvgTOI, latestAvgShifts, latestFOWinPct,
#                                           latestGS, latestWins, latestLosses, latestTies, latestOTLosses, latestGAA,
#                                           latestSavePct, latestSA, latestSaves, latestGA, latestSO, latestTOI]).transpose()
#     team_latest_stats_df.columns = ['teamID', 'playerID', 'headshotURLs', 'firstName', 'lastName', 'positionCode', 'latestGamesPlayed',
#                                           'latestGoals', 'latestAssists', 'latestPoints', 'latestPlusMinus', 'latestShots',
#                                           'latestShootingPct', 'latestAvgTOI', 'latestAvgShifts', 'latestFOWinPct',
#                                           'latestGS', 'latestWins', 'latestLosses', 'latestTies', 'latestOTLosses', 'latestGAA',
#                                           'latestSavePct', 'latestSA', 'latestSaves', 'latestGA', 'latestSO', 'latestTOI']
#     team_latest_stats_df.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/team_latest_stats.csv", index=False)

    ### 2. STATS FOR THE CURRENT SEASON FOR EACH TEAM ###
    season_stats_url = f"https://api-web.nhle.com/v1/club-stats-season/{teamTriCode}"
    try:
        response = requests.head(season_stats_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            season_team_stats = requests.get(season_stats_url).json()
            seasons = pd.DataFrame(season_team_stats)
            
            # gets lists of seasons and game types in that season (e.g. season=20232024, gameType=[2,3])
            if 'season' in seasons and 'gameTypes' in seasons:
                print("Seasons and game types found.")
                seasonYears = seasons['season'].to_list()
                seasonGameTypes = seasons['gameTypes'].to_list()

                # explodes list to get all combinations (e.g. 20232024, 2), (20232024, 3) etc.)
                seasons_gametype_list = []
                for i in range(len(seasonYears)):
                    seasonYear = seasonYears[i]
                    gameTypes = seasonGameTypes[i]
                    for gameType in gameTypes:
                        seasons_gametype_list.append((seasonYear, gameType))
                # print(seasons_gametype_list)

                season_stats_rows = []

                for seasonYearCombo in seasons_gametype_list:
                    season = seasonYearCombo[0]
                    gameType = seasonYearCombo[1]
                    season_id = str(season) + '-' + str(gameType)
                    seasonYearStatsURL = f"https://api-web.nhle.com/v1/club-stats/{teamTriCode}" + "/" + str(season) + "/" + str(gameType)
                    seasonYearStats = requests.get(seasonYearStatsURL).json()
                    # print(seasonYearStats)                    

                # Create fresh lists for the current season/team only
                    temp_seasonID = []
                    temp_teamID = []
                    temp_playerID = []
                    temp_headshotURLs = []
                    temp_firstName = []
                    temp_lastName = []
                    temp_positionCode = []
                    temp_seasonGamesPlayed = []
                    temp_seasonGoals = []
                    temp_seasonAssists = []
                    temp_seasonPoints = []
                    temp_seasonPlusMinus = []
                    temp_seasonPIM = []
                    temp_seasonShots = []
                    temp_seasonShootingPct = []
                    temp_seasonPPG = []
                    temp_seasonSHG = []
                    temp_seasonGWG = []
                    temp_seasonOTG = []
                    temp_seasonAvgTOI = []
                    temp_seasonAvgShifts = []
                    temp_seasonFOWinPct = []
                    temp_seasonGS = []
                    temp_seasonWins = []
                    temp_seasonLosses = []
                    temp_seasonTies = []
                    temp_seasonOTLosses = []
                    temp_seasonGAA = []
                    temp_seasonSavePct = []
                    temp_seasonSA = []
                    temp_seasonSaves = []
                    temp_seasonGA = []
                    temp_seasonSO = []
                    temp_seasonTOI = []

                    season_stats_keys = [
                        ('playerId', temp_playerID),
                        ('headshot', temp_headshotURLs),
                        ('gamesPlayed', temp_seasonGamesPlayed),
                        ('goals', temp_seasonGoals),
                        ('assists', temp_seasonAssists),
                        ('points', temp_seasonPoints),
                        ('plusMinus', temp_seasonPlusMinus),
                        ('penaltyMinutes', temp_seasonPIM),
                        ('gamesStarted', temp_seasonGS),
                        ('wins', temp_seasonWins),
                        ('losses', temp_seasonLosses),
                        ('ties', temp_seasonTies),
                        ('overtimeLosses', temp_seasonOTLosses),
                        ('goalsAgainstAverage', temp_seasonGAA),
                        ('savePercentage', temp_seasonSavePct),
                        ('shotsAgainst', temp_seasonSA),
                        ('saves', temp_seasonSaves),
                        ('goalsAgainst', temp_seasonGA),
                        ('shutouts', temp_seasonSO),
                        ('timeOnIce', temp_seasonTOI),
                        ('powerPlayGoals', temp_seasonPPG),
                        ('shorthandedGoals', temp_seasonSHG),
                        ('gameWinningGoals', temp_seasonGWG),
                        ('overtimeGoals', temp_seasonOTG),
                        ('shots', temp_seasonShots),
                        ('shootingPctg', temp_seasonShootingPct),
                        ('avgTimeOnIcePerGame', temp_seasonAvgTOI),
                        ('avgShiftsPerGame', temp_seasonAvgShifts),
                        ('faceoffWinPctg', temp_seasonFOWinPct)
                    ]

                    # Skaters
                    for skater in seasonYearStats['skaters']:
                        temp_seasonID.append(season_id)
                        temp_teamID.append(team_id)
                        temp_firstName.append(skater.get('firstName', {}).get('default', np.nan))
                        temp_lastName.append(skater.get('lastName', {}).get('default', np.nan))
                        temp_positionCode.append(skater.get('positionCode', np.nan))
                        for key, target_list in season_stats_keys:
                            target_list.append(skater.get(key, np.nan))

                    # Goalies
                    for goalie in seasonYearStats['goalies']:
                        temp_seasonID.append(season_id)
                        temp_teamID.append(team_id)
                        temp_firstName.append(goalie.get('firstName', {}).get('default', np.nan))
                        temp_lastName.append(goalie.get('lastName', {}).get('default', np.nan))
                        temp_positionCode.append('G')
                        for key, target_list in season_stats_keys:
                            target_list.append(goalie.get(key, np.nan))

                    # Build DataFrame for this season
                    team_season_stats_df = pd.DataFrame({
                        'seasonID': temp_seasonID,
                        'teamID': temp_teamID,
                        'playerID': temp_playerID,
                        'headshotURLs': temp_headshotURLs,
                        'firstName': temp_firstName,
                        'lastName': temp_lastName,
                        'positionCode': temp_positionCode,
                        'seasonGamesPlayed': temp_seasonGamesPlayed,
                        'seasonGoals': temp_seasonGoals,
                        'seasonAssists': temp_seasonAssists,
                        'seasonPoints': temp_seasonPoints,
                        'seasonPlusMinus': temp_seasonPlusMinus,
                        'seasonShots': temp_seasonShots,
                        'seasonShootingPct': temp_seasonShootingPct,
                        'seasonAvgTOI': temp_seasonAvgTOI,
                        'seasonAvgShifts': temp_seasonAvgShifts,
                        'seasonFOWinPct': temp_seasonFOWinPct,
                        'seasonGS': temp_seasonGS,
                        'seasonWins': temp_seasonWins,
                        'seasonLosses': temp_seasonLosses,
                        'seasonTies': temp_seasonTies,
                        'seasonOTLosses': temp_seasonOTLosses,
                        'seasonGAA': temp_seasonGAA,
                        'seasonSavePct': temp_seasonSavePct,
                        'seasonSA': temp_seasonSA,
                        'seasonSaves': temp_seasonSaves,
                        'seasonGA': temp_seasonGA,
                        'seasonSO': temp_seasonSO,
                        'seasonTOI': temp_seasonTOI
                    })

                    team_df_all_seasons.append(team_season_stats_df)


    except requests.RequestException as e:
        print("Error checking URL:", e)
    
team_df_all_seasons = pd.concat(team_df_all_seasons, ignore_index=True)
team_df_all_seasons.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/team_season_stats.csv", index=False)


    # ### HISTORICAL STATS (EACH SEASON) FOR EACH TEAM ###
    # seasons = ['19171918', '19181919', '19191920', '19201921', '19211922', '19221923', '19231924', '19241925',
    #            '19251926', '19261927', '19271928', '19281929', '19291930', '19301931', '19311932',
    #            '19321933', '19331934', '19341935', '19351936', '19361937', '19371938', '19381939',
    #            '19391940', '19401941', '19411942', '19421943', '19431944', '19441945', '19451946',
    #            '19461947', '19471948', '19481949', '19491950', '19501951', '19511952', '19521953',
    #            '19531954', '19541955', '19551956', '19561957', '19571958', '19581959', '19591960', 
    #            '19601961', '19611962', '19621963', '19631964', '19641965', '19651966', '19661967',
    #            '19671968', '19681969', '19691970', '19701971', '19711972', '19721973', '19731974',
    #            '19741975', '19751976', '19761977', '19771978', '19781979', '19791980', '19801981',
    #            '19811982', '19821983', '19831984', '19841985', '19851986', '19861987', '19871988',
    #            '19881989', '19891990', '19901991', '19911992', '19921993', '19931994', '19941995',
    #             '19951996', '19961997', '19971998', '19981999', '19992000', '20002001', '20012002',
    #             '20022003', '20032004', '20042005', '20052006', '20062007', '20072008', '20082009',
    #             '20092010', '20102011', '20112012', '20122013', '20132014', '20142015', '20152016',
    #             '20162017', '20172018', '20182019', '20192020', '20202021', '20212022', '20222023',
    #             '20232024', '20242025']
    # game_type = ['1', '2', '3']

    # for season in seasons:
    #     for game in game_type:
    #         historical_stats_url = "https://api-web.nhle.com/v1/club-stats/TOR/" + season + "/" + game
    #         try:
    #             response = requests.head(historical_stats_url, allow_redirects=True)
    #             if response.status_code == 404:
    #                 print("Page not found (404).")
    #             else:
    #                 historical_team_stats = requests.get(historical_stats_url).json()
    #         except requests.RequestException as e:
    #             print("Error checking URL:", e)



