# update_database.py

import requests
import mysql.connector
import pandas as pd
from datetime import datetime

# --- 1. Connect to MySQL ---
def connect_to_database():
    return mysql.connector.connect(
        host="11.118.0.36",  # Use the web server's IP as the host
        user="cpses_poh3clesi3",  # Use the MySQL username
        password="PatrickRoy33",  # Use the MySQL password
        database="NHL API"  # Your database name
    )

# --- 2. Scrape or Fetch Data ---
def fetch_data():
    # Example: Get data from an API
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
    return pipeline_test_df

# --- 3. Process the Data (optional) ---
def process_data(raw_data):
    # Example: turn it into a pandas DataFrame
    df = pd.DataFrame(raw_data)
    # Maybe you want to clean/rename columns here
    return df

# --- 4. Update Database ---
def update_database(conn, df):
    cursor = conn.cursor()

    for _, row in df.iterrows():
        # Example: Insert row into table
        cursor.execute("""
            INSERT INTO pipeline-test (Test Column)
            VALUES (%s, %s, %s)
        """, (row['field1'], row['field2'], row['field3']))

        # ADD BACK ON DUPLICATE KEY UPDATE

    conn.commit()
    cursor.close()

# --- 5. Main Function ---
def main():
    print(f"Starting update at {datetime.now()}")

    conn = connect_to_database()
    raw_data = fetch_data()
    processed_data = process_data(raw_data)
    update_database(conn, processed_data)

    conn.close()
    print(f"Update finished at {datetime.now()}")

if __name__ == "__main__":
    main()
