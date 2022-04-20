<?php

namespace App\Modules\Orders\Notifications;

use Illuminate\Support\Facades\Lang;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Modules\Systems\Models\Entities\EmailTemplate;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Systems\Constants\MailConstant;

class MailResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;
    public $queue;
    public $type;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token, $type)
    {
        $this->token = $token;
        $this->queue = 'sendmail';
        $this->type = $type;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }
        $template_name = config('mail.template_name');
        $template = EmailTemplate::where('name', $template_name)->first();
        if ($template) {
            $codes = MailConstant::codes;
            $email = $notifiable->getEmailForPasswordReset();

            $name = '';
            $link = '';
            if ( $this->type === 'shop') {
                $shop = OrderShop::where('email', $email)->first();
                $name = $shop->name;
                $link = route('shop.password.reset.token', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false);
            } else {
                $user = User::where('email', $email)->first();
                $name = $user->name;
                $link = route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false);
            }

            foreach ( $codes as $key => $item) {
                $replace = '';
                if ( $key == '{__TEN_SHOP__}') {
                    $replace = $name;
                }
                if ( $key == '{__LINK_RESET_PASSWORD__}') {
                    $replace = url(config('app.url') . $link);
                }
                if ($replace != '') {
                    $template->content = str_replace($key, $replace, $template->content);
                }
            }

            return (new MailMessage)
                ->view('Shops::mail.forget', ['html' => $template->content])
                ->subject($template->subject);
        }

        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', url(config('app.url').route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
            ->line('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')])
            ->line('If you did not request a password reset, no further action is required.');

    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
