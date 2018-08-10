<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components;

use tecnocen\oauth2server\exceptions\HttpTokenException;
use Yii;
use yii\helpers\Json;

/**
 * ErrorHandler handles uncaught PHP errors and exceptions.
 *
 * ErrorHandler displays these errors using appropriate views based on the
 * nature of the errors and the mode the application runs at.
 *
 * ErrorHandler is configured as an application component in [[\yii\base\Application]] by default.
 * You can access that instance via `Yii::$app->errorHandler`.
 *
 * For more details and usage information on ErrorHandler, see the [guide article on handling errors](guide:runtime-handling-errors).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Timur Ruziev <resurtm@gmail.com>
 * @since 2.0
 */
class ErrorHandler extends \yii\web\ErrorHandler
{

    /**
     * @inherit
     */
    protected function convertExceptionToArray($exception)
    {
        if ($exception instanceof HttpTokenException) {
            return Json::decode($exception->getMessage());
        }

        return parent::convertExceptionToArray($exception);
    }

}
