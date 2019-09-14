Exams
============

This platform was developed as an e-assesment platform for Universities or other parties. 
It provides the ability to organize your users by role and authorize actions to them.

* **Admins** manage the Lessons and the users of the platform.
* **Professors** subscribe to lessons creating and publishing tests for students.
* **Students** subscribe to lessons and participate on the tests.


## Running the Project


### Prerequisites

**Global**
* MySQL
* Firebase Project

**Dockerized**
* docker
* bash

**Non-dockerized**
* PHP 5.6
* Laravel 5.4
* composer

#### Setup 

After cloning the project create a new `.env` file `cp .env.example .env` and update the values mentioned below.

* Create the database mentioned in `.env`
    ```
       DB_HOST=your_database_pub_host
       DB_DATABASE=your_database_name
       DB_USERNAME=your_database_user
       DB_PASSWORD=your_database_pass
    ```
* Setup Firebase
  - Create a [firebase](https://console.firebase.google.com) project
  - Download the adminsdk auth json and store it in `resources/external/` directory.
  
  <figure class="video_container">
    <video controls="true" allowfullscreen="true">
      <source src="/guides/firebase/admin-sdk.mp4" type="video/mp4">
    </video>
  </figure>
  
  - Define the name of the file in your `.env` with the key `FIREBASE_AUTH_FILE`
  - Define the name of your firebase url in the key `FIREBASE_DB_URL`
  - Set up `MIX_*` values as well for the frontend's connection to realtime events.
    ```
        MIX_FIREBASE_API_KEY=Web API Key from 'Project Settings > General'
        MIX_FIREBASE_AUTH_DOMAIN='your_firebase_identifier.firebaseapp.com'
        MIX_FIREBASE_DATABASE_URL='https://your_firebase_identifier.firebaseio.com'
        MIX_FIREBASE_PROJECT_ID='your_firebase_identifier'
        MIX_FIREBASE_STORAGE_BUCKET='your_firebase_identifier.appspot.com'
    ```
  
**dockerized**
* Run `./docker.sh dev fresh` to create a fresh instance of the project. You can also pass an `EXPOSE_PORT` parameter to change default expose port.

**Non-dockerized**
* Initiallize `APP_KEY` WITH `php artisan key:generate`
* Install project's php dependencies `composer install`
* Give permissions `chmod -R 777 storage && chmod -R 777 bootstrap/cache`

If your database is empty you will need to run the existing migrations to setup db tables 
- **dockerized**:`./docker.sh dev artisan migrate`
- **non-dockerized**:`php artisan migrate`
  
#### Development

Run `npm install` to install webpack dependencies and track your changes on `resources` files with `npm run watch-poll`.


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
