<?php
namespace Diagro\Web\Policy;

use Diagro\Token\Model\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Defines the policy methods a Diagro policy implements.
 * The before() method checks if the user has the ability for the child policy class.
 * The name UserPolicy matches the application right "user".
 *
 * If there is no application, permission or ability is false,
 * the ability method isn't executed.
 *
 * The ability methods return default true.
 * Override a method for special checks.
 *
 * @package Diagro\Backend\Contracts
 */
abstract class Diagro
{


    private static array $abilities = [
        'read', 'create', 'update', 'delete', 'publish', 'export'
    ];


    /**
     * Checks if the user has the ability for this policy.
     * If the user wants to read a model. The policy name (is the right name) checks
     * if the read flag is true.
     *
     * If the user has the ability, then proceed to execute the policy method.
     *
     * @param User $user
     * @param $ability
     * @return bool|void
     */
    public function before(User $user, $ability)
    {
        if(in_array($ability, self::$abilities)) {
            $policy_name = class_basename(static::class);
            $right_name = Str::camel(substr($policy_name, 0, strpos($policy_name, 'Policy')));

            $app_name = config('diagro.app_name');
            if ($user->hasApplication($app_name)) {
                $app = $user->applications()[$app_name];
                if ($app->hasPermission($right_name) && $app->permissions()[$right_name]->{$ability}) {
                    return true;
                }
            }

            return false;
        }
    }


    /**
     * Helper function to check if a user is owner of a record.
     *
     * @param User $user
     * @param Model $model
     * @return bool
     */
    protected function isOwner(User $user, Model $model)
    {
        return $user->id() == optional($model->user_id);
    }


    /**
     * Check for @can('read', $myModel).
     *
     * @param User $user
     * @param Model $model
     * @return bool|Response
     */
    public function read(User $user, ?Model $model = null)
    {
        if($model == null) {
            return $this->list($user);
        } else {
            return $this->readModel($user, $model);
        }
    }


    /**
     * Can read a list of records.
     *
     * @param User $user
     * @return mixed
     */
    protected function list(User $user)
    {
        return true;
    }


    /**
     * Can read a record of the model
     *
     * @param User $user
     * @param Model $model
     * @return mixed
     */
    protected function readModel(User $user, Model $model)
    {
        return true;
    }


    /**
     * Check for @can('create', MyModel.class).
     *
     * @param User $user
     * @param Model $model
     * @return bool|Response
     */
    public function create(User $user)
    {
        return true;
    }


    /**
     * Check for @can('update', $myModel).
     *
     * @param User $user
     * @param Model $model
     * @return bool|Response
     */
    public function update(User $user, Model $model)
    {
        return true;
    }


    /**
     * Check for @can('delete', $myModel).
     *
     * @param User $user
     * @param Model $model
     * @return bool|Response
     */
    public function delete(User $user, Model $model)
    {
        return true;
    }


    /**
     * Check for @can('publish', $myModel).
     *
     * @param User $user
     * @param Model $model
     * @return bool|Response
     */
    public function publish(User $user, Model $model)
    {
        return true;
    }


    /**
     * Check for @can('export', $myModel).
     *
     * @param User $user
     * @param Model $model
     * @return bool|Response
     */
    public function export(User $user, Model $model)
    {
        return true;
    }


}