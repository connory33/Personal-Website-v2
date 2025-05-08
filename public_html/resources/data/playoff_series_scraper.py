import pandas as pd
from urllib.request import Request, urlopen
# import matplotlib.pyplot as plt
import numpy as np
import requests
import itertools

pd.set_option('display.max_columns', None)
pd.set_option('display.max_rows', None)

base_url = "https://api-web.nhle.com/v1/schedule/playoff-series/"

seriesExt = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o']
season_ext = ['19171918', '19181919', '19201921', '19211922', '19221923', '19231924', '19241925', '19251926', '19261927', '19271928',
              '19281929', '19291930', '19301931', '19311932', '19321933', '19331934', '19341935', '19351936', '19361937', '19371938',
              '19381939', '19391940', '19401941', '19411942', '19421943', '19431944', '19441945', '19451946', '19461947', '19471948',
              '19481949', '19491950', '19501951', '19511952', '19521953', '19531954', '19541955', '19551956', '19561957', '19571958',
              '19581959', '19591960', '19601961', '19611962', '19621963', '19631964', '19641965', '19651966', '19661967', '19671968',
              '19681969', '19691970', '19701971', '19711972', '19721973', '19731974', '19741975', '19751976', '19761977', '19771978',
              '19781979', '19791980', '19801981', '19811982', '19821983', '19831984', '19841985', '19851986', '19861987', '19871988',
              '19881989', '19891990', '19901991', '19911992', '19921993', '19931994', '19941995', '19951996', '19961997', '19971998',
              '19981999', '19992000', '20002001', '20012002', '20022003', '20032004', '20042005', '20052006', '20062007', '20072008',
              '20082009', '20092010', '20102011', '20112012', '20122013', '20132014', '20142015', '20152016', '20162017', '20172018',
              '20182019', '20192020', '20202021', '20212022', '20222023', '20232024', '20242025']

records = []

for season in season_ext:
    print("Season: ", season)
    for seriesLetter in seriesExt:
        url = base_url + season + '/' + seriesLetter
        try:
            response = requests.head(url, allow_redirects=True)
            if response.status_code == 404:
                print("Page not found (404).")
            else:
                # print("Page found: ", url)
                data = requests.get(url).json()
                for game in data['games']:
                    record = {
                        'season': game['season'],
                        'gameId': game['id'],
                        'seriesLetter': data.get('seriesLetter'),
                        'seriesLogo': data.get('seriesLogo'),
                        'neededToWin': data.get('neededToWin'),
                        'length': data.get('length'),
                        'awayTeamScore': game['awayTeam'].get('score'),
                        'homeTeamScore': game['homeTeam'].get('score'),
                        'gameCenterLink': game.get('gameCenterLink'),
                        'seriesStatusTopSeedWins': game.get('seriesStatus', {}).get('topSeedWins'),
                        'seriesStatusBottomSeedWins': game.get('seriesStatus', {}).get('bottomSeedWins'),
                        'fullCoverageURL': game.get('fullCoverageURL'),
                        'lastPeriodType': game.get('gameOutcome', {}).get('lastPeriodType')
                    }
                    records.append(record)
        except requests.RequestException as e:
            print(f"Error fetching JSON: {e}")
            continue

df = pd.DataFrame(records)
print(df.head(10))


df.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/playoff_series.csv", index=False)