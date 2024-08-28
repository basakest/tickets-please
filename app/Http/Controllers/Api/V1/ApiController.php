<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    protected string $policyClass;

    public function __construct()
    {
        Gate::guessPolicyNamesUsing(function () {
            return $this->policyClass;
        });
    }

    public function include(string $relationship): bool
    {
        $param = request()->get('include');
        if (! isset($param)) {
            return false;
        }
        $includeValues = explode(',', strtolower($param));
        return in_array($relationship, $includeValues);
    }

    /**
     * @throws AuthorizationException
     */
    public function isAble($ability, $targetModel): Response
    {
        return $this->authorize($ability, [$targetModel]);
        // 看了下相关的代码, $argument 这个 array 会被 unpack 掉当作 policy 类对应方法的参数(从第二个开始, 第一个参数是解析出的当前登录用户实例)
        // 但 policy 类对应的方法完全没有接收第三个参数, 传递 $this->policyClass 的意义是什么呢
        // return $this->authorize($ability, [$targetModel, $this->policyClass]);
    }
}
