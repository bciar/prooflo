<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revision extends Model
{
    protected $fillable = ['version', 'project_id', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function proofs()
    {
        return $this->hasMany(Proof::class)->with('projectFiles');
    }

    public static function store($data)
    {
        if ($data['project']->id > 0) {
            if (Revision::checkOpenRevision($data['project']->id)) {
                return null;
            } else {
                $revision = new Revision();
                $revision->version = $revision->setCurrentVersion($data['project']->id);
                $revision->status_revision = 'created';
                $revision->project()->associate($data['project']);
                $revision->save();

                return $revision;
            }
        }
    }

    public function setCurrentVersion($project_id)
    {
        $last_version = Revision::where('project_id', $project_id)->max('version');
        $last_version = (int)$last_version;
        $last_version += 1;
        return $last_version = (string)$last_version;
    }

    public static function getRevisionById($revision_id)
    {
        $revision = Revision::with('proofs.issues.comments')->find($revision_id);
        return $revision;
    }

    public static function getLastRevision($project_id)
    {
        $revision = Revision::where('project_id', $project_id)->with('proofs.issues.comments')->where('status_revision', '!=', 'approved')->first();
        if ($revision != null) {
            return $revision;
        } else {
            $revision = Revision::where('project_id', $project_id)->with('proofs.issues.comments')->orderBy('created_at', 'desc')->first();
            return $revision;
        }
    }

    public static function checkOpenRevision($project_id)
    {
        $revision = Revision::where('project_id', $project_id)->where('status_revision', '!=', 'approved')->first();
        return $revision;
    }

}
