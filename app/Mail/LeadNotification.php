<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Yahan hum direct HTML inject kar rahe hain build method ke zariye
        return $this->html($this->getHtmlLayout())
                    ->subject($this->details['subject'] ?? 'Lead Notification');
    }

    protected function getHtmlLayout()
    {
        $name = $this->details['lead_name'] ?? 'User';
        $body = $this->details['body'] ?? 'No message content provided.'; // Dynamic Body
        $url  = url('/admin/leads');

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px; overflow: hidden;'>
            <div style='background: #35394F; color: white; padding: 20px; text-align: center;'>
                <h2 style='margin:0;'>BazOps CRM</h2>
            </div>
            <div style='padding: 20px; color: #333;'>
                <h3>Hello,</h3>
                <p>Notification regarding: <strong>$name</strong></p>
                <div style='background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #35394F;'>
                    <p style='margin: 0; color: #555; white-space: pre-wrap;'>$body</p>
                </div>
                <div style='text-align: center; margin-top: 25px;'>
                    <a href='$url' style='background: #35394F; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>View Details</a>
                </div>
            </div>
            <div style='background: #f1f1f1; color: #777; padding: 15px; text-align: center; font-size: 12px;'>
                <p style='margin:0;'>&copy; " . date('Y') . " BazOps Technologies. All rights reserved.</p>
            </div>
        </div>
        ";
    }
}