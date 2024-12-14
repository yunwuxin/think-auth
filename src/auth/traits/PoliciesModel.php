<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2018/8/6
 * Time: 14:44
 */

namespace yunwuxin\auth\traits;

use yunwuxin\auth\Collection;
use yunwuxin\facade\Gate;

/**
 * @mixin \think\Model
 */
trait PoliciesModel
{
    public function withPolicies($abilities)
    {
        if (is_string($abilities)) {
            $abilities = explode(',', $abilities);
        }

        $this->withFieldAttr('policies', function () use ($abilities) {
            $data = [];
            foreach ($abilities as $ability) {
                $data[$ability] = Gate::can($ability, $this);
            }
            return $data;
        });

        $this->append(['policies']);

        return $this;
    }

    public function toCollection(iterable $collection = [], ?string $resultSetType = null): \think\Collection
    {
        return parent::toCollection($collection, Collection::class);
    }
}
