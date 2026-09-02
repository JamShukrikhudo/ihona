<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Valuations\Application;

use Illuminate\Validation\ValidationException;

final class CalculateMortgage
{
    /** @return array<string, mixed> */
    public function handle(float $propertyPrice, float $loanAmount, float $annualInterestRate, int $loanTermYears): array
    {
        if ($propertyPrice <= 0 || $loanAmount <= 0 || $loanAmount > $propertyPrice || $annualInterestRate < 0 || $annualInterestRate > 100 || $loanTermYears < 1 || $loanTermYears > 50) {
            throw ValidationException::withMessages(['mortgage' => 'Property price, loan amount, interest rate, and term must be valid.']);
        }

        $payments = $loanTermYears * 12;
        $monthlyRate = $annualInterestRate / 100 / 12;
        $monthlyPayment = $monthlyRate === 0.0
            ? $loanAmount / $payments
            : $loanAmount * ($monthlyRate * (1 + $monthlyRate) ** $payments) / ((1 + $monthlyRate) ** $payments - 1);
        $schedule = $this->schedule($loanAmount, $monthlyRate, $monthlyPayment, $payments);
        $totalPayment = array_sum(array_column($schedule, 'payment'));

        return [
            'estimated' => true,
            'property_price' => round($propertyPrice, 2),
            'loan_amount' => round($loanAmount, 2),
            'deposit' => round($propertyPrice - $loanAmount, 2),
            'loan_to_value' => round($loanAmount / $propertyPrice * 100, 2),
            'annual_interest_rate' => round($annualInterestRate, 4),
            'loan_term_years' => $loanTermYears,
            'monthly_payment' => round($monthlyPayment, 2),
            'total_payment' => round($totalPayment, 2),
            'total_interest' => round($totalPayment - $loanAmount, 2),
            'amortization_schedule' => $schedule,
            'disclaimer' => 'Estimate only; actual offers, fees, taxes, and rates vary by lender and borrower.',
        ];
    }

    /** @return list<array{month: int, payment: float, principal: float, interest: float, balance: float}> */
    private function schedule(float $loanAmount, float $monthlyRate, float $monthlyPayment, int $payments): array
    {
        $schedule = [];
        $balance = $loanAmount;
        for ($month = 1; $month <= $payments && $balance > 0.0; $month++) {
            $interest = $balance * $monthlyRate;
            $principal = min($balance, max(0.0, $monthlyPayment - $interest));
            $payment = $principal + $interest;
            $balance = max(0.0, $balance - $principal);
            $schedule[] = ['month' => $month, 'payment' => round($payment, 2), 'principal' => round($principal, 2), 'interest' => round($interest, 2), 'balance' => round($balance, 2)];
        }

        return $schedule;
    }
}
