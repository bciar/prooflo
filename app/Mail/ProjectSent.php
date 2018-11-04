<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\ViewModel\ProofEmail;

class ProjectSent extends Mailable
{
    use Queueable, SerializesModels;

    private $proofEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ProofEmail $proofEmail)
    {
        $this->proofEmail = $proofEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch ($this->proofEmail->email_type) {
            
            case 'client_sent':
                return $this->subject('New proofer revision')->view('emails.projects.sent')->with([
                    'project_hash' => $this->proofEmail->project_hash,
                    'name' => $this->proofEmail->name,
                    'project_name' => $this->proofEmail->project_name,
                    'company_name' => $this->proofEmail->company_name,
                    'project_id' => $this->proofEmail->project_id,
                    'sent_by' => $this->proofEmail->sent_by,
                    'sent_by_name' => $this->proofEmail->sent_by_name,
                    'revision_id' => $this->proofEmail->revision_id
                ]);
            break;
            case 'pending':
                return $this->subject('New proofer revision')->view('emails.projects.pending')->with([
                    'project_hash' => $this->proofEmail->project_hash,
                    'name' => $this->proofEmail->name,
                    'project_name' => $this->proofEmail->project_name,
                    'company_name' => $this->proofEmail->company_name,
                    'project_id' => $this->proofEmail->project_id,
                    'sent_by' => $this->proofEmail->sent_by,
                    'sent_by_name' => $this->proofEmail->sent_by_name,
                    'revision_id' => $this->proofEmail->revision_id
                ]);
            break;
            case 'approved':
                return $this->subject('New proofer revision')->view('emails.projects.approved')->with([
                    'project_hash' => $this->proofEmail->project_hash,
                    'name' => $this->proofEmail->name,
                    'project_name' => $this->proofEmail->project_name,
                    'company_name' => $this->proofEmail->company_name,
                    'project_id' => $this->proofEmail->project_id,
                    'sent_by' => $this->proofEmail->sent_by,
                    'sent_by_name' => $this->proofEmail->sent_by_name,
                    'revision_id' => $this->proofEmail->revision_id
                ]);
            break;
            case 'finished':
                return $this->subject('New proofer revision')->view('emails.projects.finished')->with([
                    'project_hash' => $this->proofEmail->project_hash,
                    'name' => $this->proofEmail->name,
                    'project_name' => $this->proofEmail->project_name,
                    'company_name' => $this->proofEmail->company_name,
                    'project_id' => $this->proofEmail->project_id,
                    'sent_by' => $this->proofEmail->sent_by,
                    'sent_by_name' => $this->proofEmail->sent_by_name,
                    'revision_id' => $this->proofEmail->revision_id
                ]);
            break;
        }
      

       /*  return $this->markdown('emails.projects.sent')->with([
            'url' => 'http://google.com'
        ]); */
    }
}
