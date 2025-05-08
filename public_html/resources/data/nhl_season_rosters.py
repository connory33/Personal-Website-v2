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
team_df = pd.DataFrame({'id': teamID, 'franchiseId': franchiseID, 'fullName': teamName, 'leagueId': teamLeagueId, 'triCode': teamTriCode})

# Prepare flat row containers
team_seasons = []
team_tricodes = []
team_ids = []
team_forward_ids = []
team_defense_ids = []
team_goalie_ids = []

for team_id, teamTriCode in zip(team_df['id'], team_df['triCode']):
    print(teamTriCode)

    seasons_played_url = f"https://api-web.nhle.com/v1/roster-season/{teamTriCode}/"

    try:
        response = requests.head(seasons_played_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            seasons_played = requests.get(seasons_played_url).json()
            for season in seasons_played:
                season_roster_url = f"https://api-web.nhle.com/v1/roster/{teamTriCode}/{season}/"
                roster_response = requests.get(season_roster_url)
                if roster_response.status_code == 404:
                    print("Roster not found (404).")
                    continue
                season_roster = roster_response.json()

                forwards = [f['id'] for f in season_roster.get('forwards', [])]
                defense = [d['id'] for d in season_roster.get('defensemen', [])]
                goalies = [g['id'] for g in season_roster.get('goalies', [])]

                # Append a flat row
                team_seasons.append(season)
                team_tricodes.append(teamTriCode)
                team_ids.append(team_id)
                team_forward_ids.append(forwards)
                team_defense_ids.append(defense)
                team_goalie_ids.append(goalies)

    except requests.RequestException as e:
        print("Error checking URL:", e)

# Build flat dataframe
team_season_rosters_df = pd.DataFrame({
    'team_id': team_ids,
    'team_tricode': team_tricodes,
    'season': team_seasons,
    'forwards': team_forward_ids,
    'defensemen': team_defense_ids,
    'goalies': team_goalie_ids
})

# Save
team_season_rosters_df.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/team_season_rosters.csv", index=False)
print(team_season_rosters_df.head())




