# Welcome to jFramework v2!

This is a Easy to use, lightweight PHP framework, have built-in:

* PHP **5.3+** Compatible **(Compatible with PHP7!)**
* Open-Source Project
* Documented using PHPDoc
* Object-oriented programming.
* MVC Framework
* [PSR-1,2,3,4,6,7,11,13,15,16][] Standards Ready!
* Database Manager
* Image Handler
* Exception Handler
* Captcha Generator
* Cron Job friendly _(crontab / Windows Task Scheduler)_
* PHPMAILER (Send mail using SMTP)
* Automatic Session Cleaner (Cronjob)
* Tools (tons of useful functions)
* Licensed under BSD 3-Clause License

[PSR-1,2,3,4,6,7,11,13,15,16]: <http://www.php-fig.org/psr/>

Folder tree
---
  | Folder | file / folder | description                                                                    |
  | :---   |     :---:     | :---                                                                           |
  | config | app.ini       | Configure the App by changing the parameters.                                  |
  | config | core.ini      | Internal use only!                                                             |
  | config | database.ini  | Define your database configuration like hostname, database, user and password. |
  | config | folders.ini   | Define the folder structure, If you don't like the default.                    |
  | config | mail.ini      | Define the mail settings.                                                      |
  | config | php.ini       | Define your custom PHP settings like the global php.ini, but set on runtime.   |
  | config | router.ini    | Define custom routes.                                                          |
  | app    | controller/   | Controllers here.                                                              |
  | app    | model/        | Dtabase models(tables).                                                        |
  | app    | view/         | Views / templates / layouts.                                                   |
  | data   | cache/        | Cache files about tables and etc.                                              |
  | data   | certificates/ | SSL/TLS certificates for curl and others.                                      |
  | data   | fonts/        | Fonts to be used on text on Image generation.                                  |
  | data   | logs/         | All kinds of logs.                                                             |
  | data   | session/      | PHP Session files.                                                             |
  | data   | sql/          | SQL files for setups and etc                                                   |
