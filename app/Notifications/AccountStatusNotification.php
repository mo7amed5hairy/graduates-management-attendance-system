<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountStatusNotification extends Notification
{
    use Queueable;

    public string $action;
    public string $adminName;

    public function __construct(string $action, string $adminName)
    {
        $this->action = $action;
        $this->adminName = $adminName;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject(match ($this->action) {
                'approved' => '✅ تم قبول طلب التسجيل',
                'activated' => '✅ تم تفعيل حسابك',
                'deactivated' => '⏸️ تم تعليق حسابك',
                'rejected' => '❌ تم رفض حسابك',
                'deleted' => '🗑️ تم حذف حسابك',
                default => '📢 تغيير حالة الحساب',
            })
            ->greeting('مرحباً ' . $notifiable->name);

        $mail = match ($this->action) {
            'approved' => $mail
                ->line('تم قبول طلب تسجيلك والموافقة على بياناتك من قبل الإدارة.')
                ->line('يمكنك الآن الدخول إلى حسابك والاستفادة من جميع الخدمات المتاحة.'),
            'activated' => $mail
                ->line('تم تفعيل حسابك بنجاح من قبل الإدارة.')
                ->line('يمكنك الآن الدخول إلى حسابك والاستفادة من جميع الخدمات المتاحة.'),
            'deactivated' => $mail
                ->line('تم تعليق حسابك من قبل الإدارة.')
                ->line('إذا كان لديك أي استفسار، يرجى التواصل مع الإدارة.'),
            'rejected' => $mail
                ->line('عذراً، لم يتم اعتماد حسابك في نظام توثيق الخريجين.')
                ->line('إذا كان لديك أي استفسار بخصوص سبب الرفض، يرجى التواصل مع الإدارة.'),
            'deleted' => $mail
                ->line('تم حذف حسابك من قبل الإدارة.')
                ->line('إذا كان لديك أي استفسار، يرجى التواصل مع الإدارة.'),
            default => $mail
                ->line('تم تغيير حالة حسابك من قبل الإدارة.'),
        };

        return $mail
            ->line('تمت العملية بواسطة: ' . $this->adminName)
            ->salutation('مع تحيات إدارة نظام توثيق الخريجين');
    }
}
