# Account Activated

Hi {{ $user->name }},

Good news! Your account has been verified by our team. You can now log in and access your dashboard.

<x-mail::button :url="route('login')">
Go to Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} team
