<?php

namespace tecnocen\oauth2server\filters;

/**
 *
 */
class ErrorToExceptionFilter extends \yii\base\ActionFilter
{
    use ErrorToExceptionTrait;
}
