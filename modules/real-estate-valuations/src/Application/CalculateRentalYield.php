<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Validation\ValidationException;

final class CalculateRentalYield
{
    /** @return array<string, mixed> */
    public function handle(float $propertyValue, float $annualRentalIncome, float $annualExpenses = 0): array
    {
        if ($propertyValue <= 0 || $annualRentalIncome < 0 || $annualExpenses < 0) {
            throw ValidationException::withMessages(['rental_yield' => 'Property value, rental income, and expenses must be valid non-negative amounts.']);
        }

        $netAnnualIncome = $annualRentalIncome - $annualExpenses;

        return [
            'estimated' => true,
            'property_value' => round($propertyValue, 2),
            'annual_rental_income' => round($annualRentalIncome, 2),
            'annual_expenses' => round($annualExpenses, 2),
            'net_annual_income' => round($netAnnualIncome, 2),
            'gross_yield' => round($annualRentalIncome / $propertyValue * 100, 2),
            'net_yield' => round($netAnnualIncome / $propertyValue * 100, 2),
            'expense_ratio' => round($annualExpenses / $propertyValue * 100, 2),
            'disclaimer' => 'Estimate only; vacancies, taxes, financing, maintenance, and local costs are not fully modelled.',
        ];
    }
}
