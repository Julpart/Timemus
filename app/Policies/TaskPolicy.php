<?php

namespace App\Policies;

use App\Models\ProjectUser;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       //Для супер админа
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        $obj = ProjectUser::where('user_id',$user->id)->where('project_id',$task->project->id)->first();
        if(isset($obj) and $obj->role_id === 2){
            return true;
        }elseif($task->executor->id === $user->id){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if($task->creator->id === $user->id){
            return true;
        }elseif($task->executor->id === $user->id){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if($task->creator->id === $user->id){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        //
    }

}
