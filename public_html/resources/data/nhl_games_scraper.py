import requests
import pandas as pd

# Base URL for game data API
base_url = "https://api-web.nhle.com/v1/gamecenter/"

# List of game IDs
gameID_list = [...]  # Populate this list with your actual game IDs

# Initialize a list to store game IDs
game_ids = []

# Loop over the last 100 game IDs
for game_id in gameID_list[-100:]:
    # Set game code to current game and pull JSON
    game_code = str(game_id)
    url = base_url + game_code
    
    try:
        response = requests.head(url, allow_redirects=True)
        if response.status_code == 404:
            print(f"Game {game_id} not found (404).")
        else:
            game_ids.append(game_id)  # Add the game_id to the list

    except requests.RequestException as e:
        print(f"Error fetching JSON for game {game_id}: {e}")
        continue

# Create a DataFrame from the game_ids list
pipeline_test_df = pd.DataFrame(game_ids, columns=["game_id"])

# Check the DataFrame
print(pipeline_test_df)

# Optionally, you can save this to a CSV file
pipeline_test_df.to_csv("pipeline_test_table.csv", index=False)
