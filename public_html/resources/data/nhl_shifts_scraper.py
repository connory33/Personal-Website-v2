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

# build URL for individual game details + play-by-play
base_url = 'https://api.nhle.com/stats/rest/en/shiftcharts?cayenneExp=gameId='
# game_code = '2024021247' # test game code

gameID = []; shiftID = []; playerID = []; firstName = []; lastName = []; shiftNumber = []; period = []
startTime = []; endTime = []; duration = []; teamAbbrev = []; teamId = []; teamName = []; typeCode = []
detailCode = []; eventDescription = []; eventNumber = []; hexValue = []

count=0
for game_id in gameID_list[-500:]:
    # set game code to current game and pull JSON
    if count % 100 == 0:
        print(f"Processed {count} games.")
    count += 1
    game_code = str(game_id)
    url = base_url+game_code
    try:
        response = requests.head(url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            # print("Page found.")
            shift_data = requests.get(url).json()
            shift_data = shift_data['data']
            # print(shift_data['data'])
            for shift in shift_data:
                shifts_keys = [
                ('gameId', gameID),
                ('id', shiftID),
                ('playerId', playerID),
                ('firstName', firstName),
                ('lastName', lastName),
                ('shiftNumber', shiftNumber),
                ('period', period),
                ('startTime', startTime),
                ('endTime', endTime),
                ('duration', duration),
                ('teamAbbrev', teamAbbrev),
                ('teamId', teamId),
                ('teamName', teamName),
                ('typeCode', typeCode),
                ('detailCode', detailCode),
                ('eventDescription', eventDescription),
                ('eventNumber', eventNumber),
                ('hexValue', hexValue)
              ]

                for key, target_list in shifts_keys:
                    target_list.append(shift.get(key, None))
    except requests.RequestException as e:
        print("Error checking URL:", e)



df_shifts = pd.DataFrame([gameID, shiftID, playerID, shiftNumber, period, startTime, endTime,
                          duration, teamId, typeCode, detailCode, eventDescription, eventNumber, hexValue]).transpose()

df_shifts.columns = ['gameID', 'shiftID', 'playerID', 'shiftNumber', 'period', 'startTime', 'endTime',
                          'duration', 'teamId', 'typeCode', 'detailCode', 'eventDescription', 'eventNumber', 'hexValue']
df_shifts.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/nhl_shifts.csv", index=False)
print(df_shifts.head(10))


        