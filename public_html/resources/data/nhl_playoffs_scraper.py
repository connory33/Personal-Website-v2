import pandas as pd
import requests
import time
import random

pd.set_option('display.max_columns', None)
pd.set_option('display.max_rows', None)

series_url = "https://api-web.nhle.com/v1/schedule/playoff-series/"
carousel_url = "https://api-web.nhle.com/v1/playoff-series/carousel/"

# Series letters and seasons
seriesExt = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o']
season_ext = ['19171918', '19181919', '19201921', '19211922', '19221923', '19231924', '19241925', '19251926', '19261927', '19271928',
              '19281929', '19291930', '19301931', '19311932', '19321933', '19331934', '19341935', '19351936', '19361937', '19371938',
              '19381939', '19391940', '19401941', '19411942', '19421943', '19431944', '19441945', '19451946', '19461947', '19471948',
              '19481949', '19491950', '19501951', '19511952', '19521953', '19531954', '19541955', '19551956', '19561957', '19571958',
              '19581959', '19591960', '19601961', '19611962', '19621963', '19631964', '19641965', '19651966', '19661967', '19671968',
              '19681969', '19691970', '19701971', '19711972', '19721973', '19731974', '19741975', '19751976', '19761977', '19771978',
              '19781979', '19791980', '19801981', '19811982', '19821983', '19831984', '19841985', '19851986', '19861987', '19871988',
              '19881989', '19891990', '19901991', '19911992', '19921993', '19931994', '19941995', '19951996', '19961997', '19971998',
              '19981999', '19992000', '20002001', '20012002', '20022003', '20032004', '20042005', '20052006', '20062007', '20072008',
              '20082009', '20092010', '20102011', '20112012', '20122013', '20132014', '20142015', '20152016', '20162017', '20172018',
              '20182019', '20192020', '20202021', '20212022', '20222023', '20232024', '20242025']

# Create separate DataFrames for each endpoint
series_games_df = pd.DataFrame()
carousel_df = pd.DataFrame()

# Process each season
for season in season_ext[-5:]:
    print(f"Processing season: {season}")
    
    # PART 1: Fetch and process carousel data for this season
    c_url = f"{carousel_url}{season}"
    try:
        c_response = requests.head(c_url, allow_redirects=True)
        
        if c_response.status_code == 200:
            c_data = requests.get(c_url).json()
            
            # Process carousel data
            rounds = c_data.get('rounds', [])
            
            for round_data in rounds:
                round_number = round_data.get('roundNumber')
                series_list = round_data.get('series', [])
                
                for series in series_list:
                    # Create a row for this series
                    carousel_row = {
                        'season': season,
                        'seriesLetter': series.get('seriesLetter', '').lower(),  # Normalized to lowercase
                        'roundNumber': round_number,
                        'seriesLink': series.get('seriesLink'),
                        'bottomSeedId': series.get('bottomSeed', {}).get('id'),
                        'carousel_bottomSeedWins': series.get('bottomSeed', {}).get('wins'),  # Renamed to clarify source
                        'topSeedId': series.get('topSeed', {}).get('id'),
                        'carousel_topSeedWins': series.get('topSeed', {}).get('wins')  # Renamed to clarify source
                    }
                    
                    # Add to carousel DataFrame
                    carousel_df = pd.concat([carousel_df, pd.DataFrame([carousel_row])], ignore_index=True)
    
    except requests.RequestException as e:
        print(f"Error fetching carousel data for season {season}: {e}")
    
    # PART 2: Fetch and process series/games data
    for series_letter in seriesExt:
        s_url = series_url + season + '/' + series_letter
        
        try:
            s_response = requests.head(s_url, allow_redirects=True)
            
            if s_response.status_code == 200:
                s_data = requests.get(s_url).json()
                
                # Extract the series-level data
                series_data = {
                    'season': season,
                    'seriesLetter': s_data.get('seriesLetter', '').lower(),  # Normalized to lowercase
                    'seriesLogo': s_data.get('seriesLogo'),
                    'neededToWin': s_data.get('neededToWin'),
                    'length': s_data.get('length')
                }
                
                # Process each game in the series
                for game in s_data.get('games', []):
                    # Create a row for this game
                    game_row = {
                        **series_data,  # Include all series data
                        'gameId': game.get('id'),
                        'gameNumber': game.get('gameNumber'),
                        'gameDate': game.get('gameDate'),
                        'awayTeamId': game.get('awayTeam', {}).get('id'),
                        'awayTeamAbbrev': game.get('awayTeam', {}).get('abbrev'),
                        'awayTeamScore': game.get('awayTeam', {}).get('score'),
                        'homeTeamId': game.get('homeTeam', {}).get('id'),
                        'homeTeamAbbrev': game.get('homeTeam', {}).get('abbrev'),
                        'homeTeamScore': game.get('homeTeam', {}).get('score'),
                        'gameCenterLink': game.get('gameCenterLink'),
                        'seriesStatusTopSeedWins': game.get('seriesStatus', {}).get('topSeedWins'),  # Keep original name
                        'seriesStatusBottomSeedWins': game.get('seriesStatus', {}).get('bottomSeedWins'),  # Keep original name
                        'fullCoverageURL': game.get('fullCoverageURL'),
                        'lastPeriodType': game.get('gameOutcome', {}).get('lastPeriodType')
                    }
                    
                    # Add to series games DataFrame
                    series_games_df = pd.concat([series_games_df, pd.DataFrame([game_row])], ignore_index=True)
                
            elif s_response.status_code != 404:
                # Only report non-404 errors
                print(f"Error status {s_response.status_code} for {s_url}")
                
        except requests.RequestException as e:
            print(f"Error fetching series data for {season}/{series_letter}: {e}")
        
        # Add a small delay to avoid hammering the API
        time.sleep(random.uniform(0.1, 0.3))

# Now merge the two DataFrames on season and seriesLetter
print(f"Series games data: {len(series_games_df)} rows")
print(f"Carousel data: {len(carousel_df)} rows")

# Check how many unique (season, seriesLetter) combinations we have in each DataFrame
print(f"Unique (season, seriesLetter) in series data: {series_games_df[['season', 'seriesLetter']].drop_duplicates().shape[0]}")
print(f"Unique (season, seriesLetter) in carousel data: {carousel_df[['season', 'seriesLetter']].drop_duplicates().shape[0]}")

# Merge the DataFrames
merged_df = pd.merge(
    series_games_df, 
    carousel_df,
    on=['season', 'seriesLetter'],
    how='left'
)

# Save the combined data to CSV
output_file = "C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/nhl_playoff_series_combined.csv"
merged_df.to_csv(output_file, index=False)

print(f"Data collection complete! Processed {len(merged_df)} records.")
print(f"Data saved to {output_file}")

# Print a sample of the data to verify carousel fields are populated
print("\nSample of output data (first 3 rows):")
if not merged_df.empty:
    sample = merged_df.head(3)
    cols_to_display = ['season', 'seriesLetter', 'roundNumber', 'seriesStatusTopSeedWins', 
                      'carousel_topSeedWins', 'seriesStatusBottomSeedWins', 'carousel_bottomSeedWins']
    print(sample[cols_to_display])
    
    # Check how many rows have carousel data
    has_round_data = merged_df['roundNumber'].notna().sum()
    print(f"\n{has_round_data} out of {len(merged_df)} rows have carousel data ({has_round_data/len(merged_df)*100:.1f}%)")