
# eTrack

eTrack is a web application for tracking the academic progress of students who
are studying BTEC and Cambridge Technical qualifications.

## Features

- Manage courses, course units and unit assignments.
- Manage student groups in courses to organise different assignments, and
  course pathways.
- Organise courses into faculties.
- Assign tutors to units.
- Assign a course organiser to courses so that they can control the whole course.
- Set assignments -- define what criteria it covers, and what the deadline
  should be.
- Assign specific assignment deadlines for certain users if they have extenuating
  circumstances.
- Mark submitted assignments and grade the criteria they meet. Students can then
  see this in their profile.
- Automatic calculation of final/predicted course and unit grades based on what
  criteria has been met. Tutors can also set target grades.
- UCAS Tariff points are automatically calculated for Level 3 courses.
- All users are classed under roles to determine what privileges they have.

## Supported Courses

- BTEC Nationals
- BTEC Firsts
- OCR Cambridge Technicals

## Getting set-up locally

### Prerequisites

- Ruby >= 1.9.3
- Node.js
  - Gulp
  - Bower
- PHP >= 5.4
- Composer
- MySQL

### Creating the database and MySQL user

```
mysql> create database etrack;
mysql> grant all on `etrack`.* to etrack@localhost identified by 'etrack';
```

### Set-up the database

`php artisan migrate --seed`

This will also create an admin user account with the default credentials:

**Username:** `admin`

**Password:** `password`

### Building the assets

First make sure that Gulp, Bower and the Sass gem are installed on your machine.

```
npm install
bower install
gulp
```

### Run server

PHP comes with an embedded web server. This is the quickest way of running the application
locally without having to set-up a web server such as Apache or nginx.

`php artisan serve`

This will listen on localhost at port 8000 by default.

You may optionally add the `--host` and `--port` options to override this.


## License

eTrack is copyright to City College Plymouth. See the LICENSE file for further
details.
