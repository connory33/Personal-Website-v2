import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

##################################### SEASON-BY-SEASON STATS FOR EACH PLAYER ################################

### Iterates through player IDs, accesses seasonTotals field for player, expands this field out into a full
### table of stats for each season for that player

# Define URL setup #
player_base_url = "https://api-web.nhle.com/v1/player/" 
player_suffix = "/landing"

playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/playerID_list.csv")
playerID_list = playerID_list[1:] # temp, remove 0 row before export
playerID_list = playerID_list['playerId'].to_list() # convert from df to list for looping

playerId = []
seasonAssists = []
seasonGameTypeId = []
seasonGamesPlayed = []
seasonGoals = []
seasonLeagueAbbrev = []
seasonPIM = []
seasonPoints = []
seasonSeason = []
seasonTeamName = []
seasonWins = []
seasonLosses = []
seasonGAA = []
seasonSavePct = []
seasonOTLosses = []
seasonShotsAgainst = []
seasonShutouts = []
seasonGoalsAgainst = []
seasonTimeOnIce = []
seasonTies = []

# Iterate through player IDs, get info for each player, append to lists for df creation later #
for playerID in playerID_list[:-2000]:
    player_url = player_base_url + str(playerID) + player_suffix
    try:
        response = requests.head(player_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            player_data = requests.get(player_url).json()
            season_data = player_data['seasonTotals'] # set variable to seasonTotals field list for that player

            # iterate through seasons for that player, store stats for each season
            for season in season_data:
                # print(season) # print each season for debugging
                player_season_keys = [
                    ('assists', seasonAssists),
                    ('gameTypeId', seasonGameTypeId),
                    ('gamesPlayed', seasonGamesPlayed),
                    ('goals', seasonGoals),
                    ('leagueAbbrev', seasonLeagueAbbrev),
                    ('pim', seasonPIM),
                    ('points', seasonPoints),
                    ('season', seasonSeason),
                    ('wins', seasonWins),
                    ('losses', seasonLosses),
                    ('goalsAgainstAvg', seasonGAA),
                    ('savePctg', seasonSavePct),
                    ('otLosses', seasonOTLosses),
                    ('shotsAgainst', seasonShotsAgainst),
                    ('shutouts', seasonShutouts),
                    ('goalsAgainst', seasonGoalsAgainst),
                    ('timeOnIce', seasonTimeOnIce),
                    ('ties', seasonTies)
                ]

                # iterate through tuples of key and list, get value for key, append to list
                for key, target_list in player_season_keys:
                    target_list.append(season.get(key, None))

                playerId.append(playerID)
                
                seasonTeamName.append(season['teamName']['default'])

    except requests.RequestException as e:
        print("Error checking URL:", e)

# Build df and use results
df_seasons_data = pd.DataFrame([playerId, seasonAssists, seasonGameTypeId, seasonGamesPlayed,
                                    seasonGoals, seasonLeagueAbbrev, seasonPIM, seasonPoints, seasonSeason,
                                    seasonTeamName, seasonWins, seasonLosses, seasonGAA, seasonSavePct, seasonOTLosses,
                                    seasonShotsAgainst, seasonShutouts, seasonGoalsAgainst, seasonTimeOnIce, seasonTies]).transpose()
df_seasons_data.columns = ['playerId', 'seasonAssists', 'seasonGameTypeId', 'seasonGamesPlayed',
                               'seasonGoals', 'seasonLeagueAbbrev', 'seasonPIM', 'seasonPoints', 'seasonSeason',
                               'seasonTeamName', 'seasonWins', 'seasonLosses', 'seasonGAA', 'seasonSavePct', 'seasonOTLosses',
                                    'seasonShotsAgainst', 'seasonShutouts', 'seasonGoalsAgainst', 'seasonTimeOnIce', 'seasonTies']
    
print(df_seasons_data.head())
df_seasons_data.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/player_season_stats.csv", index=False)