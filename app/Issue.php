<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Comment;

class Issue extends Model
{
    protected $fillable = ['title', 'user_id', 'proof_id', 'project_files_id', 'group'];

    protected $appends = ['user'];
   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proof()
    {
        return $this->belongsTo(Proof::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->with('projectFiles')->with('user');
    }

    public function projectFiles()
    {
        return $this->belongsTo(ProjectFile::class);
    }

    public function getUserAttribute()
    {
        return $this->user();
    }

}
