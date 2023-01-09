<?php

namespace App\Http\Middleware;

use App\Models\Module;
use App\Models\User;
use App\Utils\HttpMethodUtil;
use App\Utils\JsonUtil;
use Closure;
use Illuminate\Http\Request;

class ModuleAccessMW
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

    public function handle(Request $request, Closure $next)
    {

        $user = new User();
        $module = new Module();

        $action = $request->get('action');
        $userId = 1;
        $userRoles = $user->getUser($userId) != null ? explode(",", $user->getUser($userId)->role) : [];

        $uri = $_SERVER['REQUEST_URI'];
        $uri = ltrim($uri, '/');
        $uri = rtrim($uri, '/');
        $uri = explode("?", $uri)[0];
        $uri = explode("/", $uri);

        $minSegment = 2; // live server = 1, local server = 2

        if (count($uri) > $minSegment) {

            $moduleSlug = $uri[1];
            $subModuleSlug = $uri[2];

            $subModules = $module->getSubModuleAccessBySlug($moduleSlug, $subModuleSlug);

            $canAccess = false;

            foreach ($subModules as $sm) {
                if (in_array($sm->role_id, $userRoles)) {
                    switch ($action) {
                        case 'add':
                            if ($sm->create == 1) {
                                $canAccess = true;
                                break 2;
                            }
                            break;
                        case 'edit':
                            if ($sm->update == 1) {
                                $canAccess = true;
                                break 2;
                            }
                            break;
                        case 'delete':
                            if ($sm->delete == 1) {
                                $canAccess = true;
                                break 2;
                            }
                            break;
                        case 'read':
                            if ($sm->read == 1) {
                                $canAccess = true;
                                break 2;
                            }
                            break;
                        default:
                            if ($sm->read == 1) {
                                $canAccess = true;
                                break 2;
                            }
                            break;
                    }
                }
            }

            if (!$canAccess) {
                if (HttpMethodUtil::isMethodGet()) {
                    return abort(403);
                } else {
                    return JsonUtil::accessForbidden();
                }
            }
        }

        $moduleAccess = [];

        foreach ($module->getModuleAccessAll() as $ma) {
            if (count(array_intersect($userRoles, explode(",", $ma->role))) > 0) {
                array_push($moduleAccess, $ma->slug);
            }
        }

        $request->merge([
            'module_access' => $moduleAccess,
        ]);

        return $next($request);
    }
}
