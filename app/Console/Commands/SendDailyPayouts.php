<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\InvoiceMail;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class SendDailyPayouts extends Command
{
    /**
     * Summary: The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'payouts:send';
    protected $signature = 'payouts:send {--date= : date to process (Y-m-d), default today}';

    /**
     * Summary: The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices per customer for unprocessed payments and email them';

    /**
     * ESummary: payment table using customer invoice create and send mail.
     */
    public function handle()
    {
        $date = $this->option('date') ? \Carbon\Carbon::parse($this->option('date'))->format('Y-m-d') : now()->format('Y-m-d');

        $this->info("Processing unprocessed payments for date: $date");

        $payments = Payment::where('processed', false)
                        ->orderBy('customer_email')
                        ->get();

        if ($payments->isEmpty()) {
            $this->info('No unprocessed payments found for that date.');
            return 0;
        }

        $grouped = $payments->groupBy('customer_email');

        foreach ($grouped as $email => $paymentList) {
            $customerName = $paymentList->first()->customer_name ?? $email;

            $this->info("Generating invoice for: $email ({$customerName}) - {$paymentList->count()} items");

            $invoiceHtml = View::make('emails.invoice', [
                'customerName' => $customerName,
                'payments' => $paymentList,
                'date' => $date,
                'totalUsd' => $paymentList->sum('amount_usd'),
            ])->render();

            Mail::to($email)->send(new InvoiceMail($customerName, $invoiceHtml));

            foreach ($paymentList as $payment) {
                $payment->update([
                    'processed' => true,
                    'processed_at' => now(),
                ]);
            }

            $this->info("Sent invoice to $email and marked payments processed.");
        }

        return 0;
    }
}
