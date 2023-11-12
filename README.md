# 🎲 Dice Game

## Description
Dice Game application developed as an REST API with Laravel and tested with Postman. 
The game consists of rolling two dice and winning the round if the sum of the two dice is equal to 7.

## API endpoints

- POST /players : create a player
- PUT /players/{id} : edit player's name
- POST /players/{id}/games/ : a particular player rolls the dices
- DELETE /players/{id}/games: delete a player's games
- GET /players: returns the list of players and its average wins rate 
- GET /players/{id}/games: returns the list of games of a particular player
- GET /players/ranking: returns the ranking
- GET /players/ranking/loser: returns the player with lowest wins rate
- GET /players/ranking/winner: returns the player with highest wins rate
- DELETE /players/{id}: delete a player

### Roles
There are two types of users in this application:

- Players: created by default.
- Administrators: defined in the database.

## Technologies Used

The application has been developed using the following technologies and tools:

- **Laravel**: PHP framework that provides a robust and efficient structure for web application development.
- **Tailwind CSS**: Design framework that facilitates the creation of attractive and responsive interfaces.
- **Laravel Breeze**: Built-in authentication system in Laravel that streamlines the user registration and authentication process.
- **MySQL**: Relational database management system used to store the application data.
- **PHP Unit**: Testing is performed using PHPUnit, a PHP unit testing tool.
- **Laravel Passport**: Authentication is done using Laravel Passport, enabling token-based authentication.

## Installation and Configuration

To run the application on your local environment, follow these steps:

1. Clone the repository from GitHub: `git clone <REPOSITORY_URL>`
2. Install project dependencies: `composer install`
3. Install the npm dependencies: `npm install`
4. Copy the `.env.example` file and rename it to `.env`. Configure the environment variables, such as the database connection.
5. Generate a new application key: `php artisan key:generate`
6. Run the database migrations: `php artisan migrate`
7. Start the local server: `php artisan serve`
8. In a new terminal window, compile the frontend assets using Vite: `npm run dev`

Great! You can now access the application from your local browser using the URL provided by the local server.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

