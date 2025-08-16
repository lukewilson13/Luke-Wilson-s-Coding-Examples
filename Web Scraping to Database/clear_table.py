import mysql.connector # type: ignore

tables = ['games_played', 'passing_stats', 'receiving_stats', 'rushing_stats', 'player_numbers', 'player_profile']


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


# clears whatever is currently in player_profiles DB
try:
    with conn.cursor() as cursor:
        # cursor.execute("SELECT pid FROM player_profile WHERE first_name = 'Trey'")
        # result = cursor.fetchall()
        # print(result)
        for table in tables:
            cursor.execute("DELETE FROM {}".format(table))
            print("Cleared ", table, " table")
            conn.commit()
except Exception as e:
    print("Error clearing table:", e)