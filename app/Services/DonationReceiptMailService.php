<?php

namespace App\Services;

use App\Models\DonationOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DonationReceiptMailService
{
    /**
     * Build view data for the donation receipt email template.
     * Used by the job (send) and by the preview endpoint.
     */
    public function buildViewData(DonationOrder $order): array
    {
        $order->loadMissing(['donation_user', 'donation_items']);
        $user = $order->donation_user;
        $paymentDate = $order->meezan_payment_date ?? $order->faysal_payment_date ?? $order->updated_at;
        $dateFormatted = $paymentDate instanceof \DateTimeInterface
            ? Carbon::parse($paymentDate)->format('d M Y')
            : date('d M Y', strtotime($paymentDate));

        $paymentMethod = 'Other';
        if ($order->payment_method === 'faysal_bank') {
            $paymentMethod = 'Faysal Bank';
        } elseif ($order->payment_method === 'meezan_bank') {
            $paymentMethod = 'Meezan Bank';
        }

        $paymentReference = $order->meezan_transaction_id ?? $order->faysal_transaction_id ?? $order->payment_id ?? '';

        $addressLine1 = $user ? trim($user->address ?? '') : '';
        $addressParts = array_filter([
            $user->city ?? '',
            $user->area ?? '',
            $user->postal_code ?? '',
            $user->country ?? '',
        ]);
        $addressLine2 = $user ? trim(implode(', ', $addressParts)) : '';

        $items = $order->donation_items ?? [];
        $allocations = [];
        foreach ($items as $index => $item) {
            $qty = (int) ($item->quantity ?? 1);
            $amount = (float) ($item->unit_price ?? 0) * max(1, $qty);
            $allocations[] = [
                'program' => $item->item_name ?? 'Donation',
                'type' => $item->sponsor_type ?? 'General',
                'amount' => number_format($amount, 2),
            ];
        }

        // Template supports allocation_1_* and allocation_2_* (and optionally @foreach allocations)
        $a1 = $allocations[0] ?? ['program' => '', 'type' => '', 'amount' => '0.00'];
        $a2 = $allocations[1] ?? ['program' => '', 'type' => '', 'amount' => '0.00'];

        $currency = $order->payment_currency ?? 'PKR';
        $grandTotal = (float) ($order->grand_total ?? 0);

        $defaultLogoUrl = config('app.donation_receipt_logo_url', 'https://yourdomain.com/path-to-logo.png');
        $sourceSite = strtolower(trim($order->source_site ?? ''));
        if (in_array($sourceSite, ['cefonlineacademy.com', 'www.cefonlineacademy.com'], true)) {
            $resolvedLogoUrl = config('app.donation_receipt_logo_url_cef_online_academy', '');
        } elseif (in_array($sourceSite, ['cef.org.pk', 'www.cef.org.pk'], true)) {
            $resolvedLogoUrl = config('app.donation_receipt_logo_url_cef_org_pk', '');
        } else {
            $resolvedLogoUrl = $defaultLogoUrl;
        }
        $logoUrl = ($resolvedLogoUrl !== '') ? $resolvedLogoUrl : $defaultLogoUrl;

        return [
            'receipt_date' => $dateFormatted,
            'receipt_no' => 'CEF-DN-' . $order->id,
            'donor_name' => $user ? ($user->name ?? '') : '',
            'contact_number' => $user ? ($user->phone_number ?? '') : '',
            'email' => $user ? ($user->email ?? '') : '',
            'address_line_1' => $addressLine1,
            'address_line_2' => $addressLine2,
            'payment_reference' => $paymentReference,
            'payment_date' => $dateFormatted,
            'payment_method' => $paymentMethod,
            'currency' => $currency,
            'amount_received' => number_format($grandTotal, 2),
            'total_amount' => number_format($grandTotal, 2),
            'allocation_1_program' => $a1['program'],
            'allocation_1_type' => $a1['type'],
            'allocation_1_amount' => $a1['amount'],
            'allocation_2_program' => $a2['program'],
            'allocation_2_type' => $a2['type'],
            'allocation_2_amount' => $a2['amount'],
            'allocations' => $allocations,
            'logo_url' => $logoUrl,
        ];
    }

    /**
     * Send the receipt email to the donor. Call from the queued job only.
     * Does not log; the job is responsible for logging after send attempt.
     *
     * @throws \Throwable
     */
    public function sendReceipt(DonationOrder $order): void
    {
        $order->loadMissing(['donation_user', 'donation_items']);
        $user = $order->donation_user;
        if (!$user || empty(trim($user->email ?? ''))) {
            throw new \InvalidArgumentException('Donation order has no donor email.');
        }

        $viewData = $this->buildViewData($order);
        $to = $user->email;
        $subject = 'Your Donation Receipt – Character Education Foundation';

        Mail::send('donation-email-template', $viewData, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Render the receipt email HTML (no send). Used for preview API.
     */
    public function renderPreviewHtml(DonationOrder $order): string
    {
        $viewData = $this->buildViewData($order);
        return view('donation-email-template', $viewData)->render();
    }
}
