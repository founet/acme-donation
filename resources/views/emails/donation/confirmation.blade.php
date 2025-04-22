@component('mail::message')
    # Thank you for your donation!

    You have supported the campaign **#{{ $donation->campaignId }}** with an amount of **{{ $donation->amount }} {{ $donation->currency }}**.

    Thank you for your commitment 🌍
@endcomponent
