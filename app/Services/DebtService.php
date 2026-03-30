<?php
// app/Services/DebtService.php

namespace App\Services;

use App\Models\ConsolidatedBilling;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebtService
{
    public static function getOverdueCount()
    {
        return ConsolidatedBilling::whereIn('status', ['pending', 'sent', 'overdue'])
            ->where('due_date', '<', now())
            ->whereRaw('total_amount > paid_amount')
            ->count();
    }

    public static function sendPaymentReminders($invoiceIds, $reminderType = 'email')
    {
        $invoices = ConsolidatedBilling::whereIn('id', $invoiceIds)
            ->with('user')
            ->get();

        foreach ($invoices as $invoice) {
            // Send reminder based on type (email, SMS, etc.)
            self::sendReminder($invoice, $reminderType);

            // Log the action
            self::logCollectionAction($invoice, 'reminder', $reminderType);
        }

        return count($invoices);
    }

    public static function createPaymentPlan($invoiceId, $data)
    {
        $invoice = ConsolidatedBilling::findOrFail($invoiceId);

        DB::beginTransaction();

        try {
            $outstanding = $invoice->total_amount - $invoice->paid_amount;
            $downPayment = $data['down_payment'] ?? 0;
            $installmentCount = $data['installment_count'];

            $installmentAmount = ($outstanding - $downPayment) / $installmentCount;

            $paymentPlan = \App\Models\PaymentPlan::create([
                'consolidated_billing_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'total_amount' => $outstanding,
                'down_payment' => $downPayment,
                'installment_count' => $installmentCount,
                'installment_amount' => $installmentAmount,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'terms' => $data['terms'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create installments
            $installments = [];
            $currentDate = Carbon::parse($data['start_date']);

            for ($i = 1; $i <= $installmentCount; $i++) {
                $installments[] = [
                    'payment_plan_id' => $paymentPlan->id,
                    'installment_number' => $i,
                    'amount' => $installmentAmount,
                    'due_date' => $currentDate->copy()->addMonths($i - 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            \App\Models\PaymentPlanInstallment::insert($installments);

            // Update invoice status
            $invoice->update(['status' => 'payment_plan']);

            DB::commit();

            return $paymentPlan;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function writeOffDebt($invoiceId, $data)
    {
        $invoice = ConsolidatedBilling::findOrFail($invoiceId);

        DB::beginTransaction();

        try {
            $writeOffAmount = $invoice->total_amount - $invoice->paid_amount;

            $writeOff = \App\Models\DebtWriteOff::create([
                'consolidated_billing_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'amount' => $writeOffAmount,
                'write_off_type' => $data['type'],
                'reason' => $data['reason'],
                'approval_notes' => $data['approval_notes'] ?? null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Update invoice
            $invoice->update([
                'status' => 'written_off',
                'metadata' => array_merge(
                    $invoice->metadata ?? [],
                    ['write_off_id' => $writeOff->id]
                )
            ]);

            DB::commit();

            return $writeOff;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function assignToCollectionAgency($invoiceId, $agencyData)
    {
        $invoice = ConsolidatedBilling::findOrFail($invoiceId);

        $assignment = \App\Models\CollectionAgencyAssignment::create([
            'consolidated_billing_id' => $invoice->id,
            'user_id' => $invoice->user_id,
            'agency_name' => $agencyData['agency_name'],
            'assigned_amount' => $invoice->total_amount - $invoice->paid_amount,
            'commission_rate' => $agencyData['commission_rate'] ?? 0,
            'assignment_date' => now(),
            'notes' => $agencyData['notes'] ?? null,
        ]);

        // Update invoice status
        $invoice->update(['status' => 'collection_assigned']);

        return $assignment;
    }
}
