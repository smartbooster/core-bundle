CHANGELOG for 1.x
===================
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
