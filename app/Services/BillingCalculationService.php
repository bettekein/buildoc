<?php

namespace App\Services;

class BillingCalculationService
{
    /**
     * Calculate billing amounts based on progress billing logic.
     *
     * @param float $contractAmount
     * @param float $progressRate (Percentage)
     * @param float $previousTotalProgressAmount (Tax excluded)
     * @param float $retentionRate (Percentage)
     * @param float $offsetAmount
     * @param float $retentionReleaseAmount
     * @param float $taxRate (Percentage, e.g., 10)
     * @return array
     */
    public function calculate(
        float $contractAmount,
        float $progressRate,
        float $previousTotalProgressAmount,
        float $retentionRate = 20.00,
        float $offsetAmount = 0.00,
        float $retentionReleaseAmount = 0.00,
        float $taxRate = 10.00
    ): array {
        // 累計出来高額（税抜）: 契約総額 × 今回の累計進捗率
        $cumulativeBilledAmount = $this->floor($contractAmount * ($progressRate / 100));

        // 今回出来高額（税抜）: 累計出来高額 - 前回までの累計出来高額
        $currentBillingAmount = $cumulativeBilledAmount - $previousTotalProgressAmount;
        if ($currentBillingAmount < 0) {
           // Should be handled by validation, but here we just return calculation
           // $currentBillingAmount = 0; 
        }

        // 消費税額
        $taxAmount = $this->floor($currentBillingAmount * ($taxRate / 100));

        // 税込金額①: 今回出来高額 + 消費税額
        $grossBillingAmount = $currentBillingAmount + $taxAmount;

        // 今回保留金②: 税込金額① × 保留率
        $currentRetentionAmount = $this->floor($grossBillingAmount * ($retentionRate / 100));

        // 最終請求金額: 税込金額① - 今回保留金② + 保留金解除③ - 相殺金
        $finalBillingAmount = $grossBillingAmount - $currentRetentionAmount + $retentionReleaseAmount - $offsetAmount;

        return [
            'contract_amount' => $contractAmount,
            'progress_rate' => $progressRate,
            'previous_billed_amount' => $previousTotalProgressAmount,
            'cumulative_amount' => $cumulativeBilledAmount,
            'amount_this_time' => $currentBillingAmount, // Tax excluded
            'tax_amount' => $taxAmount,
            'gross_billing_amount' => $grossBillingAmount,
            'retention_money' => $currentRetentionAmount,
            'retention_release_amount' => $retentionReleaseAmount,
            'offset_amount' => $offsetAmount,
            'final_billing_amount' => $finalBillingAmount,
            'tax_rate' => $taxRate,
        ];
    }

    private function floor($value)
    {
        return floor($value);
    }
}
