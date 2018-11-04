<?php

namespace App\Services;

use App\Mail\CollaboratorInvite;
use App\Project;
use App\ProjectFile;
use App\Proof;
use App\Comment;
use App\Revision;
use App\ViewModel\ProofEmail;
use Illuminate\Http\Request;
use Mail;
use App\Mail\ProjectSent;
use Illuminate\Support\Str;
use App\Http\Requests\ProjectRequest;
use App\Contracts\IProjectService;
use App\Contracts\IRevisionService;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Crypt;
use Laravel\Spark\Repositories;
use Carbon\Carbon;
use App\Mail\PasswordChange;
use Laravel\Spark\Spark;
use Laravel\Spark\Repositories\UserRepository;
use Laravel\Spark\Events\Auth\UserRegistered;
/* use Laravel\Spark\Contracts\Interactions\Auth; */

class ProjectService implements IProjectService
{
    private $revisionService;
    private $userRepo;

    public function __construct(IRevisionService $revisionService, UserRepository $userRepo)
    {
        $this->revisionService = $revisionService;
        $this->userRepo = $userRepo;
    }

    public function createProject(array $data)
    {
        try {
            $user_data['client_name'] = $data['client_name'];
            $user_data['email'] = $data['email'];
            $new_user = $this->registerUser($user_data);
            if ($new_user) {
                $data['client_id'] = $new_user->id;
                $project = Project::store($data);
                if ($project != null) {
                    $this->sendWelcomeEmail($new_user, $project->name);
                    $revision_data['project'] = $project;
                    $revision = Revision::store($revision_data);
                    if ($revision) {
                        return $project;

                    }
                }
            }
            return null;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getActiveRevision($project_id)
    {
        $activeRevision = Project::getActiveRevision($project_id);
    }

    public function createSendProject(array $data)
    {
        $request = \request();
        $project = Project::store($data);
        $message = Mail::to($request->email)->send(new ProjectSent($project));
        return $message;

    }
    public function getRevisionVersions($project_id)
    {
        
        $revisions = Project::getRevisionVersions($project_id);
        return $revisions;
    }

    public function sendProject($project_id, $user_type)
    {
        try {
            $project = Project::findOrFail($project_id);
            if ($project) {
                $project_hash = $project->project_hash;
                foreach ($project->users as $user) {
                    if ($user->pivot->role == 'client') {
                        $client = $user;
                    }
                }
                if ($client) {
                    $email_data = new ProofEmail(
                        $client->email,
                        $client->name,
                        $project->name,
                        $project->company,
                        $project_hash,
                        $project_id,
                        $email_type = 'client_sent',
                        $user_type,
                        $this->revisionService->getLastRevision($project->id)->id,
                        \Auth::user()->name
                    );
                    try {
                        Mail::to($client->email)->send(new ProjectSent($email_data));
                        $current_revision = $this->revisionService->getLastRevision($project->id);
                        if ($current_revision) {
                            $this->revisionService->changeRevisionStatus($current_revision->id, 'progress');
                        }
                        return true;
                    } catch (\Swift_TransportException $e) {
                        if (count(Mail::failures()) > 0) {
                            return false;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function registerUser(array $data)
    {
        if ($this->getUserByEmail($data['email'])) {
            if (\Auth::user()->email == $data['email']) {
                return null;
            } else {
                $user = $this->getUserByEmail($data['email']);
            }

        } else {
            $user = new User();
            $user->name = $data['client_name'];
            $user->password = bcrypt(Str::random(7));
            $user->email = $data['email'];
            $user->save();
            
           /*  $temp = [
                'name' => $data['client_name'],
                'email' => $data['email'],
                'password' => bcrypt(Str::random(7)),
                'last_read_announcements_at' => Carbon::now(),
                'trial_ends_at' => Carbon::now()->addDays(Spark::trialDays()),
            ]; */

          /*   $user = $this->userRepo->create($temp); */
           
            /* event(new UserRegistered($user));
            return $user; */
        }
        return $user;
    }

    public function sendWelcomeEmail($user, $project_name)
    {
      // Generate a new reset password token
        $token = app('auth.password.broker')->createToken($user);
      
      // Send email
        Mail::to($user)->send(new PasswordChange($token, $user->name, $project_name, Auth::user()->name));

       /*  \Mail::send('emails.welcome', ['user' => $user, 'token' => $token], function ($m) use ($user) {
            $m->from('hello@appsite.com', 'Your App Name');
            $m->to($user->email, $user->name)->subject('Welcome to APP');
        }); */
    }

    public function inviteCollaborator($email, $project_id)
    {
        $data = [
            'email' => $email,
            'client_name' => 'Collaborator',
        ];

        $project = Project::find($project_id);

        if ($project && $new_user = $this->registerUser($data)) {
            $project->users()->attach($new_user, ['role' => 'collaborator']);

            $token = app('auth.password.broker')->createToken($new_user);
            Mail::to($new_user)->send(new PasswordChange($token, $new_user->name, $project->name, Auth::user()->name));

            return ApiResponse::success('Invitation sent successfully', '');
        }

        return ApiResponse::error('001', 'Invalid or missing data');
    }

    public function getUserByEmail($email)
    {
        if ($email != '') {
            $user = User::where('email', $email)->first();
            return $user;
        }
        return null;
    }

    public function submitDesicion(array $data)
    {
        $project = Project::findOrFail($data['project_id']);
        if ($project != null) {
            foreach ($project->users as $key => $_user) {
                if ($_user->pivot->role == 'freelancer') {
                    $user = $_user;
                } else if ($_user->pivot->role == 'client') {
                    $client = $_user;
                }
            }
            if ($user != null) {
                switch ($data['decision']) {
                    case 'finished':
                        $last_revision = $this->revisionService->getLastRevision($project->id);
                        $version = $this->revisionService->changeRevisionStatus($last_revision->id, 'approved');
                        $email_type = 'finished';
                        $project_hash = $project->project_hash;
                        $email_data = new ProofEmail(
                            $user->email,
                            $user->name,
                            $project->name,
                            $project->company,
                            $project_hash,
                            $data['project_id'],
                            $email_type,
                            'client',
                            $data['revision_id'],
                            $client->name
                        );
                        try {
                            Mail::to($user->email)->send(new ProjectSent($email_data));
                            $project->status = 'approved';
                            $project->save();
                            return ApiResponse::success('Decision sent successfully', '');

                        } catch (\Swift_TransportException $e) {
                            if (count(Mail::failures()) > 0) {
                                return ApiResponse::error('001', 'There has been an error sending the email');
                            }
                        }
                        break;

                    case 'approved':
                        $last_revision = $this->revisionService->getLastRevision($project->id);
                        $version = $this->revisionService->changeRevisionStatus($last_revision->id, 'approved');
                        if (count($version->proofs) == 0) {
                            $version->delete();
                            $version->save();
                        }
                        if ($version) {
                           /*  $new_revision = $this->revisionService->create($project->id); */
                            /* if ($new_revision) { */
                            $email_type = 'approved';
                            $project_hash = $project->project_hash;
                               /*  $user_id = $project->user_id;
                                $user = User::findOrFail($user_id); */
                            foreach ($project->users as $key => $_user) {
                                if ($_user->pivot->role == 'freelancer') {
                                    $user = $_user;
                                }
                            }
                            $email_data = new ProofEmail(
                                $user->email,
                                $user->name,
                                $project->name,
                                $project->company,
                                $project_hash,
                                $data['project_id'],
                                $email_type,
                                'client',
                                $data['revision_id'],
                                $client->name
                            );

                            try {
                                Mail::to($user->email)->send(new ProjectSent($email_data));
                                $project->status = 'progress';
                                $project->save();
                                return ApiResponse::success('Decision sent successfully', '');

                            } catch (\Swift_TransportException $e) {
                                if (count(Mail::failures()) > 0) {
                                    return ApiResponse::error('001', 'There has been an error sending the email');
                                }
                            }
                           /*  } else {
                                return ApiResponse::error('001', 'This revision has already been approved. You can safely close your browser window and wait for future revisions');
                            } */
                        } else {
                            return ApiResponse::error('001', 'This revision has already been approved. You can safely close your browser window and wait for future revisions');
                        }
                        break;
                }
            }
        }
        return ApiResponse::error('001', 'There has been an error sending the email');
    }

    public function sendBackRevision($project_id, $user_type)
    {
        $project = Project::findOrFail($project_id);
        $email_type = 'pending';
        $project_hash = $project->project_hash;
        if ($user_type == 'freelancer') {
            foreach ($project->users as $key => $_user) {
                if ($_user->pivot->role == 'client') {
                    $user = $_user;
                }
            }
        }
        if ($user_type == 'client') {
            foreach ($project->users as $key => $_user) {
                if ($_user->pivot->role == 'freelancer') {
                    $user = $_user;
                }
            }
        }
       /*  print_r($user); die(); */
        $email_data = new ProofEmail(
            $user->email,
            $user->name,
            $project->name,
            $project->company,
            $project_hash,
            $project_id,
            $email_type,
            $user_type,
            $this->revisionService->getLastRevision($project->id)->id,
            Auth::user()->name
        );
        try {
            Mail::to($user->email)->send(new ProjectSent($email_data));
            $current_revision = $this->revisionService->getLastRevision($project->id);
            if ($current_revision) {
                $this->revisionService->changeRevisionStatus($current_revision->id, 'progress');
            }
            return true;
        } catch (\Swift_TransportException $e) {
            if (count(Mail::failures()) > 0) {
                return false;
            }
        }
    }
}