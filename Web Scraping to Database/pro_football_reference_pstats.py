import pandas as pd # type: ignore
from bs4 import BeautifulSoup
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

import mysql.connector # type: ignore
from datetime import datetime
import random
import time
import re
from io import StringIO
# Set to current season

season = str(2024)

try:
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        db="fantasy_fortress",
    )
    print("Connection Success!")
except Exception as e:
    print("Failed to connect to database:", e)
    exit(1)

pd.set_option('display.max_columns', None)  # Show all columns
pd.set_option('display.max_rows', None)     # Show all rows
# pd.set_option('display.expand_frame_repr', False)  # Prevent line breaks in wide DataFrames

#!! DRIVER SETUP !!
def initialize_driver():
    chrome_options = Options()
    chrome_options.add_argument("--headless")  # Run in headless mode (no browser window)
    chrome_options.add_argument("--disable-logging")  # Disable logging
    chrome_options.add_argument("--log-level=3")  # Set logging level to suppress warnings and errors
    chrome_options.add_argument("--ignore-certificate-errors")  # Ignore SSL certificate errors
    driver = webdriver.Chrome(options=chrome_options)  # Ensure ChromeDriver is in PATH
    return driver


#!! FORMS PLAYER ID !!
def generate_player_id(first_name, last_name, college, birth_date):
    def normalize(value):
        if pd.isna(value) or value is None or str(value).strip() == '':
            return "unknown"
        return re.sub(r'\W+', '', str(value).strip().lower())

    # Normalize name, college
    first = normalize(first_name)
    last = normalize(last_name)
    college = normalize(college)
    birth_date = normalize(birth_date)

    player_id = f"{first}_{last}_{college}_{birth_date}"
    return player_id

# SCRAPES A PLAYER'S WEEKLY STATS
def player_gamelog_scrape(driver, conn, url, pid, pos):
    print("\n\nwelcome to the season_scrape function \n\n")

    html = driver.page_source
    soup = BeautifulSoup(html, 'html.parser')

    gamelog_table = soup.find('table', id='stats')
    gamelog_df = pd.read_html(StringIO(str(gamelog_table)))[0]
    print(gamelog_df)



# SCRAPE A PLAYER'S SEASON STATS
def player_seasons_scrape(driver, conn, url, pos, team, testing):
    if testing:
        print('Test run!')
    else:
        url = f'https://www.pro-football-reference.com{url}'
    print("Player URL: ", url)

    stats_exist = True

    # fetching the proper table based on position
    # rb tables are 'rushing_and_receiving'
    # wr/te/fb tables are 'receiving_and_rushing'
    # qb passing table is 'passing', but the also have 'rushing_and_receiving' for other stats
    if pos == 'RB':
        # visit page
        try:
            driver.get(url)
        except Exception as e:
            print(f"Timeout or page load failed for {url}: {e}")

        # check if they have a receiving_and_rushing table
        # if not, then they haven't played in a game and empty rows will be added later
        try:
            WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.ID, 'rushing_and_receiving'))
            )
        except Exception as e:
            print("No stats exist for the player")
            stats_exist = False

    elif pos == 'WR' or pos == 'TE' or pos=='FB':
        # visit page
        try:
            driver.get(url)
        except Exception as e:
            print(f"Timeout or page load failed for {url}: {e}")
        
        # check if they have a receiving_and_rushing table
        # if not, then they haven't played in a game and empty rows will be added later
        try:
            WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.ID, 'receiving_and_rushing'))
            )
        except Exception as e:
            # some players have a receiving_and_rushing div instead of a table, so check for that
            try:
                WebDriverWait(driver, 5).until(
                    EC.presence_of_element_located((By.ID, 'div_receiving_and_rushing')) # for darnell mooney for some reason
                ) 
            except: # they must not have receiving_and_rushing stats
                print("No stats exist for the player")
                stats_exist = False
    else: #quarterback
        try:
            driver.get(url)
        except Exception as e:
            print(f"Timeout or page load failed for {url}: {e}")
        try:
            WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.ID, 'rushing_and_receiving'))
            )
        except Exception as e:
            print("No stats exist for the player")
            stats_exist = False

        
    html = driver.page_source
    soup = BeautifulSoup(html, 'html.parser')
    
    team_abbrs_dict = {   
        "crd": "ARI",
        "atl": "ATL",
        "rav": "BAL",
        "buf": "BUF",
        "car": "CAR",
        "chi": "CHI",
        "cin": "CIN",
        "cle": "CLE",
        "dal": "DAL",
        "den": "DEN",
        "det": "DET",
        "gnb": "GB",
        "htx": "HOU",
        "clt": "IND",
        "jax": "JAX",
        "kan": "KC",
        "rai": "LV",
        "sdg": "LAC",
        "ram": "LAR",
        "mia": "MIA",
        "min": "MIN",
        "nwe": "NE",
        "nor": "NO",
        "nyg": "NYG",
        "nyj": "NYJ",
        "phi": "PHI",
        "pit": "PIT",
        "sfo": "SF",
        "sea": "SEA",
        "tam": "TB",
        "oti": "TEN",
        "was": "WAS"
    }

    try:
        meta = soup.find('div', id='meta').find_all('div')[1]
    except:
        meta = soup.find('div', id='meta').find_all('div')[0] # for some players there is only one 'meta' div
    name = meta.find('h1').find('span').text

    # shows games played for each season for each player
    games_table = soup.find('table', id='snap_counts')
    games_df = pd.read_html(StringIO(str(games_table)))[0]
    games_df.columns = [' '.join(col).strip() for col in games_df.columns.values]
    # print(games_df)
    rename_mapping = { # rename the columns
            'Unnamed: 0_level_0 Year': 'Year',
            'Unnamed: 2_level_0 Tm': 'Team',
            'Games G': 'G',
            'Off. Pct': 'Offensive_Pct'
    }
    games_df.rename(columns=rename_mapping, inplace=True)
    games_df = games_df[['Year', 'G', 'Offensive_Pct', 'Team']] # drop unwanted columns

    if not testing:
        with conn.cursor() as cursor:
            # fetching player pid
            select_pid_sql = "SELECT pid FROM player_profile WHERE first_name = %s AND last_name = %s"
            first_name, last_name = name.split(" ", 1)
            cursor.execute(select_pid_sql, (first_name.strip(), last_name.strip()))
            pid = cursor.fetchone()[0]
    else:
        first_name, last_name = name.split(" ", 1)
        pid = generate_player_id(first_name, last_name, 'college', 'bdate')
        with conn.cursor() as cursor:
                insert_for_test_sql = """
                INSERT INTO player_profile (pid, first_name, last_name, birth_date, height, weight, college, draft_round, draft_overall_pick)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
                """
                cursor.execute(insert_for_test_sql, (pid, first_name, last_name, 1/1/2000, "6'0", 200, "College", 1, 1))

    # inserting games played entry
    insert_gp_sql = "INSERT INTO games_played (pid, year, team_id, gp, snap_pct) VALUES (%s, %s, %s, %s, %s)"
    for index, row in games_df.iterrows():
        if row.Team not in team_abbrs_dict.values():
            continue
        try:
            with conn.cursor() as cursor:
                cursor.execute(insert_gp_sql, (pid, row.Year, team_abbrs_dict[team], row.G, row.Offensive_Pct))
            conn.commit()
            print("gp INSERT SUCCESSFUL FOR ", name)
        except Exception as e:
            print("Unsuccessful entry for games played ", e)

    player_number = soup.find_all('div', {'class': 'uni_holder pfr'})[0].find_all('text')[-1].text
    with conn.cursor() as cursor:
        cursor.execute("INSERT INTO player_numbers (pid, number) VALUES (%s, %s)", (pid, player_number))
    conn.commit()

    if stats_exist:
        # gets table for all positions that aren't QB
        if pos != 'QB':
            if pos =='RB':
                player_table = soup.find('table', id='rushing_and_receiving')
            else: # all other positions have a 'receiving and rushing' table instead
                player_table = soup.find('table', id='receiving_and_rushing')
                player_table = soup.find('div', id='div_receiving_and_rushing') # darnell mooney
            player_df = pd.read_html(StringIO(str(player_table)))[0]

            player_df.columns = [' '.join(col).strip() for col in player_df.columns.values]
            # print("original df: ",player_df) # prints original df

            # renaming unnamed columns
            rename_mapping = {
                'Unnamed: 0_level_0 Season': 'Season',
                'Unnamed: 2_level_0 Team': 'Team',
                'Unnamed: 5_level_0 G': 'Games',
                'Unnamed: 24_level_0 Fmb': 'Fumbles',
                'Unnamed: 25_level_0 Fmb': 'Fumbles',
                'Unnamed: 26_level_0 Fmb': 'Fumbles', # some quarterbacks - kyler
                'Unnamed: 27_level_0 Fmb': 'Fumbles',
                'Unnamed: 28_level_0 Fmb': 'Fumbles',
                'Unnamed: 29_level_0 Fmb': 'Fumbles', # players drafted after 2023 without rush attempts - MH Jr.
                'Unnamed: 30_level_0 Fmb': 'Fumbles',
                'Unnamed: 31_level_0 Fmb': 'Fumbles', # khadarel hodge
                'Unnamed: 32_level_0 Fmb': 'Fumbles', # most players
            }
            player_df.rename(columns=rename_mapping, inplace=True)

            # these don't exist on certain pages, so it is better to handle it with a conditional statement
            if 'Rushing Y/A' not in player_df.columns:   # if the row exists, then add it to the df.
                player_df.insert(5, 'Rushing Y/A', 0.0)

            if 'Receiving Y/Tgt' not in player_df.columns: # if the row exists, then add it to the df.
                player_df.insert(10, 'Receiving Y/Tgt', 0.0)
            # print(player_df)

            # drops unnecessary columns
            player_df = player_df[['Season', 'Team', 'Games',
                    'Rushing Att', 'Rushing Yds', 'Rushing Y/A', 'Rushing TD',
                    'Receiving Tgt', 'Receiving Rec',
                    'Receiving Yds', 'Receiving Y/Tgt', 'Receiving TD',
                    'Fumbles']]
                            
            player_df.fillna(0.0, inplace=True) # fills null cells with 0s

            # write the stats to the database
            with conn.cursor() as cursor:
                # establishing INSERT structure
                rushing_sql = '''INSERT INTO rushing_stats (pid, year, team_id, rushing_attempts, rushing_yards, yards_per_attempt, rushing_TDs, fumbles)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)'''
                
                receiving_sql = '''INSERT INTO receiving_stats (pid, year, team_id, receptions, targets, receiving_yards, yards_per_catch, receiving_TDs)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)'''
                
                for index, row in player_df.iterrows():
                    if row.Team in team_abbrs_dict.values() and row.Season != "0": # ensures only entries for teams get inserted (i.e. skips totals)
                        # writing rushing stats...
                        cursor.execute(rushing_sql, (
                            pid,
                            row.Season,
                            row.Team,
                            row['Rushing Att'],
                            row['Rushing Yds'],
                            row['Rushing Y/A'],
                            row['Rushing TD'],
                            row.Fumbles
                        ))

                        # writing receiving stats
                        cursor.execute(receiving_sql, (
                            pid,
                            row.Season,
                            row.Team,
                            row['Receiving Rec'],
                            row['Receiving Tgt'],
                            row['Receiving Yds'],
                            row['Receiving Y/Tgt'],
                            row['Receiving TD']                
                        ))
                    
                        conn.commit()
                        print("Rushing and receiving stats successfully written for ", name, " for the ", row.Season, "season!")

                        # writing receiving stats...

        else: # position == 'QB'
            # get dataframe
            player_table = soup.find('table', id='passing')
            player_df = pd.read_html(StringIO(str(player_table)))[0]
            rushing_table = soup.find('table', id='rushing_and_receiving')
            rushing_df = pd.read_html(StringIO(str(rushing_table)))[0]
            rushing_df.columns = [' '.join(col).strip() for col in rushing_df.columns.values]

            # rename columns
            # print(rushing_df) # prints original df
            player_df.fillna(0.0, inplace=True)
            rushing_df.fillna(0.0, inplace=True)

            rename_mapping = {
                    'Unnamed: 0_level_0 Season': 'Season',
                    'Unnamed: 2_level_0 Team': 'Team',
                    'Unnamed: 5_level_0 G': 'Games',
                    'Unnamed: 32_level_0 Fmb': 'Fumbles',
                    'Unnamed: 29_level_0 Fmb': 'Fumbles', # players drafted after 2023 without rush attempts - marvin harrison jr.
                    'Unnamed: 27_level_0 Fmb': 'Fumbles' # quarterbacks - kyler murray
            }
            
            rushing_df.rename(columns=rename_mapping, inplace=True)
            # print(rushing_df)

            # these don't exist on certain pages, so it is better to handle it with a conditional statement
            if 'Rushing Y/A' not in player_df.columns:   # if the row exists, then add it to the df.
                player_df.insert(5, 'Rushing Y/A', 0.0)

            with conn.cursor() as cursor:
                passing_sql = '''INSERT INTO passing_stats (pid, year, team_id, passing_attempts, completions, completion_pct, passing_yards, passing_TDs, ints, passer_rtg)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                    '''
                rushing_sql = '''INSERT INTO rushing_stats (pid, year, team_id, rushing_attempts, rushing_yards, yards_per_attempt, rushing_TDs, fumbles)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)'''
                for index, row in player_df.iterrows():
                    if row.Team in team_abbrs_dict.values():
                        cursor.execute(passing_sql, (pid, row['Season'], row['Team'], 
                            row['Att'], row['Cmp'], row['Cmp%'], row['Yds'], row['TD'], row['Int'], row['Rate'])
                        )
                for index, row in rushing_df.iterrows():
                    if row.Team in team_abbrs_dict.values():
                        cursor.execute(rushing_sql, (pid, row['Season'], row['Team'], row['Rushing Att'], 
                            row['Rushing Yds'], row['Rushing Y/A'], row['Rushing TD'], row['Fumbles'])
                        )
                    print("qb records insert successful")
                conn.commit()

    else: # handling for stats not existing
        if pos != 'QB':
            rushing_sql = '''INSERT INTO rushing_stats (pid, year, team_id, rushing_attempts, rushing_yards, yards_per_attempt, rushing_TDs, fumbles)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)'''
                    
            receiving_sql = '''INSERT INTO receiving_stats (pid, year, team_id, receptions, targets, receiving_yards, yards_per_catch, receiving_TDs)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)'''
            with conn.cursor() as cursor:
                for index, row in games_df.iterrows():
                    if row.Team not in team_abbrs_dict.values():
                        continue
                    cursor.execute(rushing_sql, (pid, row.Year, team_abbrs_dict[team], 0, 0, 0.0, 0, 0))
                    cursor.execute(receiving_sql, (pid, row.Year, team_abbrs_dict[team], 0, 0, 0, 0.0, 0))
                    print('empty set added for ', name, ' for ', row.Year)
            conn.commit()
        else: # for a qb without stats
            passing_sql = rushing_sql = '''INSERT INTO passing_stats (pid, year, team_id, passing_attempts, competions, completion_pct, passing_yards, passing_TDs, interceptions, passer_rtg)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s)'''
            with conn.cursor() as cursor:
                for index, row in games_df.iterrows():
                    if row.Team not in team_abbrs_dict.values():
                        continue
                    cursor.execute(rushing_sql, (pid, row.Year, team_abbrs_dict[team], 0, 0, 0.0, 0, 0, 0, 0.0))
                    print('empty set added for ', name, ' for ', row.Year)
            conn.commit()

    time.sleep(random.randint(3, 4))

    # !! CALLS season_scrape FUNCTION FOR THE PLAYER'S CAREER !!

    season_url = url[:-4] + '/gamelog/Career'
    print(season_url)
    # player_gamelog_scrape(driver, conn, season_url, pid, pos)


def main():
    team_limit = 0
    driver = initialize_driver()

    # aggregate data frames for stats
    nfl_df = pd.DataFrame()
    player_stats_df = pd.DataFrame()
    players_df = pd.DataFrame()

    # Team abbreviations used for pfr file paths
    pfr_team_abbrs = ['crd', 'atl', 'rav', 'buf', 'car', 'chi', 'cin', 'cle', 'dal', 'den', 'det', 'gnb', 'htx', 'clt', 'jax', 'kan', 
                'sdg', 'ram', 'rai', 'mia', 'min', 'nwe', 'nor', 'nyg', 'nyj', 'phi', 'pit', 'sea', 'sfo', 'tam', 'oti', 'was']

    try:
        for team in pfr_team_abbrs:
            team_limit +=1
            if team_limit > 2:
                break

            # URL for each team
            url = f'https://www.pro-football-reference.com/teams/{team}/{season}_roster.htm'
            print(url)

            # Wait for the page to load and get the HTML
            driver.get(url)
            WebDriverWait(driver, 10).until(
                EC.presence_of_element_located((By.ID, 'roster'))
            )

            html = driver.page_source
            soup = BeautifulSoup(html, 'html.parser')

            # fetch data for each player
            team_roster_df = pd.read_html(StringIO(str(soup)), attrs={'id':'roster'})[0]

            #!!!!!!!!!!!!!!!!!!!!!!!
            #!! DATA MANIPULATION !!
            team_roster_df = team_roster_df[team_roster_df['Pos'].isin(['RB', 'WR', 'QB', 'TE', 'FB'])]
            team_roster_df = team_roster_df.drop(['G', 'GS', 'AV'], axis=1) # gets rid of unnecessary columns
            team_roster_df.insert(loc=0, column="Team", value=team)
            team_roster_df[['First_Name', 'Last_Name']] = team_roster_df['Player'].str.split(' ', n=1, expand=True)
            team_roster_df = team_roster_df.drop(columns=['Player'])
            # fixing birthdate format
            team_roster_df['BirthDate'] = team_roster_df['BirthDate'].str.replace('/', '-')
            # Keep only the last college in the 'College/Univ' column and rename the column to 'College'
            team_roster_df['College'] = team_roster_df['College/Univ'].str.split(',').str[-1].str.strip()
            team_roster_df = team_roster_df.drop('College/Univ', axis=1)
            # Extract round and pick from the 'Drafted (tm/rnd/yr)' column
            team_roster_df[['Round', 'Pick']] = team_roster_df['Drafted (tm/rnd/yr)'].str.extract(r'/\s*(\d+)[a-z]{2}\s*/\s*(\d+)[a-z]{2}')
            team_roster_df[['Round', 'Pick']] = team_roster_df[['Round', 'Pick']].fillna('udfa')
            team_roster_df = team_roster_df.drop('Drafted (tm/rnd/yr)', axis=1)
            #!! DATA MANIPULATION !!
            #!!!!!!!!!!!!!!!!!!!!!!!

            # # add results to larger dataframe
            # nfl_df = pd.concat([nfl_df, team_roster_df], ignore_index=True)
            # print(nfl_df, '\n \n \n')

            with conn.cursor() as cursor:
                insert_sql = """
                INSERT INTO player_profile (pid, first_name, last_name, birth_date, height, weight, college, draft_round, draft_overall_pick)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
                """
                
                # adds an sql entry for each row in the roster DataFrame
                for index, row in team_roster_df.iterrows():
                    birth_date = datetime.strptime(row.BirthDate, '%m-%d-%Y')

                    cursor.execute(insert_sql, (
                        generate_player_id(row.First_Name, row.Last_Name, row.College, row.BirthDate),
                        row.First_Name, 
                        row.Last_Name, 
                        birth_date,
                        row.Ht,
                        row.Wt, 
                        row.College, 
                        row.Round, 
                        row.Pick
                    ))
                    
            conn.commit()
            print("Player Profiles inserted successfully.")
            #!!

            #!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            #!! PLAYER STATISTICS RETRIEVAL!!!!!

            # fetches the html content for all of the rows in the roster table
            roster_table = soup.find('table', id='roster') # holds the html content of the roster table
            # print(roster_table.prettify())
            rows = roster_table.find_all('tr') # Find all rows in the roster table
                
            # Process each row
            for row in rows:
                cells = row.find_all(['th', 'td'])  # Extract both <th> and <td> tags
                if cells:  # Process rows with data
                    pos = cells[3].text.strip()  # gets player position

                    # if they are a QB, RB, WR, or TE...
                    if pos in team_roster_df['Pos'].tolist():

                        player_link = cells[1].find('a')['href'] 

                        #THIS IS WHERE THE PLAYERS STATS GET SCRAPED
                        player_seasons_scrape(driver, conn, player_link, pos, team, False)
                        # player_df = player_seasons_scrape(driver, player_link)
                        # players_df = pd.concat([players_df, player_df], ignore_index=True)

                else:
                    print("No rows were returned")


            # Wait 5 seconds
            time.sleep(random.randint(4, 5))

    except KeyboardInterrupt:
        print("\nKeyboardInterrupt received. Shutting down gracefully.")
        driver.quit()
        conn.close()
        exit(0)

# for testing certain players
# player_url = 'https://www.pro-football-reference.com/players/M/MontDa01.htm'
# pos = 'RB'
# team_pff_format = 'det'
# driver = initialize_driver()
# player_seasons_scrape(driver, player_url, conn, pos, team_pff_format, True)

main()