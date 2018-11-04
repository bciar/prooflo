<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Project;

class ProjectFile extends Model
{
    protected $fillable = ['path','thumb_path', 'file_type', 'owner_type'];

    public function revision()
    {
        return $this->belongsTo(Revision::class);
    }

    public static function store($data)
    {
        $project_file = new ProjectFile();
        $project = new Project();

        $project_file->path = $data['path'];
        $project_file->thumb_path = $data['thumb_path'];
        $project_file->owner_type = $data['owner_type'];
        $project_file->file_type = $data['file_type'];
        if (!array_key_exists('revision_id', $data)) {
            $project_file->revision_id = $project->getActiveRevision($data['project_id'])->id;
        } else {
            $project_file->revision_id = $data['revision_id'];
        }

        $project_file->save();
        return $project_file;
    }

}
