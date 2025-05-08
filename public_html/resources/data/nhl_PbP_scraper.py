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

################################################ PLAY-BY-PLAY + ADDL. GAME DETAILS ###################################################

gameID = []; eventID = []; period = []; timeInPeriod = []; timeRemaining = []; situationCode = []
typeCode = []; typeDescKey = []; xCoord = []; yCoord = []; zoneCode = []; eventOwnerTeamId = []
faceoffLoserId = []; faceoffWinnerId = []; hittingPlayerId = []; hitteePlayerId = []; shotType = []
shootingPlayerId = []; goalieInNetId = []; awaySOG = []; homeSOG = []; reason = []; takeawayGiveawayPlayerId = []
blockingPlayerId = []; scoringPlayerId = []; assist1PlayerId = []; assist1PlayerTotal = []; awayScore = []
homeScore = []; penaltySeverity = []; penaltyType = []; duration = []; committerId = []; drawerId = []
homeTeamDefendingSide = []

# build URL for individual game details + play-by-play
base_url = "https://api-web.nhle.com/v1/gamecenter/"
# game_code = '2024021247' # test game code
suffix= "/play-by-play"

count=0
for game_id in gameID_list[-9500:-9000]:
    if count % 200 == 0:
        print(f"Processed {count} games.")
    count += 1
    # set game code to current game and pull JSON
    game_url = base_url + str(game_id) + suffix
    try:
        response = requests.head(game_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).")
        else:
            data = requests.get(game_url).json()
            if not data['plays']: # checks to see if plays list is empty
                gameID.append(data['id'])
                eventID.append('No data available')
                period.append('No data available')
                timeInPeriod.append('No data available')
                timeRemaining.append('No data available')
                situationCode.append('No data available')
                typeCode.append('No data available')
                typeDescKey.append('No data available')
                xCoord.append('No data available')
                yCoord.append('No data available')
                zoneCode.append('No data available')
                eventOwnerTeamId.append('No data available')
                faceoffLoserId.append('No data available')
                faceoffWinnerId.append('No data available')
                hittingPlayerId.append('No data available')
                hitteePlayerId.append('No data available')
                shotType.append('No data available')
                shootingPlayerId.append('No data available')
                goalieInNetId.append('No data available')
                awaySOG.append('No data available')
                homeSOG.append('No data available')
                reason.append('No data available')
                takeawayGiveawayPlayerId.append('No data available')
                blockingPlayerId.append('No data available')
                scoringPlayerId.append('No data available')
                assist1PlayerId.append('No data available')
                assist1PlayerTotal.append('No data available')
                awayScore.append('No data available')
                homeScore.append('No data available')
                penaltySeverity.append('No data available')
                penaltyType.append('No data available')
                duration.append('No data available')
                committerId.append('No data available')
                drawerId.append('No data available')
                homeTeamDefendingSide.append('No data available')

            else:
                for play in data['plays']:
                    # print(data['plays'])
                    # add game ID to each row for joining
                    gameID.append(data['id'])

                    # capture basic info that is always present for every play entry
                    eventID.append(play['eventId'])
                    period.append(play['periodDescriptor']['number'])
                    timeInPeriod.append(play['timeInPeriod'])
                    timeRemaining.append(play['timeRemaining'])
                    typeCode.append(play['typeCode'])
                    typeKey = play['typeDescKey']
                    typeDescKey.append(typeKey)
                    if 'situationCode' in play:
                        situationCode.append(play['situationCode'])
                    else:
                        situationCode.append(None)

                    # move on if there are no additional details to capture about the play (e.g. period end)
                    if 'details' not in play:
                        xCoord.append(None)
                        yCoord.append(None)
                        zoneCode.append(None)
                        eventOwnerTeamId.append(None)
                        faceoffLoserId.append(None)
                        faceoffWinnerId.append(None)
                        hittingPlayerId.append(None)
                        hitteePlayerId.append(None)
                        shotType.append(None)
                        shootingPlayerId.append(None)
                        goalieInNetId.append(None)
                        awaySOG.append(None)
                        homeSOG.append(None)
                        reason.append(None)
                        takeawayGiveawayPlayerId.append(None)
                        blockingPlayerId.append(None)
                        scoringPlayerId.append(None)
                        assist1PlayerId.append(None)
                        assist1PlayerTotal.append(None)
                        awayScore.append(None)
                        homeScore.append(None)
                        penaltySeverity.append(None)
                        penaltyType.append(None)
                        duration.append(None)
                        committerId.append(None)
                        drawerId.append(None)
                        homeTeamDefendingSide.append(None)
                    else:
                        # otherwise, first capture all the general fields that are present for every details field (if existing)
                        if 'xCoord' in play['details'] and 'yCoord' in play['details']:
                            xCoord.append(play['details']['xCoord'])
                            yCoord.append(play['details']['yCoord'])
                        else:
                            xCoord.append(None)
                            yCoord.append(None)
                            
                        if 'zoneCode' in play['details']:
                            zoneCode.append(play['details']['zoneCode'])
                        else:
                            zoneCode.append(None)

                        if 'eventOwnerTeamId' in play['details']:
                            eventOwnerTeamId.append(play['details']['eventOwnerTeamId'])
                        else:
                            eventOwnerTeamId.append(None)

                        if 'homeTeamDefendingSide' in play:
                            homeTeamDefendingSide.append(play['homeTeamDefendingSide'])

                        # now, capture fields that are filled or not dependent on play type

                        # Faceoff
                        if typeKey == 'faceoff':
                            faceoffLoserId.append(play['details']['losingPlayerId'])
                            faceoffWinnerId.append(play['details']['winningPlayerId'])
                            hittingPlayerId.append(None)
                            hitteePlayerId.append(None)
                            shotType.append(None)
                            shootingPlayerId.append(None)
                            goalieInNetId.append(None)
                            awaySOG.append(None)
                            homeSOG.append(None)
                            reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)

                        # Hits
                        elif typeKey == 'hit':
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(play['details']['hittingPlayerId']) 
                            hitteePlayerId.append(play['details']['hitteePlayerId'])
                            shotType.append(None)
                            shootingPlayerId.append(None)
                            goalieInNetId.append(None)
                            awaySOG.append(None)
                            homeSOG.append(None)
                            reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)

                        # Missed Shots
                        elif typeKey == 'missed-shot':
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(None) 
                            hitteePlayerId.append(None)
                            if 'shotType' in play['details']:
                                shotType.append(play['details']['shotType'])
                            if 'shootingPlayerId' in play['details']:
                                shootingPlayerId.append(play['details']['shootingPlayerId'])
                            if 'goalieInNetId' in play['details']:
                                goalieInNetId.append(play['details']['goalieInNetId'])
                            else:
                                goalieInNetId.append('Not listed')
                            awaySOG.append(None)
                            homeSOG.append(None)
                            if 'reason' in play['details']:
                                reason.append(play['details']['reason'])
                            else:
                                reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)
                        
                        # Shots on Goal
                        elif typeKey == 'shot-on-goal':
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(None) 
                            hitteePlayerId.append(None)
                            if 'shotType' in play['details']:
                                shotType.append(play['details']['shotType'])
                            else:
                                shotType.append(None)
                            if 'shootingPlayerId' in play['details']:
                                shootingPlayerId.append(play['details']['shootingPlayerId'])
                            else:
                                shootingPlayerId.append(None)
                            if 'goalieInNetId' in play['details']:
                                goalieInNetId.append(play['details']['goalieInNetId'])
                            else:
                                goalieInNetId.append('Not listed')
                            awaySOG.append(play['details']['awaySOG'])
                            homeSOG.append(play['details']['homeSOG'])
                            reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)

                        # Stoppages
                        elif typeKey == 'stoppage':
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(None) 
                            hitteePlayerId.append(None)
                            shotType.append(None)
                            shootingPlayerId.append(None)
                            goalieInNetId.append(None)
                            awaySOG.append(None)
                            homeSOG.append(None)
                            if 'reason' in play['details']:
                                reason.append(play['details']['reason'])
                            else:
                                reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)

                        # Takeaway or Giveaway
                        elif typeKey in ['takeaway', 'giveaway']:
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(None) 
                            hitteePlayerId.append(None)
                            shotType.append(None)
                            shootingPlayerId.append(None)
                            goalieInNetId.append(None)
                            awaySOG.append(None)
                            homeSOG.append(None)
                            reason.append(None)
                            if 'playerId' in play['details']:
                                takeawayGiveawayPlayerId.append(play['details']['playerId'])
                            else:
                                takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)

                        # Blocked Shot
                        elif typeKey == 'blocked-shot':
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(None) 
                            hitteePlayerId.append(None)
                            shotType.append(None)
                            if 'shootingPlayId' in play['details']:
                                shootingPlayerId.append(play['details']['shootingPlayerId'])
                            goalieInNetId.append(None)
                            awaySOG.append(None)
                            homeSOG.append(None)
                            reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            if 'blockingPlayerId' in play['details']:
                                blockingPlayerId.append(play['details']['blockingPlayerId'])
                            else:
                                blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)

                        # Goal
                        elif typeKey == 'goal':
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(None) 
                            hitteePlayerId.append(None)
                            if 'shotType' in play['details']:
                                shotType.append(play['details']['shotType'])
                            else:
                                shotType.append(None)
                            shootingPlayerId.append(None)
                            if 'goalieInNetId' in play['details']:
                                goalieInNetId.append(play['details']['goalieInNetId'])
                            else:
                                goalieInNetId.append('Not listed')
                            awaySOG.append(None)
                            homeSOG.append(None)
                            reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(play['details']['scoringPlayerId'])
                            if 'assist1PlayerId' in play['details']:
                                assist1PlayerId.append(play['details']['assist1PlayerId'])
                                assist1PlayerTotal.append(play['details']['assist1PlayerTotal'])
                            else:
                                assist1PlayerId.append('Not listed')
                                assist1PlayerTotal.append('Not listed')
                            awayScore.append(play['details']['awayScore'])
                            homeScore.append(play['details']['homeScore'])
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)

                        # Penalty
                        elif typeKey == 'penalty':
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(None) 
                            hitteePlayerId.append(None)
                            shotType.append(None)
                            shootingPlayerId.append(None)
                            goalieInNetId.append(None)
                            awaySOG.append(None)
                            homeSOG.append(None)
                            reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(play['details']['typeCode'])
                            penaltyType.append(play['details']['descKey'])
                            duration.append(play['details']['duration'])
                            if 'committedByPlayerId' in play['details']:
                                committerId.append(play['details']['committedByPlayerId'])
                            else:
                                committerId.append('Not listed')
                            if 'drawnByPlayerId' in play['details']:
                                drawerId.append(play['details']['drawnByPlayerId'])
                            else:
                                drawerId.append('Not listed')

                        else:
                            faceoffLoserId.append(None)
                            faceoffWinnerId.append(None)
                            hittingPlayerId.append(None) 
                            hitteePlayerId.append(None)
                            shotType.append(None)
                            shootingPlayerId.append(None)
                            goalieInNetId.append(None)
                            awaySOG.append(None)
                            homeSOG.append(None)
                            reason.append(None)
                            takeawayGiveawayPlayerId.append(None)
                            blockingPlayerId.append(None)
                            scoringPlayerId.append(None)
                            assist1PlayerId.append(None)
                            assist1PlayerTotal.append(None)
                            awayScore.append(None)
                            homeScore.append(None)
                            penaltySeverity.append(None)
                            penaltyType.append(None)
                            duration.append(None)
                            committerId.append(None)
                            drawerId.append(None)
    except requests.RequestException as e:
        print("Error checking URL:", e)            
    

df_plays = pd.DataFrame([gameID, eventID,period,timeInPeriod,timeRemaining,situationCode,typeCode,typeDescKey,xCoord,yCoord,zoneCode,eventOwnerTeamId,
                   faceoffLoserId,faceoffWinnerId,hittingPlayerId,hitteePlayerId,shotType,shootingPlayerId,goalieInNetId,
                   awaySOG,homeSOG,reason,takeawayGiveawayPlayerId,blockingPlayerId,scoringPlayerId,assist1PlayerId,
                   assist1PlayerTotal,awayScore,homeScore,penaltySeverity,penaltyType,duration,committerId,drawerId, homeTeamDefendingSide]).transpose()
# for column in ['shootingPlayerId', 'goalieInNetId']:
#     df_plays[column] = df_plays[column].astype(int)

df_plays.columns = ['gameID', 'eventID','period','timeInPeriod','timeRemaining','situationCode','typeCode','typeDescKey','xCoord','yCoord',
                   'zoneCode','eventOwnerTeamId','faceoffLoserId','faceoffWinnerId','hittingPlayerId','hitteePlayerId',
                   'shotType','shootingPlayerId','goalieInNetId','awaySOG','homeSOG','reason','takeawayGiveawayPlayerId',
                   'blockingPlayerId','scoringPlayerId','assist1PlayerId','assist1PlayerTotal','awayScore','homeScore',
                   'penaltySeverity','penaltyType','duration','committerId','drawerId','homeTeamDefendingSide']
df_plays.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/nhl_PbP.csv", index=False)

print(df_plays.head())


