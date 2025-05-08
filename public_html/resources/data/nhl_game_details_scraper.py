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

############################### GAME SUMMARY INFORMATION (Primary Key GameID, location, teams, score, etc.) ####################################

id = []; season = []; gameType = []; gameNumber = []; gameDate = []; easternStartTime = []; gameStateId = [] 
venue = []; venueLocation = []; awayTeamId = []; awayTeamName = []; awayScore = []; awayShots = []; awayLogo = []
homeTeamId = []; homeTeamName = []; homeScore = []; homeShots = []; homeLogo = []; shootoutInUse = []; otInUse = []
gameOutcome = []; regPeriods = []

# build URL for individual game details + play-by-play
base_url = "https://api-web.nhle.com/v1/gamecenter/"
# game_code = '2024021247' # test game code
suffix= "/play-by-play"

for game_id in gameID_list[50000:]:
    # set game code to current game and pull JSON
    game_code = str(game_id)
    url = base_url+game_code+suffix
    try:
        response = requests.head(url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            pbp = requests.get(url).json()
            # append all fields from this game to their respective lists, adding None if not found
            id.append(game_id)
            season.append(pbp['season'])
            gameType.append(pbp['gameType']) 

            game_row = game_df[game_df['id'] == game_id]
            if not game_row.empty:
                gameNumber.append(game_row.iloc[0]['gameNumber'])
                easternStartTime.append(game_row.iloc[0]['easternStartTime'])
                gameStateId.append(game_row.iloc[0]['gameStateId'])
            else:
                gameNumber.append(None)
                easternStartTime.append(None)
                gameStateId.append(None)

            if 'gameDate' in pbp:
                gameDate.append(pbp['gameDate'])
            else:
                gameDate.append(None)

            if 'plays' in pbp:
                pbp.pop('plays')
            
            if 'rosterSpots' in pbp:
                pbp.pop('rosterSpots')

            if 'venue' in pbp:
                venue.append(pbp['venue']['default'])
            else:
                venue.append(None)

            if 'venueLocation' in pbp:
                venueLocation.append(pbp['venueLocation']['default'])
            else:
                venueLocation.append(None)

            if 'awayTeam' in pbp:
                awayTeamId.append(pbp['awayTeam']['id'])
                awayTeamName.append(pbp['awayTeam']['commonName']['default'])
                awayScore.append(pbp['awayTeam']['score'])
                if 'sog' in pbp['awayTeam']:
                    awayShots.append(pbp['awayTeam']['sog'])
                else:
                    awayShots.append(None)
                if 'logo' in pbp['awayTeam']:
                    awayLogo.append(pbp['awayTeam']['logo'])
                else:
                    awayLogo.append(None)
            else:
                awayTeamId.append(None)
                awayTeamName.append(None)
                awayScore.append(None)
                awayShots.append(None)
                awayLogo.append(None)

            if 'homeTeam' in pbp:
                homeTeamId.append(pbp['homeTeam']['id'])
                homeTeamName.append(pbp['homeTeam']['commonName']['default'])
                homeScore.append(pbp['homeTeam']['score'])
                if 'sog' in pbp['homeTeam']:
                    homeShots.append(pbp['homeTeam']['sog'])
                else:
                    homeShots.append(None)
                if 'logo' in pbp['homeTeam']:
                    homeLogo.append(pbp['homeTeam']['logo'])
                else:
                    homeLogo.append(None)
            else:
                homeTeamId.append(None)
                homeTeamName.append(None)
                homeScore.append(None)
                homeShots.append(None)
                homeLogo.append(None)

            if 'shootoutInUse' in pbp:
                shootoutInUse.append(pbp['shootoutInUse'])
            else:
                shootoutInUse.append(None)

            if 'otInUse' in pbp:
                otInUse.append(pbp['otInUse'])
            else:
                otInUse.append(None)

            if 'gameOutcome' in pbp:
                gameOutcome.append(pbp['gameOutcome']['lastPeriodType'])
            else:
                gameOutcome.append(None)

            if 'regPeriods' in pbp:
                regPeriods.append(pbp['regPeriods'])
            else:
                regPeriods.append(None)
    except requests.RequestException as e:
        print("Error checking URL:", e)


df_games = pd.DataFrame([id,season,gameType,gameNumber,gameDate,easternStartTime,gameStateId,venue,venueLocation,awayTeamId,awayTeamName,awayScore,
                         awayShots,awayLogo, homeTeamId,homeTeamName, homeScore, homeShots, homeLogo, shootoutInUse,otInUse,
                         gameOutcome,regPeriods]).transpose()

df_games.columns = ['id','season','gameType','gameNumber','gameDate','easternStartTime','gameStateId','venue','venueLocation','awayTeamId','awayTeamName','awayScore',
                         'awayShots','awayLogo', 'homeTeamId','homeTeamName', 'homeScore', 'homeShots', 'homeLogo', 'shootoutInUse','otInUse',
                         'gameOutcome','regPeriods']
df_games.to_csv("C:/Users/conno/OneDrive/Documents/Personal Website/public_html/resources/data/nhl_game_details.csv", index=False)