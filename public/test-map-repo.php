<?php

declare(strict_types=1);

require __DIR__ . '/../app/repositories/MapRepository.php';

$repo = new MapRepository();

echo '<pre>';

echo "TOTALURI PE JUDETE, 2024:\n";
print_r(array_slice($repo->getCountyTotalsByYear(2024), 0, 10));

echo "\nTOTALURI PE JUDETE, 2024, MOTORINA:\n";
print_r(array_slice($repo->getCountyTotalsByYearAndFuelType(2024, 'MOTORINA'), 0, 10));

echo "\nTOTALURI PE JUDETE, 2024, AUTOBUZ:\n";
print_r(array_slice($repo->getCountyTotalsByYearAndNationalCategory(2024, 'AUTOBUZ'), 0, 10));

echo "\nTOTALURI FILTRATE, 2024, MOTORINA + AUTOBUZ:\n";
print_r(array_slice($repo->getCountyTotalsFiltered(2024, 'MOTORINA', 'AUTOBUZ'), 0, 10));

echo '</pre>';