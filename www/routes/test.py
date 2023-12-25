import mysql.connector
from mysql.connector import Error
import random
import string

def generate_random_code(length=6):
    characters = string.ascii_letters + string.digits
    return ''.join(random.choice(characters) for _ in range(length))

def generate_activation_codes(number_of_codes=100, code_length=6):
    return [generate_random_code(code_length) for _ in range(number_of_codes)]

def save_activation_codes_to_db(activation_codes):
    try:
        connection = mysql.connector.connect(
            host='ID367858_storystreak.db.webhosting.be',
            database='ID367858_storystreak',
            user='ID367858_storystreak',
            password='storystreakroot1'
        )

        if connection.is_connected():
            db_info = connection.get_server_info()
            print(f'Connected to MySQL Server version {db_info}')

            cursor = connection.cursor()

           

            # Insert codes into the 'codes' table
            for code in activation_codes:
                cursor.execute('INSERT INTO codes (code) VALUES (%s)', (code,))

            connection.commit()
            print('Codes inserted successfully.')

    except Error as e:
        print(f'Error: {e}')

    finally:
        if connection.is_connected():
            cursor.close()
            connection.close()
            print('MySQL connection closed.')

# Example usage:
number_of_codes = 100
code_length = 6

activation_codes = generate_activation_codes(number_of_codes, code_length)
save_activation_codes_to_db(activation_codes)
