<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        $obj = ProjectUser::where('user_id',$user->id)->where('project_id',$project->id)->first();
        if(isset($obj)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        $obj = ProjectUser::where('user_id',$user->id)->where('project_id',$project->id)->first();
        if(isset($obj) and $obj->role_id === 2){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        $obj = ProjectUser::where('user_id',$user->id)->where('project_id',$project->id)->first();
        if(isset($obj) and $obj->role_id === 2){
            return true;
        }else{
            return false;
        }
    }

}
