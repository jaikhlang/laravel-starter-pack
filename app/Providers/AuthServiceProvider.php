<?php

namespace App\Providers;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

      $this->registerPolicies();

      ResetPassword::createUrlUsing(function (User $user, string $token) {
        return  url('/reset-password/token='.$token);
      });


      VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
          return (new MailMessage)
              ->subject('Verify Email Address')
              ->line('Click the button below to verify your email address.')
              ->lineIf($notifiable->provider, 'Please update your username: ' . $notifiable->username)
              ->lineIf($notifiable->provider, 'Please update your password: ' . $notifiable->password)
              ->action('Verify Email Address', $url);
      });
    }
}
