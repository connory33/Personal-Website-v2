import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

shiftData = pd.read_csv('resources/data/nhl_shifts.csv')
shiftData = shiftData[1:1000]

def time_to_sec(t):
    m, s = map(int, t.split(':'))
    return m * 60 + s

# print(shiftData)

playerIDs = shiftData['playerID'].unique()

team_ids = shiftData['teamId'].unique()

shifts_list = shiftData.to_dict(orient='records')
max_end_time = max(time_to_sec(shift['endTime']) for shift in shifts_list)
on_ice = [{tid: set() for tid in team_ids} for _ in range(max_end_time + 1)]


for shift in shifts_list:
    pid = shift['playerID']
    team = shift['teamId']
    start = time_to_sec(shift['startTime'])
    end = time_to_sec(shift['endTime'])
    for t in range(start, end):
        if 0 <= t < len(on_ice):
            on_ice[t][team].add(pid)


# Assume: on_ice = [{team_id1: set(...), team_id2: set(...)}, ...]
# Also assume each second's dict always has exactly 2 teams

rows = []

# Get consistent team ordering (e.g., home/away based on order in data)
team_ids = list(on_ice[0].keys())  # assuming both teams always present

team1, team2 = team_ids[0], team_ids[1]

for second, second_data in enumerate(on_ice):
    row = {
    'gameID': shiftData['gameID'],
    'second': second,
    f'team_{team1}': ','.join(map(str, second_data[team1])),
    f'team_{team2}': ','.join(map(str, second_data[team2])),
    }
    rows.append(row)

df = pd.DataFrame(rows)
df.to_csv('on_ice_by_second.csv', index=False)
print(df.head())



