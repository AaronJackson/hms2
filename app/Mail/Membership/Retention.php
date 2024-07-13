<?php

namespace App\Mail\Membership;

use HMS\Entities\User;
use HMS\Repositories\MetaRepository;
use HMS\Repositories\RoleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Retention extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $fullname;

    /**
     * Create a new message instance.
     *
     * @param User $user
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->fullname = $user->getFirstname();
    }

    /**
     * Build the message.
     *
     * @param MetaRepository $metaRepository
     * @param RoleRepository $roleRepository
     *
     * @return $this
     */
    public function build(MetaRepository $metaRepository, RoleRepository $roleRepository)
    {
        $subject = $metaRepository->get('membership_retention_email_subject', 'Hello from Nottingham Hackspace');
        $membershipTeam = $roleRepository->findOneByName('team.membership');
        $membershipEmail = $membershipTeam->getEmail();
        $membershipMgEmail = preg_replace('/@/m', '@mg.', $membershipEmail);
        $displayName = config('branding.space_name') . " - " . $membershipTeam->getDisplayName();

        return $this
            ->replyTo($membershipEmail, $displayName)
            ->subject($subject)
            ->markdown('emails.membership.retention');
    }
}