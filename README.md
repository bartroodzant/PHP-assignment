# Prime number CLI

This CLI application calculates prime numbers between 0 and a given range, prints the roman 
literals of these prime numbers to the console and saves these in a XML file. After which 
this XML file can be imported into a SQLite database.


## Getting Started

These instructions will get you a copy of the project up and running on your local machine.

### Prerequisites

The following software must be installed on your machine to run this application

- PHP 7.0 or higher
- Composer
- SQLite

### Installing

First clone the repository.

```
git clone https://github.com/bartroodzant/PHP-assignment.git
```

Go into the repository.

```
cd PHP-assignment
```

run composer install.

```
composer install
```

#### Using the application

##### All commands

Returns a list of all the available commands.

```
php PrimeNumberCommands list
```

##### Calculate command

Calculates prime numbers between 0 and the given range.

```
php PrimeNumberCommands calculate <range> <file name>
```

*The range must be between 1 and 2097152*

##### Export command

Exports the XML file from the calculate command to a SQLite database.

```
php PrimeNumberCommands export <xml file> <database name>
```

##### Help
If you need help with the command just add --help after a command. For example:
```
php PrimeNumberCommands calculate --help
```
