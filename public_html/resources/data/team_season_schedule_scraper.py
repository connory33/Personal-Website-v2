import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

base_url = 'https://api-web.nhle.com/v1/club-schedule-season/'
team_ext = 'TOR'
season_ext = '20232024'

try:
        url = f"{base_url}{team_ext}/{season_ext}"
        response = requests.head(url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
             print("Page found.")
             schedule_data = requests.get(url).json()
             print(schedule_data)
              
except requests.RequestException as e:
    print(f"Error fetching JSON")