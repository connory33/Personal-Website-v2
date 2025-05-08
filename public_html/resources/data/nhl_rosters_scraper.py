import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

# get df of all games - 1 row per game, primary key is game ID
games = "https://api.nhle.com/stats/rest/en/game"
games_summary = requests.get(games).json()
game_df = pd.DataFrame(games_summary['data'])

# get list of all game IDs
gameID_list = game_df['id']

####################################################### ROSTERS ########################################################

gameID = []; teamID = []; playerID = []; sweaterNumber = []
positionCode = [];

# build URL for individual game details + play-by-play
base_url = "https://api-web.nhle.com/v1/gamecenter/"
# game_code = '2024021247' # test game code
suffix= "/play-by-play"

count = 0
for game_id in gameID_list[-2000:-1000]:
    if count % 100 == 0:
        print(f"Processing game {count} of {len(gameID_list)}")
    count += 1
    game_url = base_url + str(game_id) + suffix

    try:
        response = requests.head(game_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            data = requests.get(game_url).json()
    
            if not data['rosterSpots']:
                gameID.append(np.nan)
                teamID.append(np.nan)
                playerID.append(np.nan)
                sweaterNumber.append(np.nan)
                positionCode.append(np.nan)

            else:
                for player in data['rosterSpots']:

                    gameID.append(game_id)

                    roster_keys = [
                        ('teamId', teamID),
                        ('playerId', playerID),
                        ('sweaterNumber', sweaterNumber),
                        ('positionCode', positionCode),
                    ]

                    for key, target_list in roster_keys:
                        target_list.append(player.get(key, None))

    except requests.RequestException as e:
        print("Error checking URL:", e)

df_roster = pd.DataFrame([gameID, teamID, playerID, sweaterNumber, positionCode]).transpose()
df_roster.columns = ['Game ID', 'Team ID', 'Player ID', 'Number', 'Position']
df_roster.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/nhl_rosters.csv", index=False)
print(df_roster.head())