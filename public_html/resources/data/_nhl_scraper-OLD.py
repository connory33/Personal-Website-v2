import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

pd.set_option('display.max_columns', None)

# get df of all games - 1 row per game, primary key is game ID
games = "https://api.nhle.com/stats/rest/en/game"
games_summary = requests.get(games).json()
game_df = pd.DataFrame(games_summary['data'])

# get list of all game IDs
gameID_list = game_df['id']

# build URL for individual game details + play-by-play
base_url = "https://api-web.nhle.com/v1/gamecenter/"
game_code = '2024021247'
suffix= "/play-by-play"





################################ GAME SUMMARY INFORMATION (Primary Key GameID, location, teams, score, etc.) ####################################

# game info
# id = []; season = []; gameType = []; gameNumber = []; gameDate = []; easternStartTime = []; gameStateId = [] 
# venue = []; venueLocation = []; awayTeamId = []; awayTeamName = []; awayScore = []; awayShots = []; awayLogo = []
# homeTeamId = []; homeTeamName = []; homeScore = []; homeShots = []; homeLogo = []; shootoutInUse = []; otInUse = []
# gameOutcome = []; regPeriods = []

# for game_id in gameID_list[50000:]:
#     # set game code to current game and pull JSON
#     game_code = str(game_id)
#     url = base_url+game_code+suffix
#     try:
#         response = requests.head(url, allow_redirects=True)
#         if response.status_code == 404:
#             print("Page not found (404).")
#         else:
#             pbp = requests.get(url).json()
#             # append all fields from this game to their respective lists, adding None if not found
#             id.append(game_id)
#             season.append(pbp['season'])
#             gameType.append(pbp['gameType']) 

#             game_row = game_df[game_df['id'] == game_id]
#             if not game_row.empty:
#                 gameNumber.append(game_row.iloc[0]['gameNumber'])
#                 easternStartTime.append(game_row.iloc[0]['easternStartTime'])
#                 gameStateId.append(game_row.iloc[0]['gameStateId'])
#             else:
#                 gameNumber.append(None)
#                 easternStartTime.append(None)
#                 gameStateId.append(None)

#             if 'gameDate' in pbp:
#                 gameDate.append(pbp['gameDate'])
#             else:
#                 gameDate.append(None)

#             if 'plays' in pbp:
#                 pbp.pop('plays')
            
#             if 'rosterSpots' in pbp:
#                 pbp.pop('rosterSpots')

#             if 'venue' in pbp:
#                 venue.append(pbp['venue']['default'])
#             else:
#                 venue.append(None)

#             if 'venueLocation' in pbp:
#                 venueLocation.append(pbp['venueLocation']['default'])
#             else:
#                 venueLocation.append(None)

#             if 'awayTeam' in pbp:
#                 awayTeamId.append(pbp['awayTeam']['id'])
#                 awayTeamName.append(pbp['awayTeam']['commonName']['default'])
#                 awayScore.append(pbp['awayTeam']['score'])
#                 if 'sog' in pbp['awayTeam']:
#                     awayShots.append(pbp['awayTeam']['sog'])
#                 else:
#                     awayShots.append(None)
#                 if 'logo' in pbp['awayTeam']:
#                     awayLogo.append(pbp['awayTeam']['logo'])
#                 else:
#                     awayLogo.append(None)
#             else:
#                 awayTeamId.append(None)
#                 awayTeamName.append(None)
#                 awayScore.append(None)
#                 awayShots.append(None)
#                 awayLogo.append(None)

#             if 'homeTeam' in pbp:
#                 homeTeamId.append(pbp['homeTeam']['id'])
#                 homeTeamName.append(pbp['homeTeam']['commonName']['default'])
#                 homeScore.append(pbp['homeTeam']['score'])
#                 if 'sog' in pbp['homeTeam']:
#                     homeShots.append(pbp['homeTeam']['sog'])
#                 else:
#                     homeShots.append(None)
#                 if 'logo' in pbp['homeTeam']:
#                     homeLogo.append(pbp['homeTeam']['logo'])
#                 else:
#                     homeLogo.append(None)
#             else:
#                 homeTeamId.append(None)
#                 homeTeamName.append(None)
#                 homeScore.append(None)
#                 homeShots.append(None)
#                 homeLogo.append(None)

#             if 'shootoutInUse' in pbp:
#                 shootoutInUse.append(pbp['shootoutInUse'])
#             else:
#                 shootoutInUse.append(None)

#             if 'otInUse' in pbp:
#                 otInUse.append(pbp['otInUse'])
#             else:
#                 otInUse.append(None)

#             if 'gameOutcome' in pbp:
#                 gameOutcome.append(pbp['gameOutcome']['lastPeriodType'])
#             else:
#                 gameOutcome.append(None)

#             if 'regPeriods' in pbp:
#                 regPeriods.append(pbp['regPeriods'])
#             else:
#                 regPeriods.append(None)
#     except requests.RequestException as e:
#         print("Error checking URL:", e)


# df_games = pd.DataFrame([id,season,gameType,gameNumber,gameDate,easternStartTime,gameStateId,venue,venueLocation,awayTeamId,awayTeamName,awayScore,
#                          awayShots,awayLogo, homeTeamId,homeTeamName, homeScore, homeShots, homeLogo, shootoutInUse,otInUse,
#                          gameOutcome,regPeriods]).transpose()

# df_games.columns = ['id','season','gameType','gameNumber','gameDate','easternStartTime','gameStateId','venue','venueLocation','awayTeamId','awayTeamName','awayScore',
#                          'awayShots','awayLogo', 'homeTeamId','homeTeamName', 'homeScore', 'homeShots', 'homeLogo', 'shootoutInUse','otInUse',
#                          'gameOutcome','regPeriods']
# df_games.to_csv("nhl_games.csv", index=False)


################################################ PLAY-BY-PLAY + ADDL. GAME DETAILS ###################################################

# gameID = []; eventID = []; period = []; timeInPeriod = []; timeRemaining = []; situationCode = []
# typeCode = []; typeDescKey = []; xCoord = []; yCoord = []; zoneCode = []; eventOwnerTeamId = []
# faceoffLoserId = []; faceoffWinnerId = []; hittingPlayerId = []; hitteePlayerId = []; shotType = []
# shootingPlayerId = []; goalieInNetId = []; awaySOG = []; homeSOG = []; reason = []; takeawayGiveawayPlayerId = []
# blockingPlayerId = []; scoringPlayerId = []; assist1PlayerId = []; assist1PlayerTotal = []; awayScore = []
# homeScore = []; penaltySeverity = []; penaltyType = []; duration = []; committerId = []; drawerId = []

# for game_id in gameID_list[-1500:-1000]:
#     game_url = base_url + str(game_id) + suffix
#     try:
#         response = requests.head(game_url, allow_redirects=True)
#         if response.status_code == 404:
#             print("Page not found (404).")
#         else:
#             data = requests.get(game_url).json()
#             if not data['plays']: # checks to see if plays list is empty
#                 gameID.append(data['id'])
#                 eventID.append('No data available')
#                 period.append('No data available')
#                 timeInPeriod.append('No data available')
#                 timeRemaining.append('No data available')
#                 situationCode.append('No data available')
#                 typeCode.append('No data available')
#                 typeDescKey.append('No data available')
#                 xCoord.append('No data available')
#                 yCoord.append('No data available')
#                 zoneCode.append('No data available')
#                 eventOwnerTeamId.append('No data available')
#                 faceoffLoserId.append('No data available')
#                 faceoffWinnerId.append('No data available')
#                 hittingPlayerId.append('No data available')
#                 hitteePlayerId.append('No data available')
#                 shotType.append('No data available')
#                 shootingPlayerId.append('No data available')
#                 goalieInNetId.append('No data available')
#                 awaySOG.append('No data available')
#                 homeSOG.append('No data available')
#                 reason.append('No data available')
#                 takeawayGiveawayPlayerId.append('No data available')
#                 blockingPlayerId.append('No data available')
#                 scoringPlayerId.append('No data available')
#                 assist1PlayerId.append('No data available')
#                 assist1PlayerTotal.append('No data available')
#                 awayScore.append('No data available')
#                 homeScore.append('No data available')
#                 penaltySeverity.append('No data available')
#                 penaltyType.append('No data available')
#                 duration.append('No data available')
#                 committerId.append('No data available')
#                 drawerId.append('No data available')

#             else:
#                 for play in data['plays']:
#                     # print(data['plays'])
#                     # add game ID to each row for joining
#                     gameID.append(data['id'])

#                     # capture basic info that is always present for every play entry
#                     eventID.append(play['eventId'])
#                     period.append(play['periodDescriptor']['number'])
#                     timeInPeriod.append(play['timeInPeriod'])
#                     timeRemaining.append(play['timeRemaining'])
#                     typeCode.append(play['typeCode'])
#                     typeKey = play['typeDescKey']
#                     typeDescKey.append(typeKey)
#                     if 'situationCode' in play:
#                         situationCode.append(play['situationCode'])
#                     else:
#                         situationCode.append(None)

#                     # move on if there are no additional details to capture about the play (e.g. period end)
#                     if 'details' not in play:
#                         xCoord.append(None)
#                         yCoord.append(None)
#                         zoneCode.append(None)
#                         eventOwnerTeamId.append(None)
#                         faceoffLoserId.append(None)
#                         faceoffWinnerId.append(None)
#                         hittingPlayerId.append(None)
#                         hitteePlayerId.append(None)
#                         shotType.append(None)
#                         shootingPlayerId.append(None)
#                         goalieInNetId.append(None)
#                         awaySOG.append(None)
#                         homeSOG.append(None)
#                         reason.append(None)
#                         takeawayGiveawayPlayerId.append(None)
#                         blockingPlayerId.append(None)
#                         scoringPlayerId.append(None)
#                         assist1PlayerId.append(None)
#                         assist1PlayerTotal.append(None)
#                         awayScore.append(None)
#                         homeScore.append(None)
#                         penaltySeverity.append(None)
#                         penaltyType.append(None)
#                         duration.append(None)
#                         committerId.append(None)
#                         drawerId.append(None)
#                     else:
#                         # otherwise, first capture all the general fields that are present for every details field (if existing)
#                         if 'xCoord' in play['details'] and 'yCoord' in play['details']:
#                             xCoord.append(play['details']['xCoord'])
#                             yCoord.append(play['details']['yCoord'])
#                         else:
#                             xCoord.append(None)
#                             yCoord.append(None)
                            
#                         if 'zoneCode' in play['details']:
#                             zoneCode.append(play['details']['zoneCode'])
#                         else:
#                             zoneCode.append(None)

#                         if 'eventOwnerTeamId' in play['details']:
#                             eventOwnerTeamId.append(play['details']['eventOwnerTeamId'])
#                         else:
#                             eventOwnerTeamId.append(None)

#                         # now, capture fields that are filled or not dependent on play type

#                         # Faceoff
#                         if typeKey == 'faceoff':
#                             faceoffLoserId.append(play['details']['losingPlayerId'])
#                             faceoffWinnerId.append(play['details']['winningPlayerId'])
#                             hittingPlayerId.append(None)
#                             hitteePlayerId.append(None)
#                             shotType.append(None)
#                             shootingPlayerId.append(None)
#                             goalieInNetId.append(None)
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(None)
#                             takeawayGiveawayPlayerId.append(None)
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)

#                         # Hits
#                         elif typeKey == 'hit':
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(play['details']['hittingPlayerId']) 
#                             hitteePlayerId.append(play['details']['hitteePlayerId'])
#                             shotType.append(None)
#                             shootingPlayerId.append(None)
#                             goalieInNetId.append(None)
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(None)
#                             takeawayGiveawayPlayerId.append(None)
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)

#                         # Missed Shots
#                         elif typeKey == 'missed-shot':
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(None) 
#                             hitteePlayerId.append(None)
#                             shotType.append(play['details']['shotType'])
#                             shootingPlayerId.append(play['details']['shootingPlayerId'])
#                             if 'goalieInNetId' in play['details']:
#                                 goalieInNetId.append(play['details']['goalieInNetId'])
#                             else:
#                                 goalieInNetId.append('Not listed')
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(play['details']['reason'])
#                             takeawayGiveawayPlayerId.append(None)
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)
                        
#                         # Shots on Goal
#                         elif typeKey == 'shot-on-goal':
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(None) 
#                             hitteePlayerId.append(None)
#                             shotType.append(play['details']['shotType'])
#                             shootingPlayerId.append(play['details']['shootingPlayerId'])
#                             if 'goalieInNetId' in play['details']:
#                                 goalieInNetId.append(play['details']['goalieInNetId'])
#                             else:
#                                 goalieInNetId.append('Not listed')
#                             awaySOG.append(play['details']['awaySOG'])
#                             homeSOG.append(play['details']['homeSOG'])
#                             reason.append(None)
#                             takeawayGiveawayPlayerId.append(None)
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)

#                         # Stoppages
#                         elif typeKey == 'stoppage':
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(None) 
#                             hitteePlayerId.append(None)
#                             shotType.append(None)
#                             shootingPlayerId.append(None)
#                             goalieInNetId.append(None)
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(play['details']['reason'])
#                             takeawayGiveawayPlayerId.append(None)
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)

#                         # Takeaway or Giveaway
#                         elif typeKey in ['takeaway', 'giveaway']:
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(None) 
#                             hitteePlayerId.append(None)
#                             shotType.append(None)
#                             shootingPlayerId.append(None)
#                             goalieInNetId.append(None)
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(None)
#                             takeawayGiveawayPlayerId.append(play['details']['playerId'])
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)

#                         # Blocked Shot
#                         elif typeKey == 'blocked-shot':
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(None) 
#                             hitteePlayerId.append(None)
#                             shotType.append(None)
#                             shootingPlayerId.append(play['details']['shootingPlayerId'])
#                             goalieInNetId.append(None)
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(None)
#                             takeawayGiveawayPlayerId.append(None)
#                             if 'blockingPlayerId' in play['details']:
#                                 blockingPlayerId.append(play['details']['blockingPlayerId'])
#                             else:
#                                 blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)

#                         # Goal
#                         elif typeKey == 'goal':
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(None) 
#                             hitteePlayerId.append(None)
#                             if 'shotType' in play['details']:
#                                 shotType.append(play['details']['shotType'])
#                             else:
#                                 shotType.append(None)
#                             shootingPlayerId.append(None)
#                             if 'goalieInNetId' in play['details']:
#                                 goalieInNetId.append(play['details']['goalieInNetId'])
#                             else:
#                                 goalieInNetId.append('Not listed')
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(None)
#                             takeawayGiveawayPlayerId.append(None)
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(play['details']['scoringPlayerId'])
#                             if 'assist1PlayerId' in play['details']:
#                                 assist1PlayerId.append(play['details']['assist1PlayerId'])
#                                 assist1PlayerTotal.append(play['details']['assist1PlayerTotal'])
#                             else:
#                                 assist1PlayerId.append('Not listed')
#                                 assist1PlayerTotal.append('Not listed')
#                             awayScore.append(play['details']['awayScore'])
#                             homeScore.append(play['details']['homeScore'])
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)

#                         # Penalty
#                         elif typeKey == 'penalty':
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(None) 
#                             hitteePlayerId.append(None)
#                             shotType.append(None)
#                             shootingPlayerId.append(None)
#                             goalieInNetId.append(None)
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(None)
#                             takeawayGiveawayPlayerId.append(None)
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(play['details']['typeCode'])
#                             penaltyType.append(play['details']['descKey'])
#                             duration.append(play['details']['duration'])
#                             if 'committedByPlayerId' in play['details']:
#                                 committerId.append(play['details']['committedByPlayerId'])
#                             else:
#                                 committerId.append('Not listed')
#                             if 'drawnByPlayerId' in play['details']:
#                                 drawerId.append(play['details']['drawnByPlayerId'])
#                             else:
#                                 drawerId.append('Not listed')

#                         else:
#                             faceoffLoserId.append(None)
#                             faceoffWinnerId.append(None)
#                             hittingPlayerId.append(None) 
#                             hitteePlayerId.append(None)
#                             shotType.append(None)
#                             shootingPlayerId.append(None)
#                             goalieInNetId.append(None)
#                             awaySOG.append(None)
#                             homeSOG.append(None)
#                             reason.append(None)
#                             takeawayGiveawayPlayerId.append(None)
#                             blockingPlayerId.append(None)
#                             scoringPlayerId.append(None)
#                             assist1PlayerId.append(None)
#                             assist1PlayerTotal.append(None)
#                             awayScore.append(None)
#                             homeScore.append(None)
#                             penaltySeverity.append(None)
#                             penaltyType.append(None)
#                             duration.append(None)
#                             committerId.append(None)
#                             drawerId.append(None)
#     except requests.RequestException as e:
#         print("Error checking URL:", e)            
    

# df_plays = pd.DataFrame([gameID, eventID,period,timeInPeriod,timeRemaining,situationCode,typeCode,typeDescKey,xCoord,yCoord,zoneCode,eventOwnerTeamId,
#                    faceoffLoserId,faceoffWinnerId,hittingPlayerId,hitteePlayerId,shotType,shootingPlayerId,goalieInNetId,
#                    awaySOG,homeSOG,reason,takeawayGiveawayPlayerId,blockingPlayerId,scoringPlayerId,assist1PlayerId,
#                    assist1PlayerTotal,awayScore,homeScore,penaltySeverity,penaltyType,duration,committerId,drawerId]).transpose()
# # for column in ['shootingPlayerId', 'goalieInNetId']:
# #     df_plays[column] = df_plays[column].astype(int)

# df_plays.columns = ['gameID', 'eventID','period','timeInPeriod','timeRemaining','situationCode','typeCode','typeDescKey','xCoord','yCoord',
#                    'zoneCode','eventOwnerTeamId','faceoffLoserId','faceoffWinnerId','hittingPlayerId','hitteePlayerId',
#                    'shotType','shootingPlayerId','goalieInNetId','awaySOG','homeSOG','reason','takeawayGiveawayPlayerId',
#                    'blockingPlayerId','scoringPlayerId','assist1PlayerId','assist1PlayerTotal','awayScore','homeScore',
#                    'penaltySeverity','penaltyType','duration','committerId','drawerId']
# df_plays.to_csv("nhl_plays".csv", index=False)

# print(df_plays.head())


####################################################### ROSTERS ########################################################

# gameID = []; teamID = []; playerID = []; firstName = []; lastName = []; sweaterNumber = []
# positionCode = []; headshotURLs = []

# for game_id in gameID_list[:-70000]:
#     game_url = base_url + str(game_id) + suffix

#     try:
#         response = requests.head(game_url, allow_redirects=True)
#         if response.status_code == 404:
#             print("Page not found (404).")
#         else:
#             data = requests.get(game_url).json()
    
#             if not data['rosterSpots']:
#                 gameID.append(np.nan)
#                 teamID.append(np.nan)
#                 playerID.append(np.nan)
#                 firstName.append(np.nan)
#                 lastName.append(np.nan)
#                 sweaterNumber.append(np.nan)
#                 positionCode.append(np.nan)
#                 headshotURLs.append(np.nan)

#             else:
#                 for player in data['rosterSpots']:

#                     gameID.append(game_id)

#                     roster_keys = [
#                         ('teamId', teamID),
#                         ('playerId', playerID),
#                         ('sweaterNumber', sweaterNumber),
#                         ('positionCode', positionCode),
#                         ('headshot', headshotURLs)
#                     ]

#                     for key, target_list in roster_keys:
#                         target_list.append(player.get(key, None))

#                     if 'default' in player['firstName']:
#                         firstName.append(player['firstName']['default'])
#                     else:
#                         firstName.append(None)
#                     if 'default' in player['lastName']:
#                         lastName.append(player['lastName']['default'])
#                     else:
#                         lastName.append(None)

#     except requests.RequestException as e:
#         print("Error checking URL:", e)

# df_roster = pd.DataFrame([gameID, teamID, playerID, firstName, lastName, sweaterNumber, positionCode, headshotURLs]).transpose()
# df_roster.columns = ['Game ID', 'Team ID', 'Player ID', 'First Name', 'Last Name', 'Number', 'Position', 'Headshot']
# df_roster.to_csv("nhl_rosters.csv", index=False)
# print(df_roster.head())


################################################### PLAYER DETAILS ########################################################

# need to get distinct list of all player IDs from rosters - export w SQL from DB

### Define URL setup ###
player_base_url = "https://api-web.nhle.com/v1/player/" 
player_suffix = "/landing"

playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal Website/public_html/resources/data/playerID_list.csv")
playerID_list = playerID_list['playerID'].to_list() # convert from df to list for looping

### Create lists for player information ###
playerId = []; isActive = []; currentTeamId = []; currentTeamAbbrev = []; fullTeamName = []; teamCommonName = []; teamPlaceNameWithPreposition = []
firstName = []; lastName = []; badgesLogos = []; badgesNames = []; teamLogo = []; sweaterNumber = []; position = []; headshot = []; heroImage = []
heightInInches = []; heightInCentimeters = []; weightInPounds = []; weightInKilograms = []; birthDate = []; birthCity = []; birthStateProvince = []
birthCountry = []; shootsCatches = []; draftYear = []; draftTeam = []; draftRound = []; draftPickInRound = []; draftOverall = []; playerSlug = []
inTop100AllTime = []; inHHOF = []; featuredSeason = []; featuredSeasonStats = []; featuredSeasonAssists = []; featuredSeasonGWG = []; featuredSeasonGP = []
featuredSeasonGoals = []; featuredSeasonOTGoals = []; featuredSeasonPIM = []; featuredSeasonPlusMinus = []; featuredSeasonPts = []; featuredSeasonPPG = []
featuredSeasonPPPoints = []; featuredSeasonShootingPct = []; featuredSeasonSHG = []; featuredSeasonSHPts = []; featuredSeasonShots = []; regSeasonCareer = []
regSeasonCareerAssists = []; regSeasonCareerGWG = []; regSeasonCareerGP = []; regSeasonCareerGoals = []; regSeasonCareerOTGoals = []; regSeasonCareerPIM = []
regSeasonCareerPlusMinus = []; regSeasonCareerPts = []; regSeasonCareerPPG = []; regSeasonCareerPPPoints = []; regSeasonCareerShootingPct = []
regSeasonCareerSHG = []; regSeasonCareerSHPts = []; regSeasonCareerShots = []; playoffsCareer = []; playoffsCareerAssists = []; playoffsCareerGWG = []
playoffsCareerGP = []; playoffsCareerGoals = []; playoffsCareerOTGoals = []; playoffsCareerPIM = []; playoffsCareerPlusMinus = []; playoffsCareerPts = []
playoffsCareerPPG = []; playoffsCareerPPPoints = []; playoffsCareerShootingPct =[]; playoffsCareerSHG = []; playoffsCareerSHPts = []; playoffsCareerShots = []
shopLink = []; twitterLink = []; watchLink = []; last5Games = []; seasonTotals = []; awardNames = []; awardSeasons = []; currentTeamRoster = []
featuredSeasonGAA = []; featuredSeasonLosses = []; featuredSeasonSO = []; featuredSeasonTies = []; featuredSeasonWins = []; featuredSeasonGS = []
featuredSeasonGA = []; featuredSeasonTOI = []; regSeasonCareerGAA = []; regSeasonCareerLosses = []; regSeasonCareerSO = []; regSeasonCareerTies = []
regSeasonCareerWins = []; regSeasonCareerGS = []; regSeasonCareerGA = []; regSeasonCareerTOI = []; playoffsCareerGAA = []; playoffsCareerLosses = []
playoffsCareerSO = []; playoffsCareerTies = []; playoffsCareerWins = []; playoffsCareerGS = []; playoffsCareerGA = []; playoffsCareerTOI = []
featuredSeasonSavePct = []; featuredSeasonOTLosses = []; featuredSeasonShotsAgainst = []; regSeasonCareerSavePct = []; regSeasonCareerOTLosses = []
regSeasonCareerShotsAgainst = []; playoffsCareerSavePct = []; playoffsCareerOTLosses = []; playoffsCareerShotsAgainst = []

# ### Iterate through player IDs, get info for each player, append to lists for df creation later ###
# test_list = ['8474550']
# print(len(playerID_list))
count = 0
for playerID in playerID_list[-4000:-3800]:
    count += 1
    if count % 25 == 0:
        print(f"Processed {count} players.")
    # time.sleep(random.uniform(0.3, 0.7))

    player_url = player_base_url + str(playerID) + player_suffix
    try:
        response = requests.head(player_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).", playerID)
            continue
        else:
            player_data = requests.get(player_url).json()

            # print(player_data)

        # Basic Info
        if 'currentTeamId' in player_data:
            currentTeamId.append(player_data['currentTeamId'])
            currentTeamAbbrev.append(player_data['currentTeamAbbrev'])
            fullTeamName.append(player_data['fullTeamName']['default'])
            teamCommonName.append(player_data['teamCommonName']['default'])
            teamPlaceNameWithPreposition.append(player_data['teamPlaceNameWithPreposition']['default'])
        else:
            currentTeamId.append(None)
            currentTeamAbbrev.append(None)
            fullTeamName.append(None)
            teamCommonName.append(None)
            teamPlaceNameWithPreposition.append(None)
        
        firstName.append(player_data['firstName']['default'])
        lastName.append(player_data['lastName']['default'])
        if 'badges' in player_data and player_data['badges']: # check if badges key exists and makes sure that list is not empty
            badgesLogos.append(player_data['badges'][0]['logoUrl']['default'])
            badgesNames.append(player_data['badges'][0]['title']['default'])
        else:
            badgesLogos.append(None)
            badgesNames.append(None)


        basic_info_keys = [
            ('playerId', playerId),
            ('isActive', isActive),
            ('teamLogo', teamLogo),
            ('sweaterNumber', sweaterNumber),
            ('position', position),
            ('headshot', headshot),
            ('heroImage', heroImage),
            ('heightInInches', heightInInches),
            ('heightInCentimeters', heightInCentimeters),
            ('weightInPounds', weightInPounds),
            ('weightInKilograms', weightInKilograms),
            ('birthDate', birthDate),
            ('birthCountry', birthCountry),
            ('shootsCatches', shootsCatches),
            ('playerSlug', playerSlug),
            ('inTop100AllTime', inTop100AllTime),
            ('inHHOF', inHHOF)
        ]

        # iterate through tuples of key and list, get value for key, append to list
        for key, target_list in basic_info_keys:
            target_list.append(player_data.get(key, None))

        if 'birthCity' in player_data:
            birthCity.append(player_data['birthCity']['default'])
        else:
            birthCity.append(None)
        if 'birthStateProvince' in player_data:
            birthStateProvince.append(player_data['birthStateProvince']['default'])
        else:
            birthStateProvince.append(None)

        # Draft
        if 'draftDetails' in player_data:
            draftYear.append(player_data['draftDetails']['year'])
            draftTeam.append(player_data['draftDetails']['teamAbbrev'])
            draftRound.append(player_data['draftDetails']['round'])
            draftPickInRound.append(player_data['draftDetails']['pickInRound'])
            draftOverall.append(player_data['draftDetails']['overallPick'])
        else:
            draftYear.append(None)
            draftTeam.append(None)
            draftRound.append(None)
            draftPickInRound.append(None)
            draftOverall.append(None)

        # Featured Season
        if 'featuredStats' in player_data and 'regularSeason' in player_data['featuredStats']:
            featuredSeason.append(player_data['featuredStats']['season'])

            featuredSeasonStats = player_data['featuredStats']['regularSeason']['subSeason']
            
            # list tuples of key names and target lists to append to - allows looping to check if key
            # exists and appends to list if so - no need to repeat if/else blocks for every key
            featured_season_keys = [
                ('assists', featuredSeasonAssists),
                ('gameWinningGoals', featuredSeasonGWG),
                ('gamesPlayed', featuredSeasonGP),
                ('goals', featuredSeasonGoals),
                ('otGoals', featuredSeasonOTGoals),
                ('pim', featuredSeasonPIM),
                ('plusMinus', featuredSeasonPlusMinus),
                ('points', featuredSeasonPts),
                ('powerPlayGoals', featuredSeasonPPG),
                ('powerPlayPoints', featuredSeasonPPPoints),
                ('shootingPctg', featuredSeasonShootingPct),
                ('shorthandedGoals', featuredSeasonSHG),
                ('shorthandedPoints', featuredSeasonSHPts),
                ('shots', featuredSeasonShots),
                ('timeOnIce', featuredSeasonTOI),
                # goalie
                ('goalsAgainstAvg', featuredSeasonGAA),
                ('losses', featuredSeasonLosses),
                ('shutouts', featuredSeasonSO),
                ('ties', featuredSeasonTies),
                ('wins', featuredSeasonWins),
                ('gamesStarted', featuredSeasonGS),
                ('goalsAgainst', featuredSeasonGA),
                ('savePctg', featuredSeasonSavePct),
                ('OTlosses', featuredSeasonOTLosses),
                ('shotsAgainst', featuredSeasonShotsAgainst)
            ]

            # iterate through tuples of key and list, get value for key, append to list
            for key, target_list in featured_season_keys:
                target_list.append(featuredSeasonStats.get(key, None))
        else:
            featuredSeason.append(None)
            featuredSeasonAssists.append(None)
            featuredSeasonGWG.append(None)
            featuredSeasonGP.append(None)
            featuredSeasonGoals.append(None)
            featuredSeasonOTGoals.append(None)
            featuredSeasonPIM.append(None)
            featuredSeasonPlusMinus.append(None)
            featuredSeasonPts.append(None)
            featuredSeasonPPG.append(None)
            featuredSeasonPPPoints.append(None)
            featuredSeasonShootingPct.append(None)
            featuredSeasonSHG.append(None)
            featuredSeasonSHPts.append(None)
            featuredSeasonShots.append(None)

        ### Career Totals - Regular and Postseason ###
        if 'careerTotals' in player_data:
            # Regular Season
            if 'regularSeason' in player_data['careerTotals']:
                regSeasonCareer = player_data['careerTotals']['regularSeason']
            else:
                regSeasonCareer = {}

                reg_season_career_keys = [
                    ('assists', regSeasonCareerAssists),
                    ('gameWinningGoals', regSeasonCareerGWG),
                    ('gamesPlayed', regSeasonCareerGP),
                    ('goals', regSeasonCareerGoals),
                    ('otGoals', regSeasonCareerOTGoals),
                    ('pim', regSeasonCareerPIM),
                    ('plusMinus', regSeasonCareerPlusMinus),
                    ('points', regSeasonCareerPts),
                    ('powerPlayGoals', regSeasonCareerPPG),
                    ('powerPlayPoints', regSeasonCareerPPPoints),
                    ('shootingPctg', regSeasonCareerShootingPct),
                    ('shorthandedGoals', regSeasonCareerSHG),
                    ('shorthandedPoints', regSeasonCareerSHPts),
                    ('shots', regSeasonCareerShots),
                    ('timeOnIce', regSeasonCareerTOI),
                    # goalie
                    ('goalsAgainstAvg', regSeasonCareerGAA),
                    ('losses', regSeasonCareerLosses),
                    ('shutouts', regSeasonCareerSO),
                    ('ties', regSeasonCareerTies),
                    ('wins', regSeasonCareerWins),
                    ('gamesStarted', regSeasonCareerGS),
                    ('goalsAgainst', regSeasonCareerGA),
                    ('savePctg', regSeasonCareerSavePct),
                    ('OTlosses', regSeasonCareerOTLosses),
                    ('shotsAgainst', regSeasonCareerShotsAgainst)
                ]

                # iterate through tuples of key and list, get value for key, append to list
                for key, target_list in reg_season_career_keys:
                    target_list.append(regSeasonCareer.get(key, None))

            # Playoffs
            if 'playoffs' in player_data['careerTotals']:
                playoffsCareer = player_data['careerTotals']['playoffs']

                playoffs_career_keys = [
                    ('assists', playoffsCareerAssists),
                    ('gameWinningGoals', playoffsCareerGWG),
                    ('gamesPlayed', playoffsCareerGP),
                    ('goals', playoffsCareerGoals),
                    ('otGoals', playoffsCareerOTGoals),
                    ('pim', playoffsCareerPIM),
                    ('plusMinus', playoffsCareerPlusMinus),
                    ('points', playoffsCareerPts),
                    ('powerPlayGoals', playoffsCareerPPG),
                    ('powerPlayPoints', playoffsCareerPPPoints),
                    ('shootingPctg', playoffsCareerShootingPct),
                    ('shorthandedGoals', playoffsCareerSHG),
                    ('shorthandedPoints', playoffsCareerSHPts),
                    ('shots', playoffsCareerShots),
                    ('timeOnIce', playoffsCareerTOI),
                    # goalie
                    ('goalsAgainstAvg', playoffsCareerGAA),
                    ('losses', playoffsCareerLosses),
                    ('shutouts', playoffsCareerSO),
                    ('ties', playoffsCareerTies),
                    ('wins', playoffsCareerWins),
                    ('gamesStarted', playoffsCareerGS),
                    ('goalsAgainst', playoffsCareerGA),
                    ('savePctg', playoffsCareerSavePct),
                    ('OTlosses', playoffsCareerOTLosses),
                    ('shotsAgainst', playoffsCareerShotsAgainst)
                ]

                # iterate through tuples of key and list, get value for key, append to list
                for key, target_list in playoffs_career_keys:
                    target_list.append(playoffsCareer.get(key, None))
            else:
                playoffsCareerAssists.append(None)
                playoffsCareerGWG.append(None)
                playoffsCareerGP.append(None)
                playoffsCareerGoals.append(None)
                playoffsCareerOTGoals.append(None)
                playoffsCareerPIM.append(None)
                playoffsCareerPlusMinus.append(None)
                playoffsCareerPts.append(None)
                playoffsCareerPPG.append(None)
                playoffsCareerPPPoints.append(None)
                playoffsCareerShootingPct.append(None)
                playoffsCareerSHG.append(None)
                playoffsCareerSHPts.append(None)
                playoffsCareerShots.append(None)
        else:
            regSeasonCareerAssists.append(None)
            regSeasonCareerGWG.append(None)
            regSeasonCareerGP.append(None)
            regSeasonCareerGoals.append(None)
            regSeasonCareerOTGoals.append(None)
            regSeasonCareerPIM.append(None)
            regSeasonCareerPlusMinus.append(None)
            regSeasonCareerPts.append(None)
            regSeasonCareerPPG.append(None)
            regSeasonCareerPPPoints.append(None)
            regSeasonCareerShootingPct.append(None)
            regSeasonCareerSHG.append(None)
            regSeasonCareerSHPts.append(None)
            regSeasonCareerShots.append(None)

        # Other
        shopLink.append(player_data['shopLink'])
        twitterLink.append(player_data['twitterLink'])
        watchLink.append(player_data['watchLink'])
        if 'last5Games' in player_data:
            last5Games.append(player_data['last5Games'])
        else:
            last5Games.append(None)
        
        # Season-by-Season Stats
        seasonTotals.append(player_data['seasonTotals'])


        if 'awards' in player_data:
            awards = player_data['awards']

            # instantiate - for the individual player, list of award names, list of lists of corresponding years for each award
            player_awardNames = []
            player_awardSeasons = []

            # iterate through each award for the player
            for award in awards:
                # add trophy name to player award names list
                player_awardNames.append(award['trophy']['default'])
                # create a new list to track seasons won for that trophy - iterate through seasons and append their ID to this list
                seasonsWon = []
                for season in award['seasons']:
                    seasonsWon.append(season['seasonId'])
                # append the list of years for this specific award to the overall player's list of lists for awards
                player_awardSeasons.append(seasonsWon)
                
            # append award name and season lists for this specific player to the overall lists
            awardNames.append(player_awardNames)
            awardSeasons.append(player_awardSeasons)

        else:
            awardNames.append(None)
            awardSeasons.append(None)
        if 'currentTeamRoster' in player_data:
            currentTeamRoster.append(player_data['currentTeamRoster'])
        else:
            currentTeamRoster.append(None)

    except requests.RequestException as e:
        print(f"Error fetching JSON for player {playerID}: {e}")
        continue


## Create, save, and view df ###
df_players = pd.DataFrame([playerId, isActive, currentTeamId, currentTeamAbbrev, fullTeamName, teamCommonName, teamPlaceNameWithPreposition,
                            firstName, lastName, badgesLogos, badgesNames, teamLogo, sweaterNumber, position, headshot, heroImage, heightInInches, 
                            heightInCentimeters, weightInPounds, weightInKilograms, birthDate, birthCity, birthStateProvince, birthCountry, shootsCatches,
                            draftYear, draftTeam, draftRound, draftPickInRound, draftOverall, playerSlug, inTop100AllTime, inHHOF, featuredSeason, featuredSeasonAssists,
                            featuredSeasonGWG, featuredSeasonGP, featuredSeasonGoals, featuredSeasonOTGoals, featuredSeasonPIM, featuredSeasonPlusMinus,
                            featuredSeasonPts, featuredSeasonPPG, featuredSeasonPPPoints, featuredSeasonShootingPct, featuredSeasonSHG, featuredSeasonSHPts,
                            featuredSeasonShots, regSeasonCareerAssists, regSeasonCareerGWG, regSeasonCareerGP, regSeasonCareerGoals, regSeasonCareerOTGoals,
                            regSeasonCareerPIM, regSeasonCareerPlusMinus, regSeasonCareerPts, regSeasonCareerPPG, regSeasonCareerPPPoints,
                            regSeasonCareerShootingPct, regSeasonCareerSHG, regSeasonCareerSHPts, regSeasonCareerShots, playoffsCareerAssists,
                            playoffsCareerGWG, playoffsCareerGP, playoffsCareerGoals, playoffsCareerOTGoals, playoffsCareerPIM, playoffsCareerPlusMinus,
                            playoffsCareerPts, playoffsCareerPPG, playoffsCareerPPPoints, playoffsCareerShootingPct, playoffsCareerSHG, playoffsCareerSHPts,
                            playoffsCareerShots, shopLink, twitterLink, watchLink, last5Games, seasonTotals, awardNames, awardSeasons, currentTeamRoster,
                            featuredSeasonGAA, featuredSeasonLosses, featuredSeasonSO, featuredSeasonTies, featuredSeasonWins, featuredSeasonGS,
                            featuredSeasonGA, featuredSeasonTOI, regSeasonCareerGAA, regSeasonCareerLosses, regSeasonCareerSO, regSeasonCareerTies,
                            regSeasonCareerWins, regSeasonCareerGS, regSeasonCareerGA, regSeasonCareerTOI, playoffsCareerGAA, playoffsCareerLosses,
                            playoffsCareerSO, playoffsCareerTies, playoffsCareerWins, playoffsCareerGS, playoffsCareerGA, playoffsCareerTOI,
                            featuredSeasonSavePct, featuredSeasonOTLosses, featuredSeasonShotsAgainst, regSeasonCareerSavePct, regSeasonCareerOTLosses,
                            regSeasonCareerShotsAgainst, playoffsCareerSavePct, playoffsCareerOTLosses, playoffsCareerShotsAgainst]
                            ).transpose()


df_players.columns = ['playerId', 'isActive', 'currentTeamId', 'currentTeamAbbrev', 'fullTeamName', 'teamCommonName', 'teamPlaceNameWithPreposition',
                        'firstName', 'lastName', 'badgesLogos','badgesNames','teamLogo', 'sweaterNumber', 'position', 'headshot', 'heroImage',
                        'heightInInches', 'heightInCentimeters', 'weightInPounds', 'weightInKilograms', 'birthDate', 'birthCity', 'birthStateProvince', 
                        'birthCountry', 'shootsCatches', 'draftYear', 'draftTeam', 'draftRound', 'draftPickInRound', 'draftOverall','playerSlug', 
                        'inTop100AllTime', 'inHHOF','featuredSeason','featuredSeasonAssists','featuredSeasonGWG','featuredSeasonGP', 'featuredSeasonGoals',
                        'featuredSeasonOTGoals', 'featuredSeasonPIM', 'featuredSeasonPlusMinus', 'featuredSeasonPts', 'featuredSeasonPPG',
                        'featuredSeasonPPPoints', 'featuredSeasonShootingPct', 'featuredSeasonSHG', 'featuredSeasonSHPts', 'featuredSeasonShots',
                        'regSeasonCareerAssists', 'regSeasonCareerGWG', 'regSeasonCareerGP', 'regSeasonCareerGoals', 'regSeasonCareerOTGoals',
                        'regSeasonCareerPIM', 'regSeasonCareerPlusMinus', 'regSeasonCareerPts', 'regSeasonCareerPPG', 'regSeasonCareerPPPoints',
                        'regSeasonCareerShootingPct', 'regSeasonCareerSHG','regSeasonCareerSHPts','regSeasonCareerShots', 'playoffsCareerAssists',
                        'playoffsCareerGWG', 'playoffsCareerGP', 'playoffsCareerGoals', 'playoffsCareerOTGoals', 'playoffsCareerPIM',
                        'playoffsCareerPlusMinus', 'playoffsCareerPts', 'playoffsCareerPPG', 'playoffsCareerPPPoints', 'playoffsCareerShootingPct',
                        'playoffsCareerSHG', 'playoffsCareerSHPts', 'playoffsCareerShots', 'shopLink', 'twitterLink', 'watchLink', 'last5Games',
                        'seasonTotals', 'awardNames', 'awardSeasons','currentTeamRoster', 'featuredSeasonGAA', 'featuredSeasonLosses',
                        'featuredSeasonSO', 'featuredSeasonTies', 'featuredSeasonWins', 'featuredSeasonGS', 'featuredSeasonGA',
                        'featuredSeasonTOI', 'regSeasonCareerGAA', 'regSeasonCareerLosses', 'regSeasonCareerSO', 'regSeasonCareerTies',
                        'regSeasonCareerWins', 'regSeasonCareerGS', 'regSeasonCareerGA', 'regSeasonCareerTOI',
                        'playoffsCareerGAA', 'playoffsCareerLosses', 'playoffsCareerSO', 'playoffsCareerTies', 'playoffsCareerWins',
                        'playoffsCareerGS', 'playoffsCareerGA', 'playoffsCareerTOI', 'featuredSeasonSavePct', 'featuredSeasonOTLosses',
                        'featuredSeasonShotsAgainst', 'regSeasonCareerSavePct', 'regSeasonCareerOTLosses',
                        'regSeasonCareerShotsAgainst', 'playoffsCareerSavePct', 'playoffsCareerOTLosses', 'playoffsCareerShotsAgainst']

# print("Row counts in each list:")
# print([(key, len(val)) for key, val in locals().items() if isinstance(val, list)])

df_players.to_csv("nhl_players.csv", index=False)
print(df_players.head())




##################################### SEASON-BY-SEASON STATS FOR EACH PLAYER ################################

### Iterates through player IDs, accesses seasonTotals field for player, expands this field out into a full
### table of stats for each season for that player

# Define URL setup #
# player_base_url = "https://api-web.nhle.com/v1/player/" 
# player_suffix = "/landing"

# playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal Website/data/playerID_list.csv")
# playerID_list = playerID_list[1:] # temp, remove 0 row before export
# playerID_list = playerID_list['playerID'].to_list() # convert from df to list for looping

# playerId = []
# seasonAssists = []
# seasonGameTypeId = []
# seasonGamesPlayed = []
# seasonGoals = []
# seasonLeagueAbbrev = []
# seasonPIM = []
# seasonPoints = []
# seasonSeason = []
# seasonSequence = []
# seasonTeamName = []

# # Iterate through player IDs, get info for each player, append to lists for df creation later #
# for playerID in playerID_list[2000:2500]:
#     player_url = player_base_url + str(playerID) + player_suffix
#     try:
#         response = requests.head(player_url, allow_redirects=True)
#         if response.status_code == 404:
#             print("Page not found (404).")
#         else:
#             player_data = requests.get(player_url).json()
#             season_data = player_data['seasonTotals'] # set variable to seasonTotals field list for that player

#             # iterate through seasons for that player, store stats for each season
#             for season in season_data:
#                 player_season_keys = [
#                     ('assists', seasonAssists),
#                     ('gameTypeId', seasonGameTypeId),
#                     ('gamesPlayed', seasonGamesPlayed),
#                     ('goals', seasonGoals),
#                     ('leagueAbbrev', seasonLeagueAbbrev),
#                     ('pim', seasonPIM),
#                     ('points', seasonPoints),
#                     ('season', seasonSeason),
#                     ('sequence', seasonSequence)
#                 ]

#                 # iterate through tuples of key and list, get value for key, append to list
#                 for key, target_list in player_season_keys:
#                     target_list.append(season.get(key, None))

#                 playerId.append(playerID)
                
#                 seasonTeamName.append(season['teamName']['default'])

#     except requests.RequestException as e:
#         print("Error checking URL:", e)

# # Build df and use results
# df_seasons_data = pd.DataFrame([playerId, seasonAssists, seasonGameTypeId, seasonGamesPlayed,
#                                     seasonGoals, seasonLeagueAbbrev, seasonPIM, seasonPoints, seasonSeason,
#                                     seasonSequence, seasonTeamName]).transpose()
# df_seasons_data.columns = ['playerId', 'seasonAssists', 'seasonGameTypeId', 'seasonGamesPlayed',
#                                'seasonGoals', 'seasonLeagueAbbrev', 'seasonPIM', 'seasonPoints', 'seasonSeason',
#                                'seasonSequence', 'seasonTeamName']
    
# print(df_seasons_data.head())
# df_seasons_data.to_csv("season_stats.csv", index=False)

