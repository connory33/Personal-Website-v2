import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np

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
# id = []
# season = []
# gameType = []
# gameNumber = []
# gameDate = []
# easternStartTime = []
# gameStateId = [] 
# venue = []
# venueLocation = []
# awayTeamId = []
# awayTeamName = []
# awayScore = []
# awayShots = []
# awayLogo = []
# homeTeamId = []
# homeTeamName = []
# homeScore = []
# homeShots = []
# homeLogo = []
# shootoutInUse = []
# otInUse = []
# gameOutcome = []
# regPeriods = []

# print(len(gameID_list))

# for game_id in gameID_list[50000:]:
#     # print(game_id)
#     # set game code to current game and pull JSON
#     game_code = str(game_id)
#     url = base_url+game_code+suffix
#     # print(url)
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
# df_games.to_csv("gamesv2.csv", index=False)


################################################ PLAY-BY-PLAY + ADDL. GAME DETAILS ###################################################

# gameID = []
# eventID = []
# period = []
# timeInPeriod = []
# timeRemaining = []
# situationCode = []
# typeCode = []
# typeDescKey = []   
# xCoord = []
# yCoord = []
# zoneCode = []
# eventOwnerTeamId = []
# faceoffLoserId = []
# faceoffWinnerId = []
# hittingPlayerId = []
# hitteePlayerId = []
# shotType = []
# shootingPlayerId = []
# goalieInNetId = []
# awaySOG = []
# homeSOG = []
# reason = []
# takeawayGiveawayPlayerId = []
# blockingPlayerId = []
# scoringPlayerId = []
# assist1PlayerId = []
# assist1PlayerTotal = []
# awayScore = []
# homeScore = []
# penaltySeverity = []
# penaltyType = []
# duration = []
# committerId = []
# drawerId = []

# count = 0

# # print(data)

# for game_id in gameID_list[-1000:-100]: # ['2022030117']:
#     game_url = base_url + str(game_id) + suffix
#     if requests.get(game_url):
#         data = requests.get(game_url).json()
#     else:
#         continue
    
#     if not data['plays']: # checks to see if plays list is empty
#         gameID.append(data['id'])
#         eventID.append('No data available')
#         period.append('No data available')
#         timeInPeriod.append('No data available')
#         timeRemaining.append('No data available')
#         situationCode.append('No data available')
#         typeCode.append('No data available')
#         typeDescKey.append('No data available')
#         xCoord.append('No data available')
#         yCoord.append('No data available')
#         zoneCode.append('No data available')
#         eventOwnerTeamId.append('No data available')
#         faceoffLoserId.append('No data available')
#         faceoffWinnerId.append('No data available')
#         hittingPlayerId.append('No data available')
#         hitteePlayerId.append('No data available')
#         shotType.append('No data available')
#         shootingPlayerId.append('No data available')
#         goalieInNetId.append('No data available')
#         awaySOG.append('No data available')
#         homeSOG.append('No data available')
#         reason.append('No data available')
#         takeawayGiveawayPlayerId.append('No data available')
#         blockingPlayerId.append('No data available')
#         scoringPlayerId.append('No data available')
#         assist1PlayerId.append('No data available')
#         assist1PlayerTotal.append('No data available')
#         awayScore.append('No data available')
#         homeScore.append('No data available')
#         penaltySeverity.append('No data available')
#         penaltyType.append('No data available')
#         duration.append('No data available')
#         committerId.append('No data available')
#         drawerId.append('No data available')

#     else:
#         for play in data['plays']:
#             # print(data['plays'])
#             # add game ID to each row for joining
#             gameID.append(data['id'])

#             # capture basic info that is always present for every play entry
#             eventID.append(play['eventId'])
#             period.append(play['periodDescriptor']['number'])
#             timeInPeriod.append(play['timeInPeriod'])
#             timeRemaining.append(play['timeRemaining'])
#             typeCode.append(play['typeCode'])
#             typeKey = play['typeDescKey']
#             typeDescKey.append(typeKey)
#             if 'situationCode' in play:
#                 situationCode.append(play['situationCode'])
#             else:
#                 situationCode.append(None)

#             # move on if there are no additional details to capture about the play (e.g. period end)
#             if 'details' not in play:
#                 xCoord.append(None)
#                 yCoord.append(None)
#                 zoneCode.append(None)
#                 eventOwnerTeamId.append(None)
#                 faceoffLoserId.append(None)
#                 faceoffWinnerId.append(None)
#                 hittingPlayerId.append(None)
#                 hitteePlayerId.append(None)
#                 shotType.append(None)
#                 shootingPlayerId.append(None)
#                 goalieInNetId.append(None)
#                 awaySOG.append(None)
#                 homeSOG.append(None)
#                 reason.append(None)
#                 takeawayGiveawayPlayerId.append(None)
#                 blockingPlayerId.append(None)
#                 scoringPlayerId.append(None)
#                 assist1PlayerId.append(None)
#                 assist1PlayerTotal.append(None)
#                 awayScore.append(None)
#                 homeScore.append(None)
#                 penaltySeverity.append(None)
#                 penaltyType.append(None)
#                 duration.append(None)
#                 committerId.append(None)
#                 drawerId.append(None)
#             else:
#                 # otherwise, first capture all the general fields that are present for every details field (if existing)
#                 if 'xCoord' in play['details'] and 'yCoord' in play['details']:
#                     xCoord.append(play['details']['xCoord'])
#                     yCoord.append(play['details']['yCoord'])
#                 else:
#                     xCoord.append(None)
#                     yCoord.append(None)
                    
#                 if 'zoneCode' in play['details']:
#                     zoneCode.append(play['details']['zoneCode'])
#                 else:
#                     zoneCode.append(None)

#                 if 'eventOwnerTeamId' in play['details']:
#                     eventOwnerTeamId.append(play['details']['eventOwnerTeamId'])
#                 else:
#                     eventOwnerTeamId.append(None)

#                 # now, capture fields that are filled or not dependent on play type

#                 # Faceoff
#                 if typeKey == 'faceoff':
#                     faceoffLoserId.append(play['details']['losingPlayerId'])
#                     faceoffWinnerId.append(play['details']['winningPlayerId'])
#                     hittingPlayerId.append(None)
#                     hitteePlayerId.append(None)
#                     shotType.append(None)
#                     shootingPlayerId.append(None)
#                     goalieInNetId.append(None)
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(None)
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)

#                 # Hits
#                 elif typeKey == 'hit':
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(play['details']['hittingPlayerId']) 
#                     hitteePlayerId.append(play['details']['hitteePlayerId'])
#                     shotType.append(None)
#                     shootingPlayerId.append(None)
#                     goalieInNetId.append(None)
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(None)
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)

#                 # Missed Shots
#                 elif typeKey == 'missed-shot':
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(None) 
#                     hitteePlayerId.append(None)
#                     shotType.append(play['details']['shotType'])
#                     shootingPlayerId.append(play['details']['shootingPlayerId'])
#                     if 'goalieInNetId' in play['details']:
#                         goalieInNetId.append(play['details']['goalieInNetId'])
#                     else:
#                         goalieInNetId.append('Not listed')
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(play['details']['reason'])
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)
                
#                 # Shots on Goal
#                 elif typeKey == 'shot-on-goal':
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(None) 
#                     hitteePlayerId.append(None)
#                     shotType.append(play['details']['shotType'])
#                     shootingPlayerId.append(play['details']['shootingPlayerId'])
#                     if 'goalieInNetId' in play['details']:
#                         goalieInNetId.append(play['details']['goalieInNetId'])
#                     else:
#                         goalieInNetId.append('Not listed')
#                     awaySOG.append(play['details']['awaySOG'])
#                     homeSOG.append(play['details']['homeSOG'])
#                     reason.append(None)
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)

#                 # Stoppages
#                 elif typeKey == 'stoppage':
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(None) 
#                     hitteePlayerId.append(None)
#                     shotType.append(None)
#                     shootingPlayerId.append(None)
#                     goalieInNetId.append(None)
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(play['details']['reason'])
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)

#                 # Takeaway or Giveaway
#                 elif typeKey in ['takeaway', 'giveaway']:
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(None) 
#                     hitteePlayerId.append(None)
#                     shotType.append(None)
#                     shootingPlayerId.append(None)
#                     goalieInNetId.append(None)
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(None)
#                     takeawayGiveawayPlayerId.append(play['details']['playerId'])
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)

#                 # Blocked Shot
#                 elif typeKey == 'blocked-shot':
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(None) 
#                     hitteePlayerId.append(None)
#                     shotType.append(None)
#                     shootingPlayerId.append(play['details']['shootingPlayerId'])
#                     goalieInNetId.append(None)
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(None)
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(play['details']['blockingPlayerId'])
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)

#                 # Goal
#                 elif typeKey == 'goal':
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(None) 
#                     hitteePlayerId.append(None)
#                     if shotType in play['details']:
#                         shotType.append(play['details']['shotType'])
#                     else:
#                         shotType.append(None)
#                     shootingPlayerId.append(None)
#                     if 'goalieInNetId' in play['details']:
#                         goalieInNetId.append(play['details']['goalieInNetId'])
#                     else:
#                         goalieInNetId.append('Not listed')
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(None)
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(play['details']['scoringPlayerId'])
#                     if 'assist1PlayerId' in play['details']:
#                         assist1PlayerId.append(play['details']['assist1PlayerId'])
#                         assist1PlayerTotal.append(play['details']['assist1PlayerTotal'])
#                     else:
#                         assist1PlayerId.append('Not listed')
#                         assist1PlayerTotal.append('Not listed')
#                     awayScore.append(play['details']['awayScore'])
#                     homeScore.append(play['details']['homeScore'])
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)

#                 # Penalty
#                 elif typeKey == 'penalty':
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(None) 
#                     hitteePlayerId.append(None)
#                     shotType.append(None)
#                     shootingPlayerId.append(None)
#                     goalieInNetId.append(None)
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(None)
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(play['details']['typeCode'])
#                     penaltyType.append(play['details']['descKey'])
#                     duration.append(play['details']['duration'])
#                     if 'committedByPlayerId' in play['details']:
#                         committerId.append(play['details']['committedByPlayerId'])
#                     else:
#                         committerId.append('Not listed')
#                     if 'drawnByPlayerId' in play['details']:
#                         drawerId.append(play['details']['drawnByPlayerId'])
#                     else:
#                         drawerId.append('Not listed')

#                 else:
#                     faceoffLoserId.append(None)
#                     faceoffWinnerId.append(None)
#                     hittingPlayerId.append(None) 
#                     hitteePlayerId.append(None)
#                     shotType.append(None)
#                     shootingPlayerId.append(None)
#                     goalieInNetId.append(None)
#                     awaySOG.append(None)
#                     homeSOG.append(None)
#                     reason.append(None)
#                     takeawayGiveawayPlayerId.append(None)
#                     blockingPlayerId.append(None)
#                     scoringPlayerId.append(None)
#                     assist1PlayerId.append(None)
#                     assist1PlayerTotal.append(None)
#                     awayScore.append(None)
#                     homeScore.append(None)
#                     penaltySeverity.append(None)
#                     penaltyType.append(None)
#                     duration.append(None)
#                     committerId.append(None)
#                     drawerId.append(None)
                
    
#     count += 1

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
# df_plays.to_csv("plays_test.csv", index=False)

# print(df_plays.head())


####################################################### ROSTERS ########################################################

gameID = []
teamID = []
playerID = []
firstName = []
lastName = []
sweaterNumber = []
positionCode = []
headshotURLs = []

for game_id in gameID_list[-30000:-20000]: # ['2022030117']:
    game_url = base_url + str(game_id) + suffix

    try:
        response = requests.head(game_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            data = requests.get(game_url).json()
    
            if not data['rosterSpots']:
                gameID.append(np.nan)
                teamID.append(np.nan)
                playerID.append(np.nan)
                firstName.append(np.nan)
                lastName.append(np.nan)
                sweaterNumber.append(np.nan)
                positionCode.append(np.nan)
                headshotURLs.append(np.nan)

            else:
                for player in data['rosterSpots']:
                    if 'playerId' in player:
                        gameID.append(game_id)
                    else:
                        gameID.append(None)
                    if 'teamId' in player:
                        teamID.append(player['teamId'])
                    else:
                        teamID.append(None)
                    if 'playerId' in player:
                        playerID.append(player['playerId'])
                    else:
                        playerID.append(None)
                    if 'default' in player['firstName']:
                        firstName.append(player['firstName']['default'])
                    else:
                        firstName.append(None)
                    if 'default' in player['lastName']:
                        lastName.append(player['lastName']['default'])
                    else:
                        lastName.append(None)
                    if 'sweaterNumber' in player:
                        sweaterNumber.append(player['sweaterNumber'])
                    else:
                        sweaterNumber.append(None)
                    if 'positionCode' in player:
                        positionCode.append(player['positionCode'])
                    else:
                        positionCode.append(None)
                    if 'headshot' in player:
                        headshotURLs.append(player['headshot'])
                    else:
                        headshotURLs.append(None)
    except requests.RequestException as e:
        print("Error checking URL:", e)

df_roster = pd.DataFrame([gameID, teamID, playerID, firstName, lastName, sweaterNumber, positionCode, headshotURLs]).transpose()
df_roster.columns = ['Game ID', 'Team ID', 'Player ID', 'First Name', 'Last Name', 'Number', 'Position', 'Headshot']
df_roster.to_csv("roster_test.csv", index=False)
print(df_roster.head())


################################################### PLAYER DETAILS ########################################################

# need to get distinct list of all player IDs from rosters - export w SQL from DB

### Define URL setup ###
# player_base_url = "https://api-web.nhle.com/v1/player/" 
# player_suffix = "/landing"

# playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal Website/data/playerID_list.csv")
# playerID_list = playerID_list[1:] # temp, remove 0 row before export
# playerID_list = playerID_list['playerID'].to_list() # convert from df to list for looping

# ### Create lists for player information ###
# playerId = []; #isActive = []; currentTeamId = []; currentTeamAbbrev = []; fullTeamName = []; teamCommonName = []; teamPlaceNameWithPreposition = []
# # firstName = []; lastName = []; badgesLogos = []; badgesNames = []; teamLogo = []; sweaterNumber = []; position = []; headshot = []; heroImage = []
# # heightInInches = []; heightInCentimeters = []; weightInPounds = []; weightInKilograms = []; birthDate = []; birthCity = []; birthStateProvince = []
# # birthCountry = []; shootsCatches = []; draftYear = []; draftTeam = []; draftRound = []; draftPickInRound = []; draftOverall = []; playerSlug = []
# # inTop100AllTime = []; inHHOF = []; featuredSeason = []; featuredSeasonStats = []; featuredSeasonAssists = []; featuredSeasonGWG = []; featuredSeasonGP = []
# # featuredSeasonGoals = []; featuredSeasonOTGoals = []; featuredSeasonPIM = []; featuredSeasonPlusMinus = []; featuredSeasonPts = []; featuredSeasonPPG = []
# # featuredSeasonPPPoints = []; featuredSeasonShootingPct = []; featuredSeasonSHG = []; featuredSeasonSHPts = []; featuredSeasonShots = []; regSeasonCareer = []
# # regSeasonCareerAssists = []; regSeasonCareerGWG = []; regSeasonCareerGP = []; regSeasonCareerGoals = []; regSeasonCareerOTGoals = []; regSeasonCareerPIM = []
# # regSeasonCareerPlusMinus = []; regSeasonCareerPts = []; regSeasonCareerPPG = []; regSeasonCareerPPPoints = []; regSeasonCareerShootingPct = []
# # regSeasonCareerSHG = []; regSeasonCareerSHPts = []; regSeasonCareerShots = []; playoffsCareer = []; playoffsCareerAssists = []; playoffsCareerGWG = []
# # playoffsCareerGP = []; playoffsCareerGoals = []; playoffsCareerOTGoals = []; playoffsCareerPIM = []; playoffsCareerPlusMinus = []; playoffsCareerPts = []
# # playoffsCareerPPG = []; playoffsCareerPPPoints = []; playoffsCareerShootingPct =[]; playoffsCareerSHG = []; playoffsCareerSHPts = []; playoffsCareerShots = []
# # shopLink = []; twitterLink = []; watchLink = []; last5Games = []; seasonTotals = []; awardNames = []; awardSeasons = []; currentTeamRoster = []

# # ### Iterate through player IDs, get info for each player, append to lists for df creation later ###
# for playerID in playerID_list[:2500]:
#     player_url = player_base_url + str(playerID) + player_suffix
#     player_data = requests.get(player_url).json()
#     # print(playerID)

#     # Basic Info
#     playerId.append(player_data['playerId'])
#     isActive.append(player_data['isActive'])
#     if 'currentTeamId' in player_data:
#         currentTeamId.append(player_data['currentTeamId'])
#         currentTeamAbbrev.append(player_data['currentTeamAbbrev'])
#         fullTeamName.append(player_data['fullTeamName']['default'])
#         teamCommonName.append(player_data['teamCommonName']['default'])
#         teamPlaceNameWithPreposition.append(player_data['teamPlaceNameWithPreposition']['default'])
#     else:
#         currentTeamId.append(None)
#         currentTeamAbbrev.append(None)
#         fullTeamName.append(None)
#         teamCommonName.append(None)
#         teamPlaceNameWithPreposition.append(None)
    
#     firstName.append(player_data['firstName']['default'])
#     lastName.append(player_data['lastName']['default'])
#     if 'badges' in player_data and player_data['badges']: # check if badges key exists and makes sure that list is not empty
#         badgesLogos.append(player_data['badges'][0]['logoUrl']['default'])
#         badgesNames.append(player_data['badges'][0]['title']['default'])
#     else:
#         badgesLogos.append(None)
#         badgesNames.append(None)
#     if 'teamLogo' in player_data:
#         teamLogo.append(player_data['teamLogo'])
#     else:
#         teamLogo.append(None)
#     if 'sweaterNumber' in player_data:
#         sweaterNumber.append(player_data['sweaterNumber'])
#     else:
#         sweaterNumber.append(None)
#     if 'position' in player_data:
#         position.append(player_data['position'])
#     else:
#         position.append(None)
#     if 'headshot' in player_data:
#         headshot.append(player_data['headshot'])
#     else:
#         headshot.append(None)
#     if 'heroImage' in player_data:
#         heroImage.append(player_data['heroImage'])
#     else:
#         heroImage.append(None)
#     if 'heightInInches' in player_data:
#         heightInInches.append(player_data['heightInInches'])
#     else:
#         heightInInches.append(None)
#     if 'heighInCentimeters' in player_data:
#         heightInCentimeters.append(player_data['heightInCentimeters'])
#     else:
#         heightInCentimeters.append(None)
#     if 'weightInPounds' in player_data:
#         weightInPounds.append(player_data['weightInPounds'])
#     else:
#         weightInPounds.append(None)
#     if 'weightInKilograms' in player_data:
#         weightInKilograms.append(player_data['weightInKilograms'])
#     else:
#         weightInKilograms.append(None)
#     if 'birthDate' in player_data:
#         birthDate.append(player_data['birthDate'])
#     else:
#         birthDate.append(None)
#     if 'birthCity' in player_data:
#         birthCity.append(player_data['birthCity']['default'])
#     else:
#         birthCity.append(None)
#     if 'birthStateProvince' in player_data:
#         birthStateProvince.append(player_data['birthStateProvince']['default'])
#     else:
#         birthStateProvince.append(None)
#     if 'birthCountry' in player_data:
#         birthCountry.append(player_data['birthCountry'])
#     else:
#         birthCountry.append(None)
#     if 'shootsCatches' in player_data:
#         shootsCatches.append(player_data['shootsCatches'])
#     else:
#         shootsCatches.append(None)

#     # Draft
#     if 'draftDetails' in player_data:
#         draftYear.append(player_data['draftDetails']['year'])
#         draftTeam.append(player_data['draftDetails']['teamAbbrev'])
#         draftRound.append(player_data['draftDetails']['round'])
#         draftPickInRound.append(player_data['draftDetails']['pickInRound'])
#         draftOverall.append(player_data['draftDetails']['overallPick'])
#     else:
#         draftYear.append(None)
#         draftTeam.append(None)
#         draftRound.append(None)
#         draftPickInRound.append(None)
#         draftOverall.append(None)

#     playerSlug.append(player_data['playerSlug'])
#     if 'inTop100AllTime' in player_data:
#         inTop100AllTime.append(player_data['inTop100AllTime'])
#     else:
#         inTop100AllTime.append(None)
#     inHHOF.append(player_data['inHHOF'])

#     # Featured Season
#     if 'featuredStats' in player_data and 'regularSeason' in player_data['featuredStats']:
#         featuredSeason.append(player_data['featuredStats']['season'])
#         featuredSeasonStats = player_data['featuredStats']['regularSeason']['subSeason']
#         if 'assists' in featuredSeasonStats:
#             featuredSeasonAssists.append(featuredSeasonStats['assists'])
#         else:
#             featuredSeasonAssists.append(None)
#         if 'gameWinningGoals' in featuredSeasonStats:
#             featuredSeasonGWG.append(featuredSeasonStats['gameWinningGoals'])
#         else:
#             featuredSeasonGWG.append(None)
#         if 'gamesPlayed' in featuredSeasonStats:
#             featuredSeasonGP.append(featuredSeasonStats['gamesPlayed'])
#         else:
#             featuredSeasonGP.append(None)
#         if 'goals' in featuredSeasonStats:
#             featuredSeasonGoals.append(featuredSeasonStats['goals'])
#         else:
#             featuredSeasonGoals.append(None)
#         if 'otGoals' in featuredSeasonStats:
#             featuredSeasonOTGoals.append(featuredSeasonStats['otGoals'])
#         else:
#             featuredSeasonOTGoals.append(None)
#         if 'pim' in featuredSeasonStats:
#             featuredSeasonPIM.append(featuredSeasonStats['pim'])
#         else:
#             featuredSeasonPIM.append(None)
#         if 'plusMinus' in featuredSeasonStats:
#             featuredSeasonPlusMinus.append(featuredSeasonStats['plusMinus'])
#         else:
#             featuredSeasonPlusMinus.append(None)
#         if 'points' in featuredSeasonStats:
#             featuredSeasonPts.append(featuredSeasonStats['points'])
#         else:
#             featuredSeasonPts.append(None)
#         if 'powerPlayGoals' in featuredSeasonStats:
#             featuredSeasonPPG.append(featuredSeasonStats['powerPlayGoals'])
#         else:
#             featuredSeasonPPG.append(None)
#         if 'powerPlayPoints' in featuredSeasonStats:
#             featuredSeasonPPPoints.append(featuredSeasonStats['powerPlayPoints'])
#         else:
#             featuredSeasonPPPoints.append(None)
#         if 'shootingPctg' in featuredSeasonStats:
#             featuredSeasonShootingPct.append(featuredSeasonStats['shootingPctg'])
#         else:
#             featuredSeasonShootingPct.append(None)
#         if 'shorthandedGoals' in featuredSeasonStats:
#             featuredSeasonSHG.append(featuredSeasonStats['shorthandedGoals'])
#         else:
#             featuredSeasonSHG.append(None)
#         if 'shorthandedPoints' in featuredSeasonStats:
#             featuredSeasonSHPts.append(featuredSeasonStats['shorthandedPoints'])
#         else:
#             featuredSeasonSHPts.append(None)
#         if 'shots' in featuredSeasonStats:
#             featuredSeasonShots.append(featuredSeasonStats['shots'])
#         else:
#             featuredSeasonShots.append(None)
#     else:
#         featuredSeasonAssists.append(None)
#         featuredSeasonGWG.append(None)
#         featuredSeasonGP.append(None)
#         featuredSeasonGoals.append(None)
#         featuredSeasonOTGoals.append(None)
#         featuredSeasonPIM.append(None)
#         featuredSeasonPlusMinus.append(None)
#         featuredSeasonPts.append(None)
#         featuredSeasonPPG.append(None)
#         featuredSeasonPPPoints.append(None)
#         featuredSeasonShootingPct.append(None)
#         featuredSeasonSHG.append(None)
#         featuredSeasonSHPts.append(None)
#         featuredSeasonShots.append(None)

#     if 'careerTotals' in player_data:
#         # Regular Season
#         regSeasonCareer = player_data['careerTotals']['regularSeason']
#         regSeasonCareerAssists.append(regSeasonCareer['assists'])
#         if 'gameWinningGoals' in regSeasonCareer:
#             regSeasonCareerGWG.append(regSeasonCareer['gameWinningGoals'])
#         else:
#             regSeasonCareerGWG.append(None)
#         if 'gamesPlayed' in regSeasonCareer:
#             regSeasonCareerGP.append(regSeasonCareer['gamesPlayed'])
#         else:
#             regSeasonCareerGP.append(None)
#         if 'goals' in regSeasonCareer:
#             regSeasonCareerGoals.append(regSeasonCareer['goals'])
#         else:
#             regSeasonCareerGoals.append(None)
#         if 'otGoals' in regSeasonCareer:
#             regSeasonCareerOTGoals.append(regSeasonCareer['otGoals'])
#         else:
#             regSeasonCareerOTGoals.append(None)
#         if 'pim'in regSeasonCareer:
#             regSeasonCareerPIM.append(regSeasonCareer['pim'])
#         else:
#             regSeasonCareerPIM.append(None)
#         if 'plusMinus' in regSeasonCareer:
#             regSeasonCareerPlusMinus.append(regSeasonCareer['plusMinus'])
#         else:
#             regSeasonCareerPlusMinus.append(None)
#         if 'points' in regSeasonCareer:
#             regSeasonCareerPts.append(regSeasonCareer['points'])
#         else:
#             regSeasonCareerPts.append(None)
#         if 'powerPlayGoals' in regSeasonCareer:
#             regSeasonCareerPPG.append(regSeasonCareer['powerPlayGoals'])
#         else:
#             regSeasonCareerPPG.append(None)
#         if 'powerPlayPoints' in regSeasonCareer:
#             regSeasonCareerPPPoints.append(regSeasonCareer['powerPlayPoints'])
#         else:
#             regSeasonCareerPPPoints.append(None)
#         if 'shootingPctg' in regSeasonCareer:
#             regSeasonCareerShootingPct.append(regSeasonCareer['shootingPctg'])
#         else:
#             regSeasonCareerShootingPct.append(None)
#         if 'shorthandedGoals' in regSeasonCareer:
#             regSeasonCareerSHG.append(regSeasonCareer['shorthandedGoals'])
#         else:
#             regSeasonCareerSHG.append(None)
#         if 'shorthandedPoints' in regSeasonCareer:
#             regSeasonCareerSHPts.append(regSeasonCareer['shorthandedPoints'])
#         else:
#             regSeasonCareerSHPts.append(None)
#         if 'shots' in regSeasonCareer:
#             regSeasonCareerShots.append(regSeasonCareer['shots'])
#         else:
#             regSeasonCareerShots.append(None)

#         # Playoffs
#         if 'playoffs' in player_data['careerTotals']:
#             playoffsCareer = player_data['careerTotals']['playoffs']
#             if 'assists' in playoffsCareer:
#                 playoffsCareerAssists.append(playoffsCareer['assists'])
#             else:
#                 playoffsCareerAssists.append(None)
#             if 'gameWinningGoals' in playoffsCareer:
#                 playoffsCareerGWG.append(playoffsCareer['gameWinningGoals'])
#             else:
#                 playoffsCareerGWG.append(None)
#             if 'gamesPlayed' in playoffsCareer:
#                 playoffsCareerGP.append(playoffsCareer['gamesPlayed'])
#             else:
#                 playoffsCareerGP.append(None)
#             if 'goals' in playoffsCareer:
#                 playoffsCareerGoals.append(playoffsCareer['goals'])
#             else:
#                 playoffsCareerGoals.append(None)
#             if 'otGoals' in playoffsCareer:
#                 playoffsCareerOTGoals.append(playoffsCareer['otGoals'])
#             else:
#                 playoffsCareerOTGoals.append(None)
#             if 'pim' in playoffsCareer:
#                 playoffsCareerPIM.append(playoffsCareer['pim'])
#             else:
#                 playoffsCareerPIM.append(None)
#             if 'plusMinus' in playoffsCareer:
#                 playoffsCareerPlusMinus.append(playoffsCareer['plusMinus'])
#             else:
#                 playoffsCareerPlusMinus.append(None)
#             if 'points' in playoffsCareer:
#                 playoffsCareerPts.append(playoffsCareer['points'])
#             else:
#                 playoffsCareerPts.append(None)
#             if 'powerPlayGoals' in playoffsCareer:
#                 playoffsCareerPPG.append(playoffsCareer['powerPlayGoals'])
#             else:
#                 playoffsCareerPPG.append(None)
#             if 'powerPlayPoints' in playoffsCareer:
#                 playoffsCareerPPPoints.append(playoffsCareer['powerPlayPoints'])
#             else:
#                 playoffsCareerPPPoints.append(None)
#             if 'shootingPctg' in playoffsCareer:
#                 playoffsCareerShootingPct.append(playoffsCareer['shootingPctg'])
#             else:
#                 playoffsCareerShootingPct.append(None)
#             if 'shorthandedGoals' in playoffsCareer:
#                 playoffsCareerSHG.append(playoffsCareer['shorthandedGoals'])
#             else:
#                 playoffsCareerSHG.append(None)
#             if 'shorthandedPoints' in playoffsCareer:
#                 playoffsCareerSHPts.append(playoffsCareer['shorthandedPoints'])
#             else:
#                 playoffsCareerSHPts.append(None)
#             if 'shots' in playoffsCareer:
#                 playoffsCareerShots.append(playoffsCareer['shots'])
#             else:
#                 playoffsCareerShots.append(None)
#         else:
#             playoffsCareerAssists.append(None)
#             playoffsCareerGWG.append(None)
#             playoffsCareerGP.append(None)
#             playoffsCareerGoals.append(None)
#             playoffsCareerOTGoals.append(None)
#             playoffsCareerPIM.append(None)
#             playoffsCareerPlusMinus.append(None)
#             playoffsCareerPts.append(None)
#             playoffsCareerPPG.append(None)
#             playoffsCareerPPPoints.append(None)
#             playoffsCareerShootingPct.append(None)
#             playoffsCareerSHG.append(None)
#             playoffsCareerSHPts.append(None)
#             playoffsCareerShots.append(None)
#     else:
#         regSeasonCareerAssists.append(None)
#         regSeasonCareerGWG.append(None)
#         regSeasonCareerGP.append(None)
#         regSeasonCareerGoals.append(None)
#         regSeasonCareerOTGoals.append(None)
#         regSeasonCareerPIM.append(None)
#         regSeasonCareerPlusMinus.append(None)
#         regSeasonCareerPts.append(None)
#         regSeasonCareerPPG.append(None)
#         regSeasonCareerPPPoints.append(None)
#         regSeasonCareerShootingPct.append(None)
#         regSeasonCareerSHG.append(None)
#         regSeasonCareerSHPts.append(None)
#         regSeasonCareerShots.append(None)

#     # Other
#     shopLink.append(player_data['shopLink'])
#     twitterLink.append(player_data['twitterLink'])
#     watchLink.append(player_data['watchLink'])
#     if 'last5Games' in player_data:
#         last5Games.append(player_data['last5Games'])
#     else:
#         last5Games.append(None)
    
    # Season-by-Season Stats
    # seasonTotals.append(player_data['seasonTotals'])

    # print(player_data['seasonTotals'])


    ### SEASON SUMMARY DATA ###
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

    # season_data = player_data['seasonTotals']
    # for season in season_data:
    #     playerId.append(playerID)
    #     seasonAssists.append(season['assists'])
    #     seasonGameTypeId.append(season['gameTypeId'])
    #     seasonGamesPlayed.append(season['gamesPlayed'])
    #     seasonGoals.append(season['goals'])
    #     seasonLeagueAbbrev.append(season['leagueAbbrev'])
    #     if 'pim' in season:
    #         seasonPIM.append(season['pim'])
    #     else:
    #         seasonPIM.append(None)
    #     seasonPoints.append(season['points'])
    #     seasonSeason.append(season['season'])
    #     seasonSequence.append(season['sequence'])
    #     seasonTeamName.append(season['teamName']['default'])

    # print(len(playerId))
    # print(len(seasonAssists))
    # print(len(seasonGameTypeId))
    # print(len(seasonGamesPlayed))
    # print(len(seasonGoals))
    # print(len(seasonLeagueAbbrev))
    # print(len(seasonPIM))
    # print(len(seasonPoints))
    # print(len(seasonSeason))
    # print(len(seasonSequence))
    # print(len(seasonTeamName))

    # df_seasons_data = pd.DataFrame([playerId, seasonAssists, seasonGameTypeId, seasonGamesPlayed,
    #                                 seasonGoals, seasonLeagueAbbrev, seasonPIM, seasonPoints, seasonSeason,
    #                                 seasonSequence, seasonTeamName]).transpose()
    # df_seasons_data.columns = ['playerId', 'seasonAssists', 'seasonGameTypeId', 'seasonGamesPlayed',
    #                            'seasonGoals', 'seasonLeagueAbbrev', 'seasonPIM', 'seasonPoints', 'seasonSeason',
    #                            'seasonSequence', 'seasonTeamName']
    
    # print(df_seasons_data.head())
    # df_seasons_data.to_csv("season_stats.csv", index=False)


    # if 'awards' in player_data:
    #     awards = player_data['awards']
    #     for award in awards:
    #         local_awardNames = []
    #         local_awardSeasons = []
    #         local_awardNames.append(award['trophy']['default'])
    #         seasonsWon = []
    #         for season in award['seasons']:
    #             seasonsWon.append(season['seasonId'])
    #         local_awardSeasons.append(seasonsWon)
    #     awardNames.append(local_awardNames)
    #     awardSeasons.append(local_awardSeasons)
    # else:
    #     awardNames.append(None)
    #     awardSeasons.append(None)
    # if 'currentTeamRoster' in player_data:
    #     currentTeamRoster.append(player_data['currentTeamRoster'])
    # else:
        # currentTeamRoster.append(None)


### Create, save, and view df ###
# df_players = pd.DataFrame([playerId, isActive, currentTeamId, currentTeamAbbrev, fullTeamName, teamCommonName, teamPlaceNameWithPreposition,
#                             firstName, lastName, badgesLogos, badgesNames, teamLogo, sweaterNumber, position, headshot, heroImage, heightInInches, 
#                             heightInCentimeters, weightInPounds, weightInKilograms, birthDate, birthCity, birthStateProvince, birthCountry, shootsCatches,
#                             draftYear, draftTeam, draftRound, draftPickInRound, draftOverall, playerSlug, inTop100AllTime, inHHOF, featuredSeasonAssists,
#                             featuredSeasonGWG, featuredSeasonGP, featuredSeasonGoals, featuredSeasonOTGoals, featuredSeasonPIM, featuredSeasonPlusMinus,
#                             featuredSeasonPts, featuredSeasonPPG, featuredSeasonPPPoints, featuredSeasonShootingPct, featuredSeasonSHG, featuredSeasonSHPts,
#                             featuredSeasonShots, regSeasonCareerAssists, regSeasonCareerGWG, regSeasonCareerGP, regSeasonCareerGoals, regSeasonCareerOTGoals,
#                             regSeasonCareerPIM, regSeasonCareerPlusMinus, regSeasonCareerPts, regSeasonCareerPPG, regSeasonCareerPPPoints,
#                             regSeasonCareerShootingPct, regSeasonCareerSHG, regSeasonCareerSHPts, regSeasonCareerShots, playoffsCareerAssists,
#                             playoffsCareerGWG, playoffsCareerGP, playoffsCareerGoals, playoffsCareerOTGoals, playoffsCareerPIM, playoffsCareerPlusMinus,
#                             playoffsCareerPts, playoffsCareerPPG, playoffsCareerPPPoints, playoffsCareerShootingPct, playoffsCareerSHG, playoffsCareerSHPts,
#                             playoffsCareerShots, shopLink, twitterLink, watchLink, last5Games, seasonTotals, awardNames, awardSeasons, currentTeamRoster]
#                             ).transpose()

# df_players.columns = ['playerId', 'isActive', 'currentTeamId', 'currentTeamAbbrev', 'fullTeamName', 'teamCommonName', 'teamPlaceNameWithPreposition',
#                         'firstName', 'lastName', 'badgesLogos','badgesNames','teamLogo', 'sweaterNumber', 'position', 'headshot', 'heroImage',
#                         'heightInInches', 'heightInCentimeters', 'weightInPounds', 'weightInKilograms', 'birthDate', 'birthCity', 'birthStateProvince', 
#                         'birthCountry', 'shootsCatches', 'draftYear', 'draftTeam', 'draftRound', 'draftPickInRound', 'draftOverall','playerSlug', 
#                         'inTop100AllTime', 'inHHOF','featuredSeasonAssists','featuredSeasonGWG','featuredSeasonGP', 'featuredSeasonGoals',
#                         'featuredSeasonOTGoals', 'featuredSeasonPIM', 'featuredSeasonPlusMinus', 'featuredSeasonPts', 'featuredSeasonPPG',
#                         'featuredSeasonPPPoints', 'featuredSeasonShootingPct', 'featuredSeasonSHG', 'featuredSeasonSHPts', 'featuredSeasonShots',
#                         'regSeasonCareerAssists', 'regSeasonCareerGWG', 'regSeasonCareerGP', 'regSeasonCareerGoals', 'regSeasonCareerOTGoals',
#                         'regSeasonCareerPIM', 'regSeasonCareerPlusMinus', 'regSeasonCareerPts', 'regSeasonCareerPPG', 'regSeasonCareerPPPoints',
#                         'regSeasonCareerShootingPct', 'regSeasonCareerSHG','regSeasonCareerSHPts','regSeasonCareerShots', 'playoffsCareerAssists',
#                         'playoffsCareerGWG', 'playoffsCareerGP', 'playoffsCareerGoals', 'playoffsCareerOTGoals', 'playoffsCareerPIM',
#                         'playoffsCareerPlusMinus', 'playoffsCareerPts', 'playoffsCareerPPG', 'playoffsCareerPPPoints', 'playoffsCareerShootingPct',
#                         'playoffsCareerSHG', 'playoffsCareerSHPts', 'playoffsCareerShots', 'shopLink', 'twitterLink', 'watchLink', 'last5Games',
#                         'seasonTotals', 'awardNames', 'awardSeasons','currentTeamRoster']

# df_players.to_csv("player_detail.csv", index=False)
# print(df_players.head())



