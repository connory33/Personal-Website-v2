import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

# url= "https://api-web.nhle.com/v1/gamecenter/2022030324/play-by-play"
# games = 'https://api.nhle.com/stats/rest/en/shiftcharts?cayenneExp=gameId=2022030324'
# games_summary = requests.get(games).json()
# print(games_summary)


# player_base_url = "https://api-web.nhle.com/v1/player/" 
# player_suffix = "/landing"
# player_id = 8478406
# url = player_base_url + str(player_id) + player_suffix
# response = requests.get(url)
# data = response.json()
# print(data)

import requests
import pandas as pd
from collections import defaultdict

playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/nhl_player_details.csv")
playerID_list = playerID_list['playerId'].to_list() # convert from df to list for looping
base_url = 'https://api-web.nhle.com/v1/player/'
suffix = '/landing'

data = defaultdict(list)

for count, playerID in enumerate(playerID_list[-1000:], 1):
    if count % 25 == 0:
        print(f"Processed {count} players.")

    url = base_url + str(playerID) + suffix

    try:
        # Skip if player page doesn't exist
        if requests.head(url, allow_redirects=True).status_code == 404:
            print(f"404 Not Found: {playerID}")
            continue

        player_data = requests.get(url).json()
        row = {}

        # Top-level fields
        row['playerId'] = player_data.get('playerId')
        row['firstName'] = player_data.get('firstName', {}).get('default')
        row['lastName'] = player_data.get('lastName', {}).get('default')
        row['position'] = player_data.get('position')
        row['birthDate'] = player_data.get('birthDate')
        row['birthCountry'] = player_data.get('birthCountry')
        row['birthCity'] = player_data.get('birthCity', {}).get('default')
        row['birthStateProvince'] = player_data.get('birthStateProvince', {}).get('default')
        row['heightInInches'] = player_data.get('heightInInches')
        row['weightInPounds'] = player_data.get('weightInPounds')
        row['shootsCatches'] = player_data.get('shootsCatches')
        row['isActive'] = player_data.get('isActive')

        # Team info
        row['currentTeamId'] = player_data.get('currentTeamId')
        row['currentTeamAbbrev'] = player_data.get('currentTeamAbbrev')
        row['fullTeamName'] = player_data.get('fullTeamName', {}).get('default')
        row['teamCommonName'] = player_data.get('teamCommonName', {}).get('default')
        row['teamPlaceNameWithPreposition'] = player_data.get('teamPlaceNameWithPreposition', {}).get('default')

        # Draft details
        draft = player_data.get('draftDetails', {})
        row['draftYear'] = draft.get('year')
        row['draftTeam'] = draft.get('teamAbbrev')
        row['draftRound'] = draft.get('round')
        row['draftPickInRound'] = draft.get('pickInRound')
        row['draftOverall'] = draft.get('overallPick')

        # Badges
        if player_data.get('badges'):
            row['badgesLogo'] = player_data['badges'][0]['logoUrl']['default']
            row['badgesTitle'] = player_data['badges'][0]['title']['default']
        else:
            row['badgesLogo'] = row['badgesTitle'] = None

        # Stat categories: featured, regular season, playoffs
        stat_categories = {
            'featuredSeason': player_data.get('featuredStats', {}).get('regularSeason', {}).get('subSeason', {}),
            'regSeasonCareer': player_data.get('careerTotals', {}).get('regularSeason', {}),
            'playoffsCareer': player_data.get('careerTotals', {}).get('playoffs', {})
        }

        keys = [
            'assists', 'gameWinningGoals', 'gamesPlayed', 'goals', 'otGoals', 'pim',
            'plusMinus', 'points', 'powerPlayGoals', 'powerPlayPoints', 'shootingPctg',
            'shorthandedGoals', 'shorthandedPoints', 'shots', 'timeOnIce', 'goalsAgainstAvg',
            'losses', 'shutouts', 'ties', 'wins', 'gamesStarted', 'goalsAgainst',
            'savePctg', 'OTlosses', 'shotsAgainst'
        ]

        for label, stats in stat_categories.items():
            for k in keys:
                col_name = f"{label}_{k}"
                row[col_name] = stats.get(k)

        # Append data
        for k, v in row.items():
            data[k].append(v)

    except Exception as e:
        print(f"Error for {playerID}: {e}")
        continue

# Final DataFrame
df = pd.DataFrame(data)
print(df.head())
