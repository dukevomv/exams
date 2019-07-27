Exams
============

This platform was developed as an e-assesment platform for Universities or other parties. 
It provides the ability to organize your users by role and authorize actions to them.

* **Admins** manage the Lessons and the users of the platform.
* **Professors** subscribe to lessons creating and publishing tests for students.
* **Students** subscribe to lessons and participate on the tests.


## Running the Project

#### Prerequisites
* PHP 5.6
* composer
* Laravel 5.4
* MySQL

#### Setup 

After cloning the project 
* Create a new `.env` file `cp .env.example .env` and update the required values.
* Initiallize `APP_KEY` WITH `php artisan key:generate`
* Install project's php dependencies `composer install`
* Give permissions `chmod -R 777 storage && chmod -R 777 bootstrap/cache`
* Create the database mentioned in `.env`
* Setup db tables `php artisan migrate`
* Setup realtime with firebase
  - Create a firebase project
  - Include it's keys in your `.env`
  - Download the adminsdk auth json and store it in `resources/external/` directory
  - Define the name of the file in your `.env` with the key `FIREBASE_AUTH_FILE`
  - Set up `MIX_*` values as well for the frontend's connection to realtime events.
  
#### Development

Run `npm install` to install webpack dependencies and track your changes on `resources` files with `npm run watch-poll`.

Run `php artisan serve` and visit the localhost url shown in the console.

## Architecture & Framework

This project is created in [Laravel](https://laravel.com/docs) framework.

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb combination of simplicity, elegance, and innovation give you tools you need to build any application with which you are tasked.

## License

Open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
