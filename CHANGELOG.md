CHANGELOG for 1.x
===================
## v1.6.0 - (2024-06-12)
### Added
- `MarkdownUtils` to help formatting markdown content before being render as html

## v1.5.0 - (2024-06-10)
### Added
- `ProcessTrait::restartedAt` Used to know when we wanted to retry this process (creating a new dedicated process based on this one)
- `ApiCallTrait` add missing properties **rawContent**, **headers** and **contentTypeFormat** to be able to redo the api call with every orginal data 
- `ApiCallMonitor::restart` Method to recall an already monitor api call
- `smart_core` config to handle which API route is allowed to be restarted by the `ApiCallMonitor`

## v1.4.2 - (2024-06-05)
### Added
- `ApiCallMonitor::getProcessMonitor` getter to access the ProcessMonitor instance through it

### Changed
- `ApiCallMonitor::start` add **$flush** param to match `ProcessMonitor::start` definition
- `ApiCallMonitor::end` use Symfony Response const instead of hard coded value for the isSuccess check, also expand the range to >= 100 and < 400

## v1.4.1 - (2024-05-29)
### Fixed
- `DateUtils::millisecondsToString` int type cast when calling **secondsToString** to fix the deprecated: Implicit conversion from float

## v1.4.0 - (2024-05-06)
### Added
- `PhpFormatter` common date formatting to use on DateTime::format https://www.php.net/manual/en/datetime.format.php

## v1.3.0 - (2024-04-29)
### Added
- `ISO8601Formatter` for common date formatting
- `ProcessMonitor` Add functions to manage process log and print them out to the console

### Changed
- `ProcessMonitor::start` Add boolean flush param to better manage when the process should be flushed
- `ProcessMonitor::end` Handle passing process param as null 

## v1.2.4 - (2024-04-22)

**The process duration value is now calculated in milliseconds instead of seconds.**  
So update your databases values accordingly when updating to this version.  
We added new function to the interface to get the value in seconds if you don't care about milliseconds.

### Fixed
- `ProcessMonitor::end` duration calculation in milliseconds instead of seconds.

### Added
- `ProcessInterface::getDurationSeconds` + `ProcessInterface::getDurationSecondsAsString` to still manage process duration in seconds.
- `DateUtils::millisecondsToString` that adds up the number of milliseconds to the string, only if the value is less than a minute.

## v1.2.3 - (2024-04-18)
### Added
- `StringUtils::upperAccentuatedCharacter` + tests
- `StringUtils::lowerAccentuatedCharacter` + tests
- `StringUtils::formatLastName` + tests
- `StringUtils::formatFirstName` + tests
- `StringUtils::formatSpaceBetween` + tests

## v1.2.2 - (2024-04-02)
### Added
- Add mapping comment on EntityTrait to be compatible with doctrine annotations type
- `ApiCallMonitor` to manage start/end ApiCallInterface
- `ProcessInterface::addExceptionTraceData` function to store the exception trace

### Fixed
- `ProcessTrait::addLog` Switch adding log at the end instead of the beginning 

## v1.2.1 - (2024-03-27)
### Fixed

- `ArrayUtils::checkIssetKeys` : Modify method with not strict comparaison

## v1.2.0 - (2024-03-27)
### Added
- Common Entity Interface and Trait such as the `ProcessInterface` which we will use to monitor cron, api and file generation.
- `ProcessMonitor` to centralize process code management
- `CommandPoolHelper` service to fetch data about the project symfony commands (like getting all cron choices)
- `DateUtils::secondsToString` helper to convert seconds into a small summary string
- `IniOverrideConfig::initDefaultTimezoneForCli` helper to properly set the timezone when using date with PHP CLI on CleverCloud
- `ApiCallInterface` and trait to ease monitoring API calls 

### Fixed
- `RegexUtils::PHONE_PATTERN` remove wrong extra digit needed on foreign number 

## v1.1.0 - (2024-03-25)
### Added
- new `ArrayUtils` methods : `checkIssetKeys`, `trimExplode`, `removeEmpty`, `filterByPattern`, `flatToMap`
- new `MathUtils` methods : `calculateAverage`, `calculateDivision`
- new `DateUtils` methods : `getFirstDayYearFromDateTime`, `getLastDayMonthFromDateTime`, `getMonthsBetweenDateTimes`, `getDateTimeMonth`, `getDateTimeYear`, `getNbOfWorkingDaysBetweenDateTimes`, `getDateTimeFromMonthYear`, `getNbDayBetweenDateTimes`, `isNighttime`, `getLastDayPreviousMonthFromDateTime`, `getFirstDayNextMonthFromDateTime`, `getFirstDayMonth`, `getNextBirthdayDateTime`, `getFormattedLongMonth`, `getFormattedLongMonthYears`, `getFormattedShortMonthYears`, `addDays`, `subDays`, `addMonths`, `subMonths`, `addYears`, `subYears`

## v1.0.5 - (2024-03-15)
### Added
- `StringUtils::getEntityRoutePrefix` Add optional $context param to be more generic and being used on other domain/context.

## v1.0.4 - (2024-02-27)

### Added

- `MathUtils::convertCentsToEuro` : Convert cents to euro

## v1.0.3 - (2024-01-11)

### Fix

- `MathUtils::formatBytes` : Use 1000 instead of 1024 for calculate

## v1.0.2 - (2024-01-11)

### Added

- `MathUtils::formatBytes` : Convert and Transform byte into readable format

## v1.0.1 - (2024-01-05)

### Added

- `Fidry\AliceDataFixtures\Loader\PurgerLoader` service alias into `services.yaml`
- Documentation on `^/anonymous` path in `security.yaml`
