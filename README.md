# Subscene Bot

[![license](https://img.shields.io/github/license/sh-sh-dev/subscene-bot.svg?style=flat-square)](https://github.com/sh-sh-dev/subscene-bot/blob/master/LICENSE)
[![HitCount](http://hits.dwyl.io/sh-sh-dev/subscene-bot.svg)](http://hits.dwyl.io/Naereen/badges)


A Telegram bot to access [subscene.com](https://subscene.com) subtitles using [subscene-api-php](https://github.com/nimah79/subscene-api-php)

## Demo

Visit [Subscene Robot](https://t.me/SubsceneRobot) on Telegram.

## Installation

* Clone the repository

```bash
git clone https://github.com/sh-sh-dev/subscene-bot.git && cd subscene-bot
```

* Import database

Import the `db.sql` file to your database server

## Configuration

You must fill 4 variables placed in `config.php` file:

* **timezone** - Your app timezone

* **token** - Telegram API token

* **dbConfiguration** - Database configuration (host, user, password and database name)

* **subscene** - Your subscene account email and password

## TODO

* Add other languages

## License

This project is licensed under the GPL License - see the LICENSE.md file for details
