<?php

namespace App\Http\Middleware;

use App\Traits\ApiStatusTrait;
use Closure;
use Illuminate\Http\Request;

class Student
{
    use ApiStatusTrait;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * role 2 instructor
         * role 3 student
         * role 4 organization
         * instructor & student & organization both can access student panel
         */

        if (file_exists(storage_path('installed'))) {
             // Define exceptions (route names or paths)
            $exceptions = [
                'student.cartList', // by route name
                'student.goToCheckout',
                'student.cartListInvovice',
                'student.checkout',
                'student.pay',
                'student.checkoutInvoice',
                'student.addToCart',
                'student.addToCartWithCheckout',
                'student.cartDelete',
                // or 'cart-list' if you want to match URI
            ];
              // If current route is in exceptions → skip status check
            if ($request->routeIs($exceptions)) {
                return $next($request);
            }


            if (in_array(auth()->user()->role, [USER_ROLE_STUDENT, USER_ROLE_INSTRUCTOR, USER_ROLE_ORGANIZATION])) {
                if (auth()->user()->student->status == STATUS_APPROVED) {
                    return $next($request);
                } else {
                    if ($request->wantsJson()) {
                        $msg = __("Unauthorize route");
                        return $this->error([], $msg, 403);
                    } else {
                        abort('403');
                    }
                }
            } else {
                if ($request->wantsJson()) {
                    $msg = __("Unauthorize route");
                    return $this->error([], $msg, 403);
                } else {
                    abort('403');
                }
            }
        } else {
            if ($request->wantsJson()) {
                $msg = __("Application is not installed");
                return $this->error([], $msg, 404);
            } else {
                return redirect()->to('/install');
            }

        }
    }
}
