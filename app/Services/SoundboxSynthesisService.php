<?php

namespace App\Services;

/**
 * Service class for multilingual soundbox announcements (English, Hindi, Gujarati)
 * for instant UPI payment confirmations and kitchen alerts.
 */
class SoundboxSynthesisService
{
    /**
     * Generate structured speech announcement data for payment confirmation.
     *
     * @param float $amount Received payment amount
     * @param string $language Preferred language ('en', 'hi', 'gu')
     * @param string $currency Currency label ('Rupees' / 'रुपये' / 'રૂપિયા')
     * @return array Announcement payload with synthesized text and speech parameters
     */
    public function generatePaymentAnnouncement(float $amount, string $language = 'en', string $currency = 'INR'): array
    {
        $formattedAmount = number_format($amount, 2);
        $intAmount = (int)round($amount);

        switch (strtolower($language)) {
            case 'gu':
            case 'gujarati':
                $text = "DineFlow AI પર {$intAmount} રૂપિયા પ્રાપ્ત થયા.";
                $langCode = 'gu-IN';
                $pitch = 1.0;
                $rate = 0.95;
                break;

            case 'hi':
            case 'hindi':
                $text = "DineFlow AI पर {$intAmount} रुपये प्राप्त हुए।";
                $langCode = 'hi-IN';
                $pitch = 1.0;
                $rate = 0.95;
                break;

            case 'en':
            default:
                $text = "Received {$formattedAmount} rupees on DineFlow AI.";
                $langCode = 'en-IN';
                $pitch = 1.0;
                $rate = 1.0;
                break;
        }

        return [
            'success' => true,
            'amount' => $amount,
            'language' => $language,
            'lang_code' => $langCode,
            'announcement_text' => $text,
            'speech_config' => [
                'voice' => 'female',
                'pitch' => $pitch,
                'rate' => $rate,
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Generate kitchen audio chime configuration for incoming KOT orders.
     */
    public function generateKotChimeConfig(int $orderId, string $priority = 'normal'): array
    {
        return [
            'order_id' => $orderId,
            'priority' => $priority,
            'chime_type' => $priority === 'urgent' ? 'dual_beep_alert' : 'soft_bell',
            'frequencies_hz' => $priority === 'urgent' ? [880, 1174.66] : [587.33, 880],
            'duration_ms' => $priority === 'urgent' ? 1200 : 800,
        ];
    }
}
