<?php
/**
 * Created by PhpStorm.
 * User: yunwuxin
 * Date: 2018/8/6
 * Time: 14:52
 */

namespace yunwuxin\auth\traits;

trait PoliciesCollection
{

    public function withPolicies($abilities)
    {
        $this->each(function ($model) use ($abilities) {
            /** @var PoliciesModel $model */
            $model && $model->withPolicies($abilities);
        });

        return $this;
    }
}