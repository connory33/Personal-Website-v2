import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

################################################### PLAYER DETAILS ########################################################

# need to get distinct list of all player IDs from rosters - export w SQL from DB

### Define URL setup ###
player_base_url = "https://api-web.nhle.com/v1/player/" 
player_suffix = "/landing"

playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/playerID_list.csv")
playerID_list = playerID_list['playerId'][:-1].to_list() # convert from df to list for looping

game_id = []
homeRoad = []
opponent = []
team = []

count=0
for playerID in playerID_list[-9000:-7000]:
    count += 1
    if count % 100 == 0:
        print(f"Processed {count} players.")

    player_url = player_base_url + str(playerID) + player_suffix
    try:
        response = requests.head(player_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).", playerID)
            continue
        else:
            player_data = requests.get(player_url).json()
            last5games = player_data.get('last5Games', [])
            if last5games:
                for game in last5games:
                    game_id.append(game.get('gameId'))
                    homeRoad.append(game.get('homeRoadFlag'))
                    opponent.append(game.get('opponentAbbrev'))
                    team.append(game.get('teamAbbrev'))

                    
            else:
                print(f"No last 5 games data for player {playerID}.")
                game_id.append(np.nan)
                homeRoad.append(np.nan)
                opponent.append(np.nan)
                team.append(np.nan)

    
    except requests.RequestException as e:
        print(f"Error fetching JSON for player {playerID}: {e}")
        continue


# Combine data into a list of tuples
data = list(zip(playerID_list[-500:], game_id, homeRoad, opponent, team))

# Create DataFrame
last5games_df = pd.DataFrame(data, columns=['playerID', 'game_id', 'homeRoad', 'opponent', 'team'])

# Save DataFrame to CSV
last5games_df.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/player_last_5_games.csv", index=False)
