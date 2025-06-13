import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

pd.set_option('display.max_columns', None)  # Show all columns

################################################### PLAYER DETAILS ########################################################

# need to get distinct list of all player IDs from rosters - export w SQL from DB
playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/playerID_list.csv")
playerID_list = playerID_list['playerId'].to_list()

# For testing with just 10 players
test_ids = playerID_list[-100:]

### Define URL setup ###
player_base_url = "https://api-web.nhle.com/v1/player/" 
player_suffix = "/landing"

# Create empty DataFrames to store our data
player_details_df = pd.DataFrame()
last5games_df = pd.DataFrame()
season_stats_df = pd.DataFrame()

# Create lists for player details columns
player_details_columns = [
    'playerId', 'isActive', 'currentTeamId', 'currentTeamAbbrev', 'fullTeamName', 'teamCommonName', 
    'teamPlaceNameWithPreposition', 'firstName', 'lastName', 'badgesLogos', 'badgesNames', 
    'teamLogo', 'sweaterNumber', 'position', 'headshot', 'heroImage', 'heightInInches', 
    'heightInCentimeters', 'weightInPounds', 'weightInKilograms', 'birthDate', 'birthCity', 
    'birthStateProvince', 'birthCountry', 'shootsCatches', 'draftYear', 'draftTeam', 'draftRound', 
    'draftPickInRound', 'draftOverall', 'playerSlug', 'inTop100AllTime', 'inHHOF', 
    'featuredSeason', 'featuredSeasonAssists', 'featuredSeasonGWG', 'featuredSeasonGP', 
    'featuredSeasonGoals', 'featuredSeasonOTGoals', 'featuredSeasonPIM', 'featuredSeasonPlusMinus', 
    'featuredSeasonPts', 'featuredSeasonPPG', 'featuredSeasonPPPoints', 'featuredSeasonShootingPct', 
    'featuredSeasonSHG', 'featuredSeasonSHPts', 'featuredSeasonShots', 'regSeasonCareerAssists', 
    'regSeasonCareerGWG', 'regSeasonCareerGP', 'regSeasonCareerGoals', 'regSeasonCareerOTGoals', 
    'regSeasonCareerPIM', 'regSeasonCareerPlusMinus', 'regSeasonCareerPts', 'regSeasonCareerPPG', 
    'regSeasonCareerPPPoints', 'regSeasonCareerShootingPct', 'regSeasonCareerSHG', 'regSeasonCareerSHPts', 
    'regSeasonCareerShots', 'playoffsCareerAssists', 'playoffsCareerGWG', 'playoffsCareerGP', 
    'playoffsCareerGoals', 'playoffsCareerOTGoals', 'playoffsCareerPIM', 'playoffsCareerPlusMinus', 
    'playoffsCareerPts', 'playoffsCareerPPG', 'playoffsCareerPPPoints', 'playoffsCareerShootingPct', 
    'playoffsCareerSHG', 'playoffsCareerSHPts', 'playoffsCareerShots', 'shopLink', 'twitterLink', 
    'watchLink', 'awardNames', 'awardSeasons', 'currentTeamRoster', 'featuredSeasonGAA', 
    'featuredSeasonLosses', 'featuredSeasonSO', 'featuredSeasonTies', 'featuredSeasonWins', 
    'featuredSeasonGS', 'featuredSeasonGA', 'featuredSeasonTOI', 'regSeasonCareerGAA', 
    'regSeasonCareerLosses', 'regSeasonCareerSO', 'regSeasonCareerTies', 'regSeasonCareerWins', 
    'regSeasonCareerGS', 'regSeasonCareerGA', 'regSeasonCareerTOI', 'playoffsCareerGAA', 
    'playoffsCareerLosses', 'playoffsCareerSO', 'playoffsCareerTies', 'playoffsCareerWins', 
    'playoffsCareerGS', 'playoffsCareerGA', 'playoffsCareerTOI', 'featuredSeasonSavePct', 
    'featuredSeasonOTLosses', 'featuredSeasonShotsAgainst', 'regSeasonCareerSavePct', 
    'regSeasonCareerOTLosses', 'regSeasonCareerShotsAgainst', 'playoffsCareerSavePct', 
    'playoffsCareerOTLosses', 'playoffsCareerShotsAgainst'
]

# Main processing loop
count = 0
for playerID in test_ids:  # Use playerID_list for full processing
    count += 1
    if count % 25 == 0:
        print(f"Processed {count} players.")
    
    player_url = player_base_url + str(playerID) + player_suffix
    try:
        response = requests.head(player_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).", playerID)
            continue
        else:
            player_data = requests.get(player_url).json()
            
            # ==== PLAYER DETAILS ====
            # Create a dictionary to hold all player details
            player_details = {col: None for col in player_details_columns}
            player_details['playerId'] = playerID
            
            # Basic info
            if 'currentTeamId' in player_data:
                player_details['currentTeamId'] = player_data['currentTeamId']
                player_details['currentTeamAbbrev'] = player_data['currentTeamAbbrev']
                player_details['fullTeamName'] = player_data['fullTeamName']['default']
                player_details['teamCommonName'] = player_data['teamCommonName']['default']
                player_details['teamPlaceNameWithPreposition'] = player_data['teamPlaceNameWithPreposition']['default']
            
            if 'firstName' in player_data and 'default' in player_data['firstName']:
                player_details['firstName'] = player_data['firstName']['default']
            if 'lastName' in player_data and 'default' in player_data['lastName']:
                player_details['lastName'] = player_data['lastName']['default']
            
            if 'badges' in player_data and player_data['badges']:
                player_details['badgesLogos'] = player_data['badges'][0]['logoUrl']['default']
                player_details['badgesNames'] = player_data['badges'][0]['title']['default']
            
            # Basic info fields
            basic_info_keys = [
                'isActive', 'teamLogo', 'sweaterNumber', 'position', 'headshot', 'heroImage', 
                'heightInInches', 'heightInCentimeters', 'weightInPounds', 'weightInKilograms',
                'birthDate', 'birthCountry', 'shootsCatches', 'playerSlug', 'inTop100AllTime', 'inHHOF'
            ]
            for key in basic_info_keys:
                if key in player_data:
                    player_details[key] = player_data.get(key)
            
            if 'birthCity' in player_data:
                player_details['birthCity'] = player_data['birthCity']['default']
            if 'birthStateProvince' in player_data:
                player_details['birthStateProvince'] = player_data['birthStateProvince']['default']
            
            # Draft info
            if 'draftDetails' in player_data:
                player_details['draftYear'] = player_data['draftDetails']['year']
                player_details['draftTeam'] = player_data['draftDetails']['teamAbbrev']
                player_details['draftRound'] = player_data['draftDetails']['round']
                player_details['draftPickInRound'] = player_data['draftDetails']['pickInRound']
                player_details['draftOverall'] = player_data['draftDetails']['overallPick']
            
            # Featured Season
            if 'featuredStats' in player_data and 'regularSeason' in player_data['featuredStats']:
                player_details['featuredSeason'] = player_data['featuredStats']['season']
                featuredSeasonStats = player_data['featuredStats']['regularSeason']['subSeason']
                
                featured_season_keys = [
                    'assists', 'gameWinningGoals', 'gamesPlayed', 'goals', 'otGoals', 'pim', 
                    'plusMinus', 'points', 'powerPlayGoals', 'powerPlayPoints', 'shootingPctg', 
                    'shorthandedGoals', 'shorthandedPoints', 'shots', 'timeOnIce', 'goalsAgainstAvg', 
                    'losses', 'shutouts', 'ties', 'wins', 'gamesStarted', 'goalsAgainst', 'savePctg',
                    'OTlosses', 'shotsAgainst'
                ]
                
                for key in featured_season_keys:
                    if key in featuredSeasonStats:
                        map_key = f"featuredSeason{key[0].upper()}{key[1:]}"
                        if key == 'OTlosses':  # Handle special case
                            map_key = 'featuredSeasonOTLosses'
                        player_details[map_key] = featuredSeasonStats.get(key)
            
            # Career Totals - Regular Season
            if 'careerTotals' in player_data and 'regularSeason' in player_data['careerTotals']:
                regSeasonCareer = player_data['careerTotals']['regularSeason']
                
                reg_season_career_keys = [
                    'assists', 'gameWinningGoals', 'gamesPlayed', 'goals', 'otGoals', 'pim', 
                    'plusMinus', 'points', 'powerPlayGoals', 'powerPlayPoints', 'shootingPctg', 
                    'shorthandedGoals', 'shorthandedPoints', 'shots', 'timeOnIce', 'goalsAgainstAvg', 
                    'losses', 'shutouts', 'ties', 'wins', 'gamesStarted', 'goalsAgainst', 'savePctg',
                    'OTlosses', 'shotsAgainst'
                ]
                
                for key in reg_season_career_keys:
                    if key in regSeasonCareer:
                        map_key = f"regSeasonCareer{key[0].upper()}{key[1:]}"
                        if key == 'OTlosses':  # Handle special case
                            map_key = 'regSeasonCareerOTLosses'
                        player_details[map_key] = regSeasonCareer.get(key)
            
            # Career Totals - Playoffs
            if 'careerTotals' in player_data and 'playoffs' in player_data['careerTotals']:
                playoffsCareer = player_data['careerTotals']['playoffs']
                
                playoffs_career_keys = [
                    'assists', 'gameWinningGoals', 'gamesPlayed', 'goals', 'otGoals', 'pim', 
                    'plusMinus', 'points', 'powerPlayGoals', 'powerPlayPoints', 'shootingPctg', 
                    'shorthandedGoals', 'shorthandedPoints', 'shots', 'timeOnIce', 'goalsAgainstAvg', 
                    'losses', 'shutouts', 'ties', 'wins', 'gamesStarted', 'goalsAgainst', 'savePctg',
                    'OTlosses', 'shotsAgainst'
                ]
                
                for key in playoffs_career_keys:
                    if key in playoffsCareer:
                        map_key = f"playoffsCareer{key[0].upper()}{key[1:]}"
                        if key == 'OTlosses':  # Handle special case
                            map_key = 'playoffsCareerOTLosses'
                        player_details[map_key] = playoffsCareer.get(key)
            
            # Other fields
            player_details['shopLink'] = player_data.get('shopLink')
            player_details['twitterLink'] = player_data.get('twitterLink')
            player_details['watchLink'] = player_data.get('watchLink')
            
            # Awards
            if 'awards' in player_data:
                awards = player_data['awards']
                player_awardNames = []
                player_awardSeasons = []
                
                for award in awards:
                    player_awardNames.append(award['trophy']['default'])
                    seasonsWon = []
                    for season in award['seasons']:
                        seasonsWon.append(season['seasonId'])
                    player_awardSeasons.append(seasonsWon)
                
                player_details['awardNames'] = json.dumps(player_awardNames)
                player_details['awardSeasons'] = json.dumps(player_awardSeasons)
            
            if 'currentTeamRoster' in player_data:
                player_details['currentTeamRoster'] = json.dumps(player_data['currentTeamRoster'])
            
            # Add player details to the dataframe
            player_details_df = pd.concat([player_details_df, pd.DataFrame([player_details])], ignore_index=True)
            
            # ==== LAST 5 GAMES ====
            last5games = player_data.get('last5Games', [])
            if last5games:
                for game in last5games:
                    game_row = {
                        'playerID': playerID,
                        'game_id': game.get('gameId'),
                        'homeRoad': game.get('homeRoadFlag'),
                        'opponent': game.get('opponentAbbrev'),
                        'team': game.get('teamAbbrev')
                    }
                    last5games_df = pd.concat([last5games_df, pd.DataFrame([game_row])], ignore_index=True)
            else:
                print(f"No last 5 games data for player {playerID}.")
            
            # ==== SEASON BY SEASON STATS ====
            season_data = player_data.get('seasonTotals', [])
            for season in season_data:
                season_row = {
                    'playerID': playerID,
                    'seasonAssists': season.get('assists'),
                    'seasonGameTypeId': season.get('gameTypeId'),
                    'seasonGamesPlayed': season.get('gamesPlayed'),
                    'seasonGoals': season.get('goals'),
                    'seasonLeagueAbbrev': season.get('leagueAbbrev'),
                    'seasonPIM': season.get('pim'),
                    'seasonPoints': season.get('points'),
                    'seasonSeason': season.get('season'),
                    'seasonTeamName': season['teamName']['default'] if 'teamName' in season else None,
                    'seasonWins': season.get('wins'),
                    'seasonLosses': season.get('losses'),
                    'seasonGAA': season.get('goalsAgainstAvg'),
                    'seasonSavePct': season.get('savePctg'),
                    'seasonOTLosses': season.get('otLosses'),
                    'seasonShotsAgainst': season.get('shotsAgainst'),
                    'seasonShutouts': season.get('shutouts'),
                    'seasonGoalsAgainst': season.get('goalsAgainst'),
                    'seasonTimeOnIce': season.get('timeOnIce'),
                    'seasonTies': season.get('ties')
                }
                season_stats_df = pd.concat([season_stats_df, pd.DataFrame([season_row])], ignore_index=True)

    except requests.RequestException as e:
        print(f"Error fetching JSON for player {playerID}: {e}")
        continue
    
    # Add a small delay to avoid hitting API limits
    time.sleep(random.uniform(0.1, 0.3))

# Save all dataframes to CSV
player_details_df.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/nhl_player_details.csv", index=False)
last5games_df.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/player_last_5_games.csv", index=False)
season_stats_df.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/player_season_stats.csv", index=False)

print("Data extraction complete!")
print(f"Player details: {len(player_details_df)} records")
print(f"Last 5 games: {len(last5games_df)} records")
print(f"Season stats: {len(season_stats_df)} records")