import pandas as pd
from urllib.request import Request, urlopen
# import matplotlib.pyplot as plt
import numpy as np
import requests
import itertools

pd.set_option('display.max_columns', None)
pd.set_option('display.max_rows', None)

# url = "https://api.nhle.com/stats/rest/en/draft"
# data = requests.get(url).json()

# draftID = []
# draftYear = []
# draftRounds = []

# for year in data['data']:
#     draftID.append(year['id'])
#     draftYear.append(year['draftYear'])
#     draftRounds.append(year['rounds'])

# df = pd.DataFrame([draftID, draftYear, draftRounds]).transpose()
# df.columns = ['draftID', 'draftYear', 'draftRounds']

# df.to_csv('draft_years.csv', index=False)

######################################################################################################

# seasons = ['1917', '1918', '1918, 1919', '1919, 1920', '1920, 1921', '1921, 1922', '1922, 1923', '1923',
#            '1924, 1925', '1925, 1926', '1926, 1927', '1927, 1928', '1928, 1929', '1929, 1930', '1930, 1931',
#            '1932', '1933', '1934', '1935', '1936', '1937', '1938', '1939', '1940', '1941',
#            '1942', '1943', '1944', '1945', '1946', '1947', '1948', '1949', '1950', '1951',
#             '1952', '1953', '1954', '1955', '1956', '1957', '1958', '1959',
#             '1960', '1961', '1962', '1963', '1964', '1965', '1966', '1967', '1968', '1969',
#             '1970', '1971', '1972', '1973', '1974', '1975', '1976', '1977',
#             '1978', '1979', '1980', '1981', '1982', '1983', '1984', '1985',
#             '1986', '1987', '1988', '1989', '1990', '1991', '1992', '1993',
#             '1994', '1995', '1996', '1997', '1998', '1999', '2000', '2001',
#             '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009',
#             '2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017',
#             '2018', '2019', '2020', '2021', '2022', '2023', '2024']


# draftYear = []; selectableRounds = []; round = []; pickInRound = []; overallPick = []; teamID = []; teamPickHistory = []
# firstName = []; lastName = []; position = []; country = []; height = []; weight = []; amateurLeague = []; amateurClubName = []



# base_url = "https://api-web.nhle.com/v1/draft/picks/"
# suffix = "/all"

# for season in seasons:
#     url = base_url + season + suffix

#     try:
#         response = requests.head(url, allow_redirects=True)
#         if response.status_code == 404:
#             print("Page not found (404).")
#         else:
#             # print("Page found.")
#             draft_data = requests.get(url).json()
            
#             for pick in draft_data['picks']:
#                 # print(pick)
#                 draftYear.append(season)
#                 selectableRounds.append(len(draft_data['selectableRounds']))
#                 round.append(pick['round'])
#                 pickInRound.append(pick['pickInRound'])
#                 overallPick.append(pick['overallPick'])
#                 teamID.append(pick['teamId'])
#                 teamPickHistory.append(pick['teamPickHistory'])
#                 if 'firstName' in pick:
#                     firstName.append(pick['firstName']['default'])
#                 else:
#                     firstName.append(None)
#                 if 'lastName' in pick:
#                     lastName.append(pick['lastName']['default'])
#                 else:
#                     lastName.append(None)
#                 if 'positionCode' in pick:
#                     position.append(pick['positionCode'])
#                 else:
#                     position.append(None)
#                 if 'countryCode' in pick:
#                     country.append(pick['countryCode'])
#                 else:
#                     country.append(None)
#                 if 'height' in pick:
#                     height.append(pick['height'])
#                 else:
#                     height.append(None)
#                 if 'weight' in pick:   
#                     weight.append(pick['weight'])
#                 else:
#                     weight.append(None)
#                 if 'amateurLeague' in pick:
#                     amateurLeague.append(pick['amateurLeague'])
#                 else:
#                     amateurLeague.append(None)
#                 if 'amateurClubName' in pick:
#                     amateurClubName.append(pick['amateurClubName'])
#                 else:
#                     amateurClubName.append(None)



#     except requests.RequestException as e:
#         print(f"Error fetching JSON: {e}")
#         continue


# df = pd.DataFrame([draftYear, selectableRounds, round, pickInRound, overallPick, teamID, teamPickHistory,
#                 firstName, lastName, position, country, height, weight, amateurLeague,
#                 amateurClubName]).transpose()
# df.columns = ['draftYear', 'selectableRounds', 'round', 'pickInRound', 'overallPick', 'teamID', 'teamPickHistory',
#                 'firstName', 'lastName', 'position', 'country', 'height', 'weight',
#                 'amateurLeague', 'amateurClubName']

# # print(df.head())

# df.to_csv('draft_picks.csv', index=False)


#############################################################################################################################

# url = "https://api-web.nhle.com/v1/draft/rankings/2025/1"
# data = requests.get(url).json()
# print(data)

draftYearList = ['2025', '2024', '2023', '2022', '2021', '2020', '2019', '2018', '2017', '2016', '2015', '2014', '2013', '2012', '2011', '2010', '2009', '2008']
categories = ['1', '2', '3', '4']

draftYears = []
categoryID = []
# rankings
lastName = []
firstName = []
position = []
shootsCatches = []
heightIn = []
weightLbs = []
lastClub = []
lastLeague = []
birthDate = []
birthCity = []
birthStateProvince = []
birthCountry = []
midtermRank = []
finalRank = []

base_url = "https://api-web.nhle.com/v1/draft/rankings/"

for draftYear in draftYearList:
    # print('check')
    for category in categories:
        url = base_url + draftYear + "/" + category
        # print(url)
        try:
            response = requests.head(url, allow_redirects=True)
            if response.status_code == 404:
                print("Page not found (404).")
            else:
                draft_data = requests.get(url).json()

                for player in draft_data['rankings']:
                    draftYears.append(draftYear)
                    categoryID.append(category)

                    keys = [
                        ('lastName', lastName),
                        ('firstName', firstName),
                        ('positionCode', position),
                        ('shootsCatches', shootsCatches),
                        ('heightInInches', heightIn),
                        ('weightInPounds', weightLbs),
                        ('lastAmateurClub', lastClub),
                        ('lastAmateurLeague', lastLeague),
                        ('birthDate', birthDate),
                        ('birthCity', birthCity),
                        ('birthStateProvince', birthStateProvince),
                        ('birthCountry', birthCountry),
                        ('midtermRank', midtermRank),
                        ('finalRank', finalRank)
                    ]
                    for key, target_list in keys:
                        if key in player:
                            target_list.append(player[key])
                        else:
                            target_list.append(None)


        except requests.RequestException as e:
            print(f"Error fetching JSON: {e}")
            continue


df = pd.DataFrame([draftYears, categoryID, lastName, firstName, position, shootsCatches, heightIn, weightLbs,
                lastClub, lastLeague, birthDate, birthCity, birthStateProvince, birthCountry,
                midtermRank, finalRank]).transpose()
df.columns = ['draftYear', 'categoryID', 'lastName', 'firstName', 'position', 'shootsCatches', 'heightIn',
                'weightLbs', 'lastClub', 'lastLeague', 'birthDate', 'birthCity', 'birthStateProvince',
                'birthCountry', 'midtermRank', 'finalRank']

df.to_csv('draft_rankings.csv', index=False)
