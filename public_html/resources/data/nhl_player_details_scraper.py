import requests
import json
from bs4 import BeautifulSoup
import pandas as pd
import numpy as np
import time
import random

pd.set_option('display.max_columns', None)  # Show all columns


################################################### PLAYER DETAILS ########################################################

# need to get distinct list of all player IDs from rosters - export w SQL from DB

### Define URL setup ###
player_base_url = "https://api-web.nhle.com/v1/player/" 
player_suffix = "/landing"

# playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/playerID_list.csv")
playerID_list = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/team_prospects.csv")
playerID_list = playerID_list['prospect_id'].to_list() # convert from df to list for looping

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
# test_list = ['8478406']
count = 0
for playerID in playerID_list: 
    count += 1
    # print(count)
    if count % 25 == 0:
        print(f"Processed {count} players.")

    player_url = player_base_url + str(playerID) + player_suffix
    try:
        response = requests.head(player_url, allow_redirects=True)
        if response.status_code == 404:
            print("Page not found (404).", playerID)
            continue
        else:
            player_data = requests.get(player_url).json()


        ### BASIC INFO ###
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

        if 'firstName' in player_data and 'default' in player_data['firstName']:
            firstName.append(player_data['firstName']['default'])
        else:
            firstName.append(None)
        if 'lastName' in player_data and 'default' in player_data['lastName']:
            lastName.append(player_data['lastName']['default'])
        else:
            lastName.append(None)

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
        if 'careerTotals' in player_data and 'regularSeason' in player_data['careerTotals']:
            regSeasonCareer = player_data['careerTotals']['regularSeason']

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
            regSeasonCareerTOI.append(None)
            regSeasonCareerGAA.append(None)
            regSeasonCareerLosses.append(None)
            regSeasonCareerSO.append(None)
            regSeasonCareerTies.append(None)
            regSeasonCareerWins.append(None)
            regSeasonCareerGS.append(None)
            regSeasonCareerGA.append(None)
            regSeasonCareerSavePct.append(None)
            regSeasonCareerOTLosses.append(None)
            regSeasonCareerShotsAgainst.append(None)



        # Playoffs
        if 'careerTotals' in player_data and 'playoffs' in player_data['careerTotals']:
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
            playoffsCareerTOI.append(None)
            playoffsCareerGAA.append(None)
            playoffsCareerLosses.append(None)
            playoffsCareerSO.append(None)
            playoffsCareerTies.append(None)
            playoffsCareerWins.append(None)
            playoffsCareerGS.append(None)
            playoffsCareerGA.append(None)
            playoffsCareerSavePct.append(None)
            playoffsCareerOTLosses.append(None)
            playoffsCareerShotsAgainst.append(None)

        # Other
        shopLink.append(player_data['shopLink'])
        twitterLink.append(player_data['twitterLink'])
        watchLink.append(player_data['watchLink'])
        # if 'last5Games' in player_data:
        #     last5Games.append(player_data['last5Games'])
        # else:
        #     last5Games.append(None)
        
        # Season-by-Season Stats
        # seasonTotals.append(player_data['seasonTotals'])


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
df_players = pd.DataFrame([playerId, isActive, currentTeamId,
                            firstName, lastName, badgesLogos, badgesNames, sweaterNumber, position, headshot, heroImage, heightInInches, 
                            heightInCentimeters, weightInPounds, weightInKilograms, birthDate, birthCity, birthStateProvince, birthCountry, shootsCatches,
                            draftYear, draftTeam, draftRound, draftPickInRound, draftOverall, playerSlug, inTop100AllTime, inHHOF, featuredSeason, featuredSeasonAssists,
                            featuredSeasonGWG, featuredSeasonGP, featuredSeasonGoals, featuredSeasonOTGoals, featuredSeasonPIM, featuredSeasonPlusMinus,
                            featuredSeasonPts, featuredSeasonPPG, featuredSeasonPPPoints, featuredSeasonShootingPct, featuredSeasonSHG, featuredSeasonSHPts,
                            featuredSeasonShots, regSeasonCareerAssists, regSeasonCareerGWG, regSeasonCareerGP, regSeasonCareerGoals, regSeasonCareerOTGoals,
                            regSeasonCareerPIM, regSeasonCareerPlusMinus, regSeasonCareerPts, regSeasonCareerPPG, regSeasonCareerPPPoints,
                            regSeasonCareerShootingPct, regSeasonCareerSHG, regSeasonCareerSHPts, regSeasonCareerShots, playoffsCareerAssists,
                            playoffsCareerGWG, playoffsCareerGP, playoffsCareerGoals, playoffsCareerOTGoals, playoffsCareerPIM, playoffsCareerPlusMinus,
                            playoffsCareerPts, playoffsCareerPPG, playoffsCareerPPPoints, playoffsCareerShootingPct, playoffsCareerSHG, playoffsCareerSHPts,
                            playoffsCareerShots, awardNames, awardSeasons, currentTeamRoster,
                            featuredSeasonGAA, featuredSeasonLosses, featuredSeasonSO, featuredSeasonTies, featuredSeasonWins, featuredSeasonGS,
                            featuredSeasonGA, featuredSeasonTOI, regSeasonCareerGAA, regSeasonCareerLosses, regSeasonCareerSO, regSeasonCareerTies,
                            regSeasonCareerWins, regSeasonCareerGS, regSeasonCareerGA, regSeasonCareerTOI, playoffsCareerGAA, playoffsCareerLosses,
                            playoffsCareerSO, playoffsCareerTies, playoffsCareerWins, playoffsCareerGS, playoffsCareerGA, playoffsCareerTOI,
                            featuredSeasonSavePct, featuredSeasonOTLosses, featuredSeasonShotsAgainst, regSeasonCareerSavePct, regSeasonCareerOTLosses,
                            regSeasonCareerShotsAgainst, playoffsCareerSavePct, playoffsCareerOTLosses, playoffsCareerShotsAgainst]
                            ).transpose()


df_players.columns = ['playerId', 'isActive', 'currentTeamId',
                        'firstName', 'lastName', 'badgesLogos','badgesNames', 'sweaterNumber', 'position', 'headshot', 'heroImage',
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
                        'playoffsCareerSHG', 'playoffsCareerSHPts', 'playoffsCareerShots',
                        'awardNames', 'awardSeasons','currentTeamRoster', 'featuredSeasonGAA', 'featuredSeasonLosses',
                        'featuredSeasonSO', 'featuredSeasonTies', 'featuredSeasonWins', 'featuredSeasonGS', 'featuredSeasonGA',
                        'featuredSeasonTOI', 'regSeasonCareerGAA', 'regSeasonCareerLosses', 'regSeasonCareerSO', 'regSeasonCareerTies',
                        'regSeasonCareerWins', 'regSeasonCareerGS', 'regSeasonCareerGA', 'regSeasonCareerTOI',
                        'playoffsCareerGAA', 'playoffsCareerLosses', 'playoffsCareerSO', 'playoffsCareerTies', 'playoffsCareerWins',
                        'playoffsCareerGS', 'playoffsCareerGA', 'playoffsCareerTOI', 'featuredSeasonSavePct', 'featuredSeasonOTLosses',
                        'featuredSeasonShotsAgainst', 'regSeasonCareerSavePct', 'regSeasonCareerOTLosses',
                        'regSeasonCareerShotsAgainst', 'playoffsCareerSavePct', 'playoffsCareerOTLosses', 'playoffsCareerShotsAgainst']


df_players.to_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/nhl_player_details.csv", index=False)
for column in df_players.columns:
    print(len(df_players[column]), column)
# print(df_players.head())