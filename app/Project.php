<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Revision;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = ['name', 'company', 'status', 'project_hash'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role');
    }

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }

    public static function store($data)
    {
        try {
            if (array_key_exists('id', $data)) {
                if ($data['id'] > 0) {
                    $project = Project::findOrFail($data['id']);
                    $project->status = $data['status'];
                } else {
                    return null;
                }
            } else {
                $project = new Project();
                $project->project_hash = Str::random(40);
                $project->status = 'created';

            }
            $project->name = $data['name'];
            $project->company = $data['company'];
            $project->save();
            
            $project->users()->attach(\Auth::user()->id, ['role' => 'freelancer']);
            $client_user = User::find($data['client_id']);
            if ($client_user) {
                $project->users()->attach($client_user, ['role' => 'client']);
            }
            return $project;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }



    public static function getActiveRevision($project_id)
    {
        try {
            $revision = Revision::where('project_id', $project_id)->first();
            return $revision;
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $e->getMessage();
        }
    }

    public static function getRevisionVersions($project_id)
    {
        try {
            $project = Project::findOrFail($project_id);
            $revisions = $project->revisions()->orderBy('created_at', 'desc')->get();
            return $revisions;
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $e->getMessage();
        }
    }

}
