<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;

/**
 * Trait for authorization checks using Spatie Permission
 * Use this trait in controllers to add role/permission checks
 */
trait AuthorizesRequest
{
    /**
     * Check if user has specific role
     *
     * @param string|array $roles
     * @param string $message
     * @return bool
     * @throws AuthorizationException
     */
    public function authorizeRole($roles, $message = 'Unauthorized')
    {

        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException($message);
        }

        if (!$user->hasAnyRole((array) $roles)) {
            throw new AuthorizationException($message);
        }

        return true;
    }

    /**
     * Check if user has specific permission
     *
     * @param string|array $permissions
     * @param string $message
     * @return bool
     * @throws AuthorizationException
     */
    public function authorizePermission($permissions, $message = 'Unauthorized')
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException($message);
        }

        if (!$user->hasAnyPermission((array) $permissions)) {
            throw new AuthorizationException($message);
        }

        return true;
    }

    /**
     * Check if user has role OR permission
     *
     * @param array $roles
     * @param array $permissions
     * @param string $message
     * @return bool
     * @throws AuthorizationException
     */
    public function authorizeRoleOrPermission($roles = [], $permissions = [], $message = 'Unauthorized')
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException($message);
        }

        $hasRole = !empty($roles) && $user->hasAnyRole($roles);
        $hasPermission = !empty($permissions) && $user->hasAnyPermission($permissions);

        if (!$hasRole && !$hasPermission) {
            throw new AuthorizationException($message);
        }

        return true;
    }

    /**
     * Check if user is admin or superadmin
     *
     * @param string $message
     * @return bool
     * @throws AuthorizationException
     */
    public function authorizeAdmin($message = 'Only admins can access this')
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException($message);
        }

        if (!$user->hasAnyRole(['admin', 'superadmin'])) {
            throw new AuthorizationException($message);
        }

        return true;
    }

    /**
     * Check if user is manager
     *
     * @param string $message
     * @return bool
     * @throws AuthorizationException
     */
    public function authorizeManager($message = 'Only managers can access this')
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException($message);
        }

        if (!$user->hasRole('manager')) {
            throw new AuthorizationException($message);
        }

        return true;
    }

    /**
     * Check if user is cashier
     *
     * @param string $message
     * @return bool
     * @throws AuthorizationException
     */
    public function authorizeCashier($message = 'Only cashiers can access this')
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException($message);
        }

        if (!$user->hasRole('cashier')) {
            throw new AuthorizationException($message);
        }

        return true;
    }
}
