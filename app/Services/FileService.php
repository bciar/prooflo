<?php

namespace App\Services;

use App\ProjectFile;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request;
use App\Contracts\IFileService;
use App\Services\ProofService;
use Illuminate\Support\Str;
use App\Issue;
use App\Comment;
use Image;


class FileService implements IFileService
{
    public function uploadFile(array $data)
    {
        $proofService = new ProofService();

        try {
            $image = Storage::disk('public')->putFile('pictures', $data['photos']);

            $stream_image = Image::make(Storage::disk('public')->get($image))->encode('jpg', 95)->stream();

            $stream_thumb = Image::make(Storage::disk('public')->get($image))->encode('jpg', 95)->stream();

            $thumb = 'pictures/' . $data['owner_type'] . '/' . Str::random(40) . '.jpg';
            $big = 'pictures/' . $data['owner_type'] . '/' . Str::random(40) . '.jpg';

            Storage::disk('public')->put($thumb, $stream_thumb);
            Storage::disk('public')->put($big, $stream_image);
            Storage::delete($image);

            if ($thumb && $big) {
                $data['thumb_path'] = $thumb;
                $data['path'] = $big;
                $project_file = ProjectFile::store($data);
                if ($project_file != null) {
                    if ($data['owner_type'] == 'issue') {
                        if (array_key_exists('issue_id', $data)) {
                            $proofService->addPictureToIssue($data['issue_id'], $project_file);
                        }
                    }
                    if ($data['owner_type'] == 'comment') {
                        if (array_key_exists('comment_id', $data)) {
                            $proofService->addPictureToComment($data['comment_id'], $project_file);
                        }
                    }
                    if ($data['owner_type'] == 'proof') {
                        $proof = $proofService->createProof($project_file);
                    }
                    return $project_file;
                } else {
                    return false;
                }
            }

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getById($id)
    {
        if ($id > 0) {
            return ProjectFile::findOrFail($id);
        }
    }

    public function deleteFile($id)
    {
        try {
            if ($id) {
                $file = ProjectFile::findOrFail($id);
                if($file){
                    $path = $file->path;
                    $thumb_path = $file->thumb_path;
                    $owner_type = $file->owner_type;
                    $removed = $file->delete();
                    if($removed){
                        $removed_path = Storage::disk('public')->delete($path);
                        $removed_thumb = Storage::disk('public')->delete($thumb_path);
                        if ($removed_path == 1 && $removed_thumb == 1) {
                            if($owner_type == 'issue'){
                                $issue = Issue::where('project_files_id', $id)->first();
                                if($issue){
                                    $issue->project_files_id = null;
                                    $issue->save();
                                }
                                return $issue;

                            }else if($owner_type == 'comment'){
                                $comment = Comment::where('project_files_id', $id)->first();
                                if($comment){
                                    $comment->project_files_id = null;
                                    $comment->save();
                                }
                                return $comment;
                            }
                        } else {
                            return null;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}