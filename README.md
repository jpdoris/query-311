# query-311

This is a simple command line tool built in PHP to query the SeeClickFix open311 API.

## Prerequisites:
Requires PHP 7+ CLI
Testing requires PHPUnit and Guzzle which are packaged via Composer.
Once the files are in place, running composer install should have you up and running.

## Usage:
php 311.php {latitude} {longitude} {fields}
The 'latitude' and 'longitude' parameters are required. The 'fields' parameter is optional. It expects 
a comma-separated list of field names you wish to query.  By default, the response will record
the 'service_request_id', 'description', and 'service_name' fields.  Returned data will be stored in a file
called 311.csv in the local working directory, assuming the user has write permission.

## Notes:
I included a basic unit test script to give an idea of one way that I might go about testing this tool.
Tests are executed by running ./vendor/bin/phpunit from the command line.  Again, this is just a simple demo.
With more time, ideally there would be more tests that specifically check for things like bad user input, 
error responses, timeouts, etc.