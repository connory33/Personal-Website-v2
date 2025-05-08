import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

data = pd.read_csv('resources/data/team_prospects.csv')

# print(data)

player_base_url = "https://api-web.nhle.com/v1/player/" 
player_suffix = "/landing"

for prospect_id in data['prospect_id']:
    url = player_base_url + str(prospect_id) + player_suffix
    response = requests.get(url)
    data = response.json()
    print(data)