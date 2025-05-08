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

url = 'https://api-web.nhle.com/v1/gamecenter/2023020204/boxscore'
response = requests.get(url)
data = response.json()
# print(data)


skater_game_ID = []
skater_playerID = []
# skater_name = []
skater_sweaterNumber = []
skater_position = []
goalie_game_ID = []
goalie_playerID = []
# goalie_name = []
goalie_sweaterNumber = []
goalie_position = []
goals = []
assists = []
points = []
plusMinus = []
skater_pim = []
goalie_pim = []
hits = []
powerPlayGoals = []
sog = []
faceoffWinningPctg = []
skater_toi = []
goalie_toi = []
blockedShots = []
shifts = []
giveaways = []
takeaways = []
evenStrengthShotsAgainst = []
powerPlayShotsAgainst = []
shorthandedShotsAgainst = []
saveShotsAgainst = []
savePctg = []
evenStrengthGoalsAgainst = []
powerPlayGoalsAgainst = []
shorthandedGoalsAgainst = []
goalsAgainst = []
starter = []
shotsAgainst = []
saves = []

for gameID in gameID_list[-30000:-20000]:
    # print(f"Scraping game ID: {gameID}")
    # Construct the URL for the boxscore
    url = f"https://api-web.nhle.com/v1/gamecenter/{gameID}/boxscore"
    
    # Make the request to get the boxscore data
    response = requests.get(url)
    
    # Check if the response is valid (status code 200)
    if response.status_code == 200:
        data = response.json()

        if 'playerByGameStats' in data:
            # all fields except the one below should already be captured in game summary scrape - ignoring
            playerByGameStats = data['playerByGameStats']       

            awayPlayerByGameStats = playerByGameStats['awayTeam']
            homePlayerByGameStats = playerByGameStats['homeTeam']

            # these give lists of player dictionaries, not a dictionary itself
            awayForwardByGameStats = awayPlayerByGameStats['forwards']
            awayDefenseByGameStats = awayPlayerByGameStats['defense']
            awayGoalieByGameStats = awayPlayerByGameStats['goalies']

            homeForwardByGameStats = homePlayerByGameStats['forwards']
            homeDefenseByGameStats = homePlayerByGameStats['defense']
            homeGoalieByGameStats = homePlayerByGameStats['goalies']


            ### Skaters ###

            forwardDefenseKeys = [ 
                ('playerId', skater_playerID),
                ('sweaterNumber', skater_sweaterNumber),
                ('position', skater_position),
                ('pim', skater_pim),
                ('toi', skater_toi),
                ('goals', goals),
                ('assists', assists),
                ('points', points),
                ('plusMinus', plusMinus),
                ('hits', hits),
                ('powerPlayGoals',  powerPlayGoals),              
                ('sog', sog),
                ('faceoffWinningPctg', faceoffWinningPctg),
                ('blockedShots', blockedShots),
                ('shifts', shifts),
                ('giveaways', giveaways),
                ('takeaways', takeaways)
                ]
            
            # Away forwards
            for forward in awayForwardByGameStats:
                for key, target_list in forwardDefenseKeys:
                    if key in forward:
                        target_list.append(forward[key])
                    else:
                        target_list.append(np.nan)
                skater_game_ID.append(gameID)
                # skater_name.append(forward['name']['default'])

            # Away defense
            for defense in awayDefenseByGameStats:
                for key, target_list in forwardDefenseKeys:
                    if key in defense:
                        target_list.append(defense[key])
                    else:
                        target_list.append(np.nan)
                skater_game_ID.append(gameID)
                # skater_name.append(forward['name']['default'])

            # Home forwards
            for forward in homeForwardByGameStats:
                for key, target_list in forwardDefenseKeys:
                    if key in forward:
                        target_list.append(forward[key])
                    else:
                        target_list.append(np.nan)
                skater_game_ID.append(gameID)
                # skater_name.append(forward['name']['default'])

            # Home defense
            for defense in homeDefenseByGameStats:
                for key, target_list in forwardDefenseKeys:
                    if key in defense:
                        target_list.append(defense[key])
                    else:
                        target_list.append(np.nan)
                skater_game_ID.append(gameID)
                # skater_name.append(forward['name']['default'])
            

            ### Goalies ###
            goalieKeys = [
                ('playerId', goalie_playerID),
                ('sweaterNumber', goalie_sweaterNumber),
                ('position', goalie_position),
                ('pim', goalie_pim),
                ('toi', goalie_toi),
                ('evenStrengthShotsAgainst', evenStrengthShotsAgainst),
                ('powerPlayShotsAgainst', powerPlayShotsAgainst),
                ('shorthandedShotsAgainst', shorthandedShotsAgainst),
                ('saveShotsAgainst', saveShotsAgainst),
                ('savePctg', savePctg),
                ('evenStrengthGoalsAgainst', evenStrengthGoalsAgainst),
                ('powerPlayGoalsAgainst', powerPlayGoalsAgainst),
                ('shorthandedGoalsAgainst', shorthandedGoalsAgainst),
                ('goalsAgainst', goalsAgainst),
                ('starter', starter),
                ('shotsAgainst', shotsAgainst),
                ('saves', saves)
            ]

            # Away goalies
            for goalie in awayGoalieByGameStats:
                for key, target_list in goalieKeys:
                    if key in goalie:
                        target_list.append(goalie[key])
                    else:
                        target_list.append(np.nan)
                goalie_game_ID.append(gameID)
                # goalie_name.append(goalie['name']['default'])

            # Home goalies
            for goalie in homeGoalieByGameStats:
                for key, target_list in goalieKeys:
                    if key in goalie:
                        target_list.append(goalie[key])
                    else:
                        target_list.append(np.nan)
                goalie_game_ID.append(gameID)
                # goalie_name.append(goalie['name']['default'])       

        else:
            skater_game_ID.append(gameID)
            skater_playerID.append(np.nan)
            # skater_name.append(np.nan)
            skater_sweaterNumber.append(np.nan)
            skater_position.append(np.nan)
            goalie_game_ID.append(gameID)
            goalie_playerID.append(np.nan)
            # goalie_name.append(np.nan)
            goalie_sweaterNumber.append(np.nan)
            goalie_position.append(np.nan)
            goals.append(np.nan)
            assists.append(np.nan)
            points.append(np.nan)
            plusMinus.append(np.nan)
            skater_pim.append(np.nan)
            goalie_pim.append(np.nan)
            hits.append(np.nan)
            powerPlayGoals.append(np.nan)
            sog.append(np.nan)
            faceoffWinningPctg.append(np.nan)
            skater_toi.append(np.nan)
            goalie_toi.append(np.nan)
            blockedShots.append(np.nan)
            shifts.append(np.nan)
            giveaways.append(np.nan)
            takeaways.append(np.nan)
            evenStrengthShotsAgainst.append(np.nan)
            powerPlayShotsAgainst.append(np.nan)
            shorthandedShotsAgainst.append(np.nan)
            saveShotsAgainst.append(np.nan)
            savePctg.append(np.nan)
            evenStrengthGoalsAgainst.append(np.nan)
            powerPlayGoalsAgainst.append(np.nan)
            shorthandedGoalsAgainst.append(np.nan)
            goalsAgainst.append(np.nan)
            starter.append(np.nan)
            shotsAgainst.append(np.nan)
            saves.append(np.nan)

    else:
        print(f"Failed to retrieve data for game ID {gameID}. Status code: {response.status_code}")
        


# print(len(skater_game_ID), len(skater_playerID), len(skater_name), len(skater_sweaterNumber), len(skater_position))
# print(len(goalie_game_ID), len(goalie_playerID), len(goalie_name), len(goalie_sweaterNumber), len(goalie_position))
# print(len(goals), len(assists), len(points), len(plusMinus), len(pim), len(hits), len(powerPlayGoals), len(sog), len(faceoffWinningPctg), len(toi), len(blockedShots), len(shifts), len(giveaways), len(takeaways))
# print(len(evenStrengthShotsAgainst), len(powerPlayShotsAgainst), len(shorthandedShotsAgainst), len(saveShotsAgainst), len(savePctg), len(evenStrengthGoalsAgainst), len(powerPlayGoalsAgainst), len(shorthandedGoalsAgainst), len(goalsAgainst), len(starter), len(shotsAgainst), len(saves))



skaters_df = pd.DataFrame({
            'gameID': skater_game_ID,
            'playerId': skater_playerID,
            # 'name': skater_name,
            'sweaterNumber': skater_sweaterNumber,
            'position': skater_position,
            'goals': goals,
            'assists': assists,
            'points': points,
            'plusMinus': plusMinus,
            'pim': skater_pim,
            'hits': hits,
            'powerPlayGoals': powerPlayGoals,              
            'sog': sog,
            'faceoffWinningPctg': faceoffWinningPctg,
            'toi': skater_toi,
            'blockedShots': blockedShots,
            'shifts': shifts,
            'giveaways': giveaways,
            'takeaways': takeaways
        })




goalies_df = pd.DataFrame({
            'gameID': goalie_game_ID,
            'playerId': goalie_playerID,
            # 'name': goalie_name,
            'sweaterNumber': goalie_sweaterNumber,
            'position': goalie_position,
            'pim': goalie_pim,
            'toi': goalie_toi,
            'evenStrengthShotsAgainst': evenStrengthShotsAgainst,
            'powerPlayShotsAgainst': powerPlayShotsAgainst,
            'shorthandedShotsAgainst': shorthandedShotsAgainst,
            'saveShotsAgainst': saveShotsAgainst,
            'savePctg': savePctg,
            'evenStrengthGoalsAgainst': evenStrengthGoalsAgainst,
            'powerPlayGoalsAgainst': powerPlayGoalsAgainst,
            'shorthandedGoalsAgainst': shorthandedGoalsAgainst,
            'goalsAgainst': goalsAgainst,
            'starter': starter,
            'shotsAgainst': shotsAgainst,
            'saves': saves
        })

print(skaters_df.head())
skaters_df.to_csv('skaters_gamebygame_stats.csv', index=False)
print(goalies_df.head())
goalies_df.to_csv('goalies_gamebygame_stats.csv', index=False)
print("Dataframes created and saved to CSV files.")