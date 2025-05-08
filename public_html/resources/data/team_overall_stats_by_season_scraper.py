import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

seasons = ['20242025', '20232024', '20222023', '20212022', '20202021', '20192020', '20182019', '20172018', '20162017', '20152016',
           '20142015', '20132014', '20122013', '20112012', '20102011', '20092010', '20082009', '20072008', '20062007', '20052006',
           '20042005', '20032004', '20022003', '20012002', '20002001', '19992000', '19982099', '19971998', '19961997', '19951996', 
           '19941995', '19931994', '19921993', '19911992', '19901991', '19891990', '19881989', '19871988', '19861987', '19851986',
           '19841985', '19831984', '19821983', '19811982', '19801981', '19791980', '19781979', '19771978', '19761977', '19751976',
           '19741975', '19731974', '19721973', '19711972', '19701971', '19691970', '19681969', '19671968', '19661967', '19651966',
           '19641965', '19631964', '19621963', '19611962', '19601961', '19591960', '19581959', '19571958', '19561957', '19551956',
           '19541955', '19531954', '19521953', '19511952', '19501951', '19491950', '19481949', '19471948', '19461947', '19451946',
           '19441945', '19431944', '19421943', '19411942', '19401941', '19391940', '19381939', '19371938', '19361937', '19351936',
           '19341935', '19331934', '19321933', '19311932', '19301931', '19291930', '19281929', '19271928', '19261927', '19251926',
           '19241925', '19231924', '19221923', '19211922', '19201921', '19191920', '19181919', '19171918', '19161917', '19151916']

season_id = []
faceoffWinPct = []
gamesPlayed = []
goalsAgainst = []
goalsAgainstPerGame = []
goalsFor = []
goalsForPerGame = []
losses = []
otLosses = []
penaltyKillNetPct = []
penaltyKillPct = []
pointPct = []
points = []
powerPlayNetPct = []
powerPlayPct = []
regulationAndOtWins = []
seasonId = []
shotsAgainstPerGame = []
shotsForPerGame = []
teamFullName = []
teamId = []
ties = []
wins = []
winsInRegulation = []
winsInShootout = []

base_url= 'https://api.nhle.com/stats/rest/en/team/summary?sort=shotsForPerGame&cayenneExp=seasonId='

for season in seasons:
    try:
        url = f"{base_url}{season}"
        response = requests.head(url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            print("Page found.")
            response = requests.get(url)
            data = response.json()

            for team in data['data']:
                # print(team['teamFullName'], team['shotsForPerGame'])
                # print(team.keys())
                keys_list = [
                        ('faceoffWinPct', faceoffWinPct),
                        ('gamesPlayed', gamesPlayed),
                        ('goalsAgainst', goalsAgainst),
                        ('goalsAgainstPerGame', goalsAgainstPerGame),
                        ('goalsFor', goalsFor),
                        ('goalsForPerGame', goalsForPerGame),
                        ('losses', losses),
                        ('otLosses',  otLosses),
                        ('penaltyKillNetPct', penaltyKillNetPct),
                        ('penaltyKillPct',  penaltyKillPct),
                        ('pointPct', pointPct),
                        ('points', points),
                        ('powerPlayNetPct', powerPlayNetPct ),
                        ('powerPlayPct', powerPlayPct),
                        ('regulationAndOtWins', regulationAndOtWins),
                        ('seasonId', seasonId),
                        ('shotsAgainstPerGame', shotsAgainstPerGame),
                        ('shotsForPerGame', shotsForPerGame),
                        ('teamFullName', teamFullName),
                        ('teamId', teamId),
                        ('ties', ties),
                        ('wins', wins),
                        ('winsInRegulation', winsInRegulation),
                        ('winsInShootout', winsInShootout)
                ]

                for key, target_list in keys_list:
                    target_list.append(team.get(key, None))

                season_id.append(season)
    except requests.RequestException as e:
        print(f"Error fetching JSON: {e}")





# Create a DataFrame from the lists
team_overall_stats_df = pd.DataFrame([season_id, faceoffWinPct, gamesPlayed, goalsAgainst, goalsAgainstPerGame,
               goalsFor, goalsForPerGame, losses, otLosses, penaltyKillNetPct,
               penaltyKillPct, pointPct, points, powerPlayNetPct, powerPlayPct,
               regulationAndOtWins, seasonId, shotsAgainstPerGame, shotsForPerGame,
               teamId, ties, wins, winsInRegulation, winsInShootout]).transpose()

# Set the column names
team_overall_stats_df.columns = ['season_id', 'faceoffWinPct', 'gamesPlayed', 'goalsAgainst', 'goalsAgainstPerGame',
               'goalsFor', 'goalsForPerGame', 'losses', 'otLosses', 'penaltyKillNetPct',
               'penaltyKillPct', 'pointPct', 'points', 'powerPlayNetPct', 'powerPlayPct',
               'regulationAndOtWins', 'seasonId', 'shotsAgainstPerGame', 'shotsForPerGame',
               'teamId', 'ties', 'wins', 'winsInRegulation', 'winsInShootout']

# Print the DataFrame to check the data
print(team_overall_stats_df.head())

# Save the DataFrame to a CSV file
team_overall_stats_df.to_csv("team_overall_stats_by_season.csv", index=False)












               