<?php

require __DIR__.'/../../vendor/autoload.php';

use App\Support\DocumentActivityYear;

$cases = [
    ['2022-12-31', '2026-01-01', 2022],
    ['2022-12-31T23:00:00-08:00', null, 2022],
    [null, '2026-01-01T00:00:00Z', 2026],
    [new DateTimeImmutable('2024-02-29'), null, 2024],
    ['0005-12-02', '2026-01-01', null],
    ['0003-03-21', '2026-01-01', null],
    ['0000-00-00', null, null],
    ['2025-02-30', null, null],
    ['invalid', '2026-01-01', null],
    [null, null, null],
];
foreach ($cases as [$approved, $created, $expected]) {
    if (DocumentActivityYear::fromDates($approved, $created) !== $expected) {
        throw new RuntimeException('Incorrect document activity year.');
    }
}
echo "PASS: valid approval dates, upload fallback, timezone boundaries, and invalid dates\n";
