#!/usr/bin/env bash

set -euo pipefail

project_dir="/home/almalinux/projects/real-estate-laravel"
cd "$project_dir"

echo "[$(date -u +%FT%TZ)] real-estate refactor background verification started"
echo "[$(date -u +%FT%TZ)] branch: $(git branch --show-current)"

composer install --no-interaction --no-progress --no-scripts
composer validate --no-check-publish
vendor/bin/pint --test modules tests

vendor/bin/pest \
    tests/Feature/RealEstatePropertyActionsTest.php \
    tests/Feature/RealEstatePartyActionsTest.php \
    tests/Feature/RealEstateCoreActionsTest.php \
    tests/Feature/RealEstateMediaDocumentsTest.php \
    tests/Feature/RealEstateValuationsTest.php \
    tests/Feature/RealEstateInstructionsTest.php \
    tests/Feature/RealEstateListingsTest.php \
    tests/Feature/RealEstateMatchingTest.php \
    tests/Feature/RealEstateViewingsTest.php \
    tests/Feature/RealEstateOffersTest.php \
    tests/Feature/RealEstateSalesProgressionTest.php \
    tests/Feature/RealEstateMarketingTest.php \
    tests/Feature/RealEstatePortalsReportingTest.php \
    tests/Feature/RealEstatePortalIntegrationsTest.php \
    tests/Architecture/ModuleBoundariesTest.php \
    tests/Unit/CanonicalModuleDiscoveryTest.php \
    --no-coverage

git diff --check
git status --short --branch
echo "[$(date -u +%FT%TZ)] real-estate refactor background verification finished"
