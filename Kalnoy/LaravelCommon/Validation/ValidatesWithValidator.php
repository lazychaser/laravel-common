<?php

namespace Kalnoy\LaravelCommon\Validation;

trait ValidatesWithValidator
{
    /**
     * @param DataValidator $validator
     */
    public function validateUsing(DataValidator $validator)
    {
        if ($validator->fails()) {
            $this->throwValidationException(app('request'), $validator);
        }
    }
}