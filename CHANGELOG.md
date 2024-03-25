CHANGELOG for 1.x
===================
## v1.1.0 - (2024-03-25)
### Added
- new `ArrayUtils` methods : `checkIssetKeys`, `trimExplode`, `removeEmpty`, `filterByPattern`, `flatToMap`
- new `MathUtils` methods : `calculateAverage`, `calculateDivision`
- new `DateUtils` methods : `getFirstDayYearFromDateTime`, `getLastDayMonthFromDateTime`, `getMonthsBetweenDateTimes`, `getDateTimeMonth`, `getDateTimeYear`, `getNbOfWorkingDaysBetweenDateTimes`, `getDateTimeFromMonthYear`, `getNbDayBetweenDateTimes`, `isNighttime`, `getLastDayMonth`, `getLastDayPreviousMonthFromDateTime`, `getFirstDayNextMonthFromDateTime`, `getFirstDayMonth`, `getNextBirthdayDateTime`, `getFormattedLongMonth`, `getFormattedLongMonthYears`, `getFormattedShortMonthYears`, `addDays`, `subDays`, `addMonths`, `subMonths`, `addYears`, `subYears`

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
