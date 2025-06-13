from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import mysql.connector
from mysql.connector import Error
from langchain_ollama import ChatOllama
from langchain_core.prompts import ChatPromptTemplate
import json
import time
import markdown
from datetime import datetime
import traceback
import re

# ───────────────────────────────────────────────────────────
# 1. Database connection function
# ───────────────────────────────────────────────────────────
def connect_to_database():
    """Create a fresh connection for each query"""
    try:
        connection = mysql.connector.connect(
            host="connoryoung.com",
            user="connor",
            password="PatrickRoy33",
            database="NHL_API"
        )
        return connection
    except Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

# Execute a query with proper connection management
def execute_query(query):
    connection = connect_to_database()
    if connection:
        try:
            cursor = connection.cursor(dictionary=True)
            cursor.execute(query)
            result = cursor.fetchall()
            cursor.close()
            connection.close()
            return result
        except Error as e:
            print(f"Error executing query: {e}")
            if connection and connection.is_connected():
                connection.close()
            raise e
    return []  # Return empty list instead of None to avoid type issues

# Get list of actual tables
def get_actual_tables():
    connection = connect_to_database()
    if connection:
        try:
            cursor = connection.cursor()
            cursor.execute("SHOW TABLES")
            tables = [table[0] for table in cursor.fetchall()]
            cursor.close()
            connection.close()
            return tables
        except Error as e:
            print(f"Error getting tables: {e}")
            if connection:
                connection.close()
    return []

# Get the real tables with their structure directly in SQL, and create a mapping
def get_real_tables_with_structure():
    real_tables_info = {}
    connection = connect_to_database()
    if connection:
        try:
            cursor = connection.cursor()
            # Get all tables
            cursor.execute("SHOW TABLES")
            tables = [table[0] for table in cursor.fetchall()]
            
            # For each table, get column information
            for table_name in tables:
                try:
                    # Get table definition
                    cursor.execute(f"DESCRIBE {table_name}")
                    columns = cursor.fetchall()
                    column_info = []
                    for col in columns:
                        column_info.append({
                            "name": col[0],
                            "type": col[1],
                            "nullable": col[2],
                            "key": col[3],
                            "default": col[4],
                            "extra": col[5]
                        })
                    
                    # Check for primary key and relationships
                    cursor.execute(f"SHOW KEYS FROM {table_name} WHERE Key_name = 'PRIMARY'")
                    primary_keys = cursor.fetchall()
                    pk_columns = [pk[4] for pk in primary_keys]
                    
                    # Store table info
                    real_tables_info[table_name] = {
                        "columns": column_info,
                        "primary_keys": pk_columns
                    }
                    
                except Exception as e:
                    print(f"Error analyzing table {table_name}: {e}")
                    
            cursor.close()
            connection.close()
        except Exception as e:
            print(f"Error getting real tables: {e}")
            if connection:
                connection.close()
            
    return real_tables_info

# Define manual descriptions for the most important tables
MANUAL_TABLE_DESCRIPTIONS = {
    "draft_history": "Contains historical draft information for NHL players. Specifically, includes draftYear, selectableRounds in that draft, round, pickInRound, overallPick, teamID, teamPickHistory, firstName, lastName, position, country, height, weight, amateurLeague, amateurClubName, draftID, and playerId.",

    "draft_rankings": "Contains draft rankings for NHL players, including player_id, draft year, and ranking.",

    "goalies_gamebygame_stats": "Detailed game-by-game statistics for goalies including saves, save percentage, goals against, etc.",

    "goalie_past_season_leaders": "Contains records for leading goalies from past seasons by various statistical categories.",

    "league_pages": "Contains information about NHL league pages.",

    "nhl_contracts": "Contains information about NHL player contracts.",

    "nhl_EOY_standings": "Contains end-of-year standings for NHL teams.",

    "nhl_EOY_team_stats": "Contains end-of-year team statistics for NHL teams.",

    "nhl_games": "Information about NHL games including game_id, home team, away team, score, date, etc.",

    "nhl_players": "Contains basic information about NHL players including player_id, first name, last name, position, birth country, etc.",

    "nhl_plays": "Contains information about NHL plays including play_id, game_id, player_id, etc.",

    "nhl_rosters": "Contains one row per player per game - contains the gameID, teamID, playerID, sweaterNumber, and positionCode.",

    "nhl_shifts": "Contains information about NHL player shifts during games.",

    "nhl_teams": "Contains information about NHL teams including id, name, division, conference, etc.",

    "nhl_transactions": "Contains information about NHL player transactions.",

    "non_nhl_teams": "Contains information about non-NHL teams.",

    "player_last_5_games": "Contains recent performance data from a player's last 5 games including goals, assists, points, etc.",

    "player_season_stats": "Contains season-by-season statistics for players including goals, assists, points, etc. Linked to players via player_id.",

    "playoff_results": "Contains results of NHL playoff games.",

    "season_awards": "Contains information about NHL season awards.",

    "skaters_gamebygame_stats": "Detailed game-by-game statistics for skaters (non-goalies) including goals, assists, points, time on ice, etc.",

    "skater_past_season_leaders": "Contains records for leading skaters from past seasons by various statistical categories.",

    "standings_backup": "Backup of NHL standings data.",

    "stats_backup": "Backup of NHL statistics data.",

    "team_latest_stats": "Contains latest statistics for NHL teams.",

    "team_overall_stats_by_season": "Contains overall statistics for NHL teams by season.",

    "team_prospects": "Contains information about NHL team prospects.",

    "team_season_rosters": "Contains season rosters for NHL teams.",

    "team_season_stats": "Contains season statistics for NHL teams.",
}

# Get actual tables and their structure
ACTUAL_TABLES = get_actual_tables()
TABLES_STRUCTURE = get_real_tables_with_structure()

# Generate descriptions for tables with primary keys mentioned
def generate_table_descriptions():
    descriptions = {}
    
    # First add manual descriptions
    descriptions.update(MANUAL_TABLE_DESCRIPTIONS)
    
    # Then add descriptions for any remaining tables
    for table_name, info in TABLES_STRUCTURE.items():
        if table_name not in descriptions:
            pk_info = ""
            if info["primary_keys"]:
                pk_info = f" Primary key(s): {', '.join(info['primary_keys'])}"
                
            cols = [col["name"] for col in info["columns"]]
            col_summary = ", ".join(cols[:5])
            if len(cols) > 5:
                col_summary += f", ... ({len(cols)-5} more)"
                
            # Create description based on table name patterns
            if "player" in table_name.lower():
                descriptions[table_name] = f"Player data table containing: {col_summary}.{pk_info}"
            elif "team" in table_name.lower():
                descriptions[table_name] = f"Team data table containing: {col_summary}.{pk_info}"
            elif "game" in table_name.lower():
                descriptions[table_name] = f"Game data table containing: {col_summary}.{pk_info}"
            elif "stat" in table_name.lower():
                descriptions[table_name] = f"Statistics table containing: {col_summary}.{pk_info}"
            else:
                descriptions[table_name] = f"Table containing: {col_summary}.{pk_info}"
                
    return descriptions

TABLE_DESCRIPTIONS = generate_table_descriptions()

# Format table list with descriptions
def format_table_list_with_descriptions():
    table_list = []
    for table in ACTUAL_TABLES:
        desc = TABLE_DESCRIPTIONS.get(table, "No description available")
        table_list.append(f"{table}: {desc}")
    return "\n".join(table_list)

# Format detailed schema for specific tables
def format_detailed_schema_for_tables(table_names):
    schema_text = []
    
    for table_name in table_names:
        if table_name in TABLES_STRUCTURE:
            # Add table name and description
            desc = TABLE_DESCRIPTIONS.get(table_name, "No description available")
            schema_text.append(f"TABLE: {table_name}\nDESCRIPTION: {desc}\nCOLUMNS:")
            
            # Add column details
            for col in TABLES_STRUCTURE[table_name]["columns"]:
                pk_marker = " [PRIMARY KEY]" if col["name"] in TABLES_STRUCTURE[table_name]["primary_keys"] else ""
                schema_text.append(f"  - {col['name']} ({col['type']}){pk_marker}")
            
            # Add a separator
            schema_text.append("")
            
    return "\n".join(schema_text)

# Get tables that might contain goals or player stats based on column names
def find_tables_with_goals():
    goal_tables = []
    
    for table_name, info in TABLES_STRUCTURE.items():
        column_names = [col["name"].lower() for col in info["columns"]]
        
        if any(("goal" in col or "goals" in col) for col in column_names):
            goal_tables.append(table_name)
            
    return goal_tables

# Pre-process SQL results to add formatted links
def process_results_with_links(results):
    """Pre-process SQL results to add properly formatted links for players and teams"""
    if not results or not isinstance(results, list) or len(results) == 0:
        return results
    
    processed_results = []
    for row in results:
        processed_row = row.copy()  # Make a copy to avoid modifying the original
        
        # Process player ID links
        player_id = None
        player_name = ""
        
        # Look for player IDs in the row with various possible column names
        for key in ['playerId', 'playerID', 'player_id']:
            if key in row and row[key]:
                player_id = row[key]
                # Try to construct player name from first and last name fields
                if 'firstName' in row and 'lastName' in row:
                    player_name = f"{row['firstName']} {row['lastName']}".strip()
                elif 'first_name' in row and 'last_name' in row:
                    player_name = f"{row['first_name']} {row['last_name']}".strip()
                elif 'playerName' in row:
                    player_name = row['playerName']
                elif 'player_name' in row:
                    player_name = row['player_name']
                else:
                    player_name = f"Player {player_id}"
                break
        
        # If we found a player ID, add a formatted link
        if player_id and str(player_id).isdigit():
            player_link = f"[{player_name}](https://connoryoung.com/player_details.php?player_id={player_id})"
            processed_row['player_link'] = player_link
        
        # Process team ID links
        team_id = None
        team_name = ""
        
        # Look for team IDs in the row with various possible column names
        for key in ['teamId', 'teamID', 'team_id']:
            if key in row and row[key]:
                team_id = row[key]
                # Try to get team name
                if 'teamName' in row:
                    team_name = row['teamName']
                elif 'team_name' in row:
                    team_name = row['team_name']
                elif 'teamCommonName' in row:
                    team_name = row['teamCommonName']
                else:
                    team_name = f"Team {team_id}"
                break
        
        # If we found a team ID, add a formatted link
        if team_id and str(team_id).isdigit():
            team_link = f"[{team_name}](https://connoryoung.com/team_details.php?team_id={team_id})"
            processed_row['team_link'] = team_link
        
        processed_results.append(processed_row)
    
    return processed_results

# Helper function to replace placeholders in the final answer
def fix_links_in_answer(answer):
    """Replace any placeholder links with proper format"""
    # Fix player links
    player_pattern = r'\[([^\]]+)\]\(https://connoryoung\.com/player_details\.php\?player_id=(?:<[^>]+>|%3C[^%]+%3E)\)'
    player_fixed = re.sub(player_pattern, r'the player \1', answer)
    
    # Fix team links
    team_pattern = r'\[([^\]]+)\]\(https://connoryoung\.com/team_details\.php\?team_id=(?:<[^>]+>|%3C[^%]+%3E)\)'
    team_fixed = re.sub(team_pattern, r'the team \1', player_fixed)
    
    return team_fixed

# ───────────────────────────────────────────────────────────
# 2. Set up LLM
# ───────────────────────────────────────────────────────────
llm = ChatOllama(model="mistral", temperature=0)

# ───────────────────────────────────────────────────────────
# 3. FastAPI – tiny chat API
# ───────────────────────────────────────────────────────────
app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],  
    allow_headers=["*"],
)

class Question(BaseModel):
    question: str

@app.post("/ask")
async def ask(q: Question):
    """Answer questions about the NHL database"""
    try:
        print("► USER:", q.question)
        
        # Find potentially relevant tables based on the question
        question_lower = q.question.lower()
        potential_tables = []

        # EXPLICIT PATTERN MATCHING FOR COMMON QUESTION TYPES
        if "draft" in question_lower or "pick" in question_lower:
            # Draft questions should ALWAYS use draft_history
            potential_tables = ['draft_history']
            # Add additional tables only if needed
            if "player" in question_lower:
                potential_tables.append('nhl_players')
            if "team" in question_lower:
                potential_tables.append('nhl_teams')
            print("► DRAFT QUESTION DETECTED - Using draft_history table")
                
        elif "goal" in question_lower or "scoring" in question_lower or "score" in question_lower:
            potential_tables = find_tables_with_goals()
            if "player" in question_lower:
                potential_tables = [t for t in potential_tables if "player" in t.lower() or "skater" in t.lower()]
            print("► GOAL/SCORING QUESTION DETECTED")
                
        elif "player" in question_lower:
            potential_tables = [t for t in ACTUAL_TABLES if "player" in t.lower() or "skater" in t.lower()]
            print("► PLAYER QUESTION DETECTED")
                
        elif "team" in question_lower:
            potential_tables = [t for t in ACTUAL_TABLES if "team" in t.lower()]
            print("► TEAM QUESTION DETECTED")
                
        elif "game" in question_lower:
            potential_tables = [t for t in ACTUAL_TABLES if "game" in t.lower()]
            print("► GAME QUESTION DETECTED")
                
        else:
            # If no specific keywords, include common tables
            potential_tables = [t for t in ACTUAL_TABLES if any(key in t.lower() for key in ["player", "team", "game", "stat"])]
            print("► GENERAL QUESTION - Using common tables")
                
        # Ensure we have at least some tables and they exist
        potential_tables = [t for t in potential_tables if t in ACTUAL_TABLES]
        if not potential_tables:
            potential_tables = ACTUAL_TABLES[:10]  # Use first 10 tables as default
                
        print(f"► POTENTIAL TABLES: {potential_tables}")
        
        # Format schema for these tables
        detailed_schema = format_detailed_schema_for_tables(potential_tables)
        
        # Step 1: Generate SQL query using LLM with detailed schema
        sql_generation_prompt = ChatPromptTemplate.from_template(
            """You are writing an SQL query to answer a question about NHL data.

QUESTION: {question}

IMPORTANT CONTEXT:
- For draft questions, use DRAFT_HISTORY table
- For goal/scoring stats, use PLAYER_SEASON_STATS or SKATERS_GAMEBYGAME_STATS
- For player information, use NHL_PLAYERS
- For team information, use NHL_TEAMS

USE ONLY THE FOLLOWING TABLES (these are the actual tables in the database):
{table_list}

DETAILED SCHEMA (column names and types):
{detailed_schema}

Rules:
1. ONLY use tables and columns that are explicitly listed above
2. DO NOT invent or guess table or column names
3. Use table aliases (e.g., p for players) and qualify all column names with table aliases
4. Be extremely precise about column names - they must match exactly what's in the schema
5. Only SELECT columns that actually exist in the tables you're using
6. Draft questions MUST use draft_history table
7. Career player stats questions MUST use nhl_players table
8. When using the nhl_teams table, the teams are identified by 'id', not 'team_id'

SQL QUERY (write ONLY the SQL query, no explanations):
"""
        )
        
        sql_query = llm.invoke(
            sql_generation_prompt.format(
                question=q.question,
                table_list="\n".join(potential_tables),
                detailed_schema=detailed_schema
            )
        ).content.strip()
        
        # Clean up the query
        sql_query = sql_query.replace("```sql", "").replace("```", "").strip()
        print("► SQL QUERY:", sql_query)
        
        # Step 2: Execute the query with proper connection handling
        MAX_RETRIES = 2
        retries = 0
        last_error = None
        
        while retries <= MAX_RETRIES:
            try:
                # Execute the query
                sql_result = execute_query(sql_query)
                
                # Check if result is iterable
                if not isinstance(sql_result, list):
                    sql_result = []  # Convert to empty list if not iterable
                    
                # Print first few results
                preview = sql_result[:3] if len(sql_result) > 3 else sql_result
                print(f"► QUERY RESULT ({len(sql_result)} rows):", preview)
                break
                
            except Exception as sql_error:
                print(f"SQL Error: {str(sql_error)}")
                last_error = sql_error
                retries += 1
                
                if retries > MAX_RETRIES:
                    # If all retries failed, provide an error response
                    error_msg = f"I couldn't answer your question due to a database error: {str(last_error)}"
                    return {
                        "answer": error_msg,
                        "answer_html": markdown.markdown(error_msg),
                        "sql_query": sql_query,
                        "error": True
                    }
                
                # Try to recover with error feedback
                correction_prompt = ChatPromptTemplate.from_template(
                    """Your SQL query failed:
{sql_query}

Error: {error}

AVAILABLE TABLES:
{table_list}

DETAILED SCHEMA:
{detailed_schema}

Fix the SQL query to work with the correct schema and tables.
Double-check that all column names exist in the tables you're using.
Only return the fixed SQL query with no explanations or markdown.
"""
                )
                
                corrected_sql = llm.invoke(
                    correction_prompt.format(
                        sql_query=sql_query,
                        error=str(sql_error),
                        table_list="\n".join(potential_tables),
                        detailed_schema=detailed_schema
                    )
                ).content.strip().replace("```sql", "").replace("```", "").strip()
                
                print(f"► CORRECTED SQL QUERY (attempt {retries}):", corrected_sql)
                sql_query = corrected_sql
                time.sleep(1)  # Small delay between retries
        
        # PRE-PROCESS results to add properly formatted links
        processed_results = process_results_with_links(sql_result)
        
        # Step 3: Format the answer with pre-processed links
        answer_prompt = ChatPromptTemplate.from_template(
            """Based on the SQL query and its results, answer the user's question about NHL data.

I've already created proper links for players and teams in the results.
- If you see a 'player_link' field, use that EXACT value in your answer for player links
- If you see a 'team_link' field, use that EXACT value in your answer for team links

DO NOT try to create your own links - use the pre-made links provided in the results.

Question: {question}
SQL Query: {sql_query}
SQL Result: {processed_results}

Your answer in natural language. If links are available in the results, include them exactly as provided:"""
        )
        
        # Limit the size of processed results if it's very large
        processed_results_str = "[]"  # Default empty result
        try:
            if processed_results:
                if len(processed_results) > 10:
                    # For large results, limit to first 10 rows
                    processed_results_str = json.dumps(processed_results[:10], default=str) + f"... (showing 10 of {len(processed_results)} results)"
                else:
                    processed_results_str = json.dumps(processed_results, default=str)
        except Exception as e:
            print(f"Error formatting results: {e}")
            processed_results_str = f"Error formatting results: {str(e)}"
        
        final_answer = llm.invoke(
            answer_prompt.format(
                question=q.question,
                sql_query=sql_query,
                processed_results=processed_results_str
            )
        ).content
        
        # Fix any remaining placeholder links
        final_answer = fix_links_in_answer(final_answer)
        
        print("■ ANSWER:", final_answer)
        
        # Convert markdown to HTML for UI rendering
        html_content = markdown.markdown(final_answer)
        
        return {
            "answer": final_answer,
            "answer_html": html_content,
            "sql_query": sql_query
        }
    except Exception as e:
        print("!! ERROR:", str(e))
        traceback.print_exc()  # Print full stack trace
        return {
            "error": str(e),
            "answer": f"I'm sorry, I encountered an error: {str(e)}",
            "answer_html": f"I'm sorry, I encountered an error: {str(e)}"
        }

# ───────────────────────────────────────────────────────────
# 4. Dev entry-point
# ───────────────────────────────────────────────────────────
if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=True)