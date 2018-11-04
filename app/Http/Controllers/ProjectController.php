<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProjectRequest;
use App\Project;
use App\ProjectFile;
use App\Proof;
use Mail;
use App\Mail\ProjectSent;
use App\Contracts\IProjectService;
use App\Contracts\IRevisionService;
use App\Helpers\ApiResponse;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    private $projectService;
    private $revisionService;

    public function __construct(IProjectService $projectService, IRevisionService $revisionService)
    {
        $this->projectService = $projectService;
        $this->revisionService = $revisionService;
    }

    public function index()
    {
        $user = \Auth::user();
        $projects = [];
        if (count($user->projects) > 0) {
            foreach ($user->projects as $key => $project) {
                $last_revision = $this->revisionService->getLastRevision($project->id);
                if ($last_revision != null) {
                    $projects[$key] = [
                        'id' => $project->id,
                        'name' => $project->name,
                        'active_revision' => $last_revision,
                        'company' => $project->company,
                        'status' => $project->status,
                    ];

                    foreach ($project->users as $key2 => $_user) {
                        if ($user->id == $_user->id) {
                            $projects[$key]['role'] = $_user->pivot->role;
                        }
                    }
                }
            }
            return $projects;
        }
        return [];
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->all();
        $project = $this->projectService->createProject($data);
        if ($project) {
            $result = [
                'project_id' => $project->id,
                'revision_id' => $project->getActiveRevision($project->id)->id
            ];
            return ApiResponse::success('Project saved successfully', $result);
        } else {
            return ApiResponse::error('001', 'This email address is associated with your freelancer account in this project. Please use a different client email address');
        }
    }

    public function getRevisionVersions($project_id)
    {
        $revisions = $this->projectService->getRevisionVersions($project_id);
        $result = [];
        if ($revisions) {
            foreach ($revisions as $key => $revision) {
                $result[$key]['id'] = $revision->id;
                $result[$key]['status'] = $revision->status;
                $result[$key]['value'] = $revision->version;
                $result[$key]['label'] = 'V' . $revision->version;
            }
        }
        return $result;
    }

    public function getCollaborators($project_id)
    {
        $project = Project::with(['users' => function ($query) {
            $query->where('role', 'collaborator');
        }])->where('id', $project_id)->firstOrFail();

        return ['status' => 1, 'collaborators' => $project->users];
    }

    public function loadInitialRevision($project_id, $revison_id)
    {
        $result = [];
        $revisionVersions = $this->projectService->getRevisionVersions($project_id);
        if ($revisionVersions) {
            foreach ($revisionVersions as $key => $version) {
                $result['versions'][$key]['id'] = $version->id;
                $result['versions'][$key]['status'] = $version->status_revision;
                $result['versions'][$key]['value'] = $version->version;
                $result['versions'][$key]['label'] = 'V' . $version->version;
            }
        }
        if ($revison_id > 0) {
            $revision = $this->revisionService->getRevisionById($revison_id);
            if ($revision) {
                $result['last_revision'] = $revision;
                return ApiResponse::success('', $result);
            }
        } else {
            $lastRevision = $this->revisionService->getLastRevision($project_id);
            if ($lastRevision) {
                $result['last_revision'] = $lastRevision;
                return ApiResponse::success('', $result);
            }
        }
        return ApiResponse::error('001', 'Error loading revision');
    }


    public function sendProject($project_id, $user_type)
    {
        if ($project_id > 0 && $user_type != '') {
            $result = $this->projectService->sendProject($project_id, $user_type);
            if ($result == true) {
                return ApiResponse::success('Project sent successfully', '');
            }
        }
        return ApiResponse::error('001', 'There has been a problem sending the project. Try again in few moments');
    }

    public function loadProofer($project_hash, $revision_id)
    {
        if ($project_hash) {
            if (\Auth::check()) {
                Auth::logout();
                return redirect('/login');
            }

            $project = Project::where('project_hash', $project_hash)->first();
            if ($project) {
                $current_revision = $this->revisionService->getRevisionById($revision_id);
                return redirect('/proofer/' . $project->id . '/' . $current_revision->id);
            }

        }
        return view('404');
    }

   /*  public function loadEditorClient($project_hash, $revision_id)
    {
        if ($project_hash) {
            $project = Project::where('project_hash', $project_hash)->first();
            if ($project) {
                $current_revision = $this->revisionService->getRevisionById($revision_id);
                return redirect('/proofer_guest/' . $project->id . '/' . $current_revision->id . '/' . $project_hash);
            }
        }
        return view('404');
    }

    public function loadEditorFreelancer($project_id, $revision_id)
    {
        if ($project_id) {
            if (\Auth::check()) {
                $project = Project::findOrFail($project_id);
                if ($project) {
                    $user_id = $project->user_id;
                    if (\Auth::user()->id == $user_id) {
                        $current_revision = $this->revisionService->getRevisionById($revision_id);
                        return redirect('/proofer_freelancer/' . $project_id . '/' . $current_revision->id);
                    }
                }
            } else {
                return redirect('/login');
            }
        }
        return view('404');
    } */

    public function submitDecision(Request $request)
    {
        $validatedData = $request->validate([
            'project_id' => 'required|integer',
            'revision_id' => 'required|integer',
            'decision' => 'required'
        ]);

        $data = $request->all();
        return $result = $this->projectService->submitDesicion($data);
    }

    public function sendCollaboratorInvite(Request $request)
    {
        $this->validate($request, [
            'project_id' => 'required|integer',
            'email' => 'required|email',
        ]);

        return $this->projectService->inviteCollaborator($request->input('email'), $request->input('project_id'));
    }
    
    public function getCurrentUserRol($project_id)
    {
        if ($project_id > 0) {
            $project = Project::findOrFail($project_id);
            if ($project) {
                if ($project->client_id == \Auth::user()->id) {
                    return ApiResponse::success('user rol', 'client');
                }
                if ($project->user_id == \Auth::user()->id) {
                    return ApiResponse::success('user rol', 'freelancer');
                }
            }
            return ApiResponse::error('001', 'Error getting user rol in this project');
        }
    }

    public function sendBackRevision($project_id, $user_type)
    {
        if ($project_id > 0 && $user_type != '') {
            $result = $this->projectService->sendBackRevision($project_id, $user_type);
            if ($result == true) {
                return ApiResponse::success('Revision sent successfully', '');
            }
        }
        return ApiResponse::error('001', 'There has been a problem sending the revision email back. Try again in few moments');
    }

}