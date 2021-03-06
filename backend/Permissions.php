<?php
/**
 * Permissions.php
 * @author Revin Roman
 * @link https://rmrevin.com
 */

namespace cookyii\modules\Postman\backend;

use cookyii\interfaces\PermissionsModuleDictInterface;
use rmrevin\yii\rbac\RbacFactory;

/**
 * Class Permissions
 * @package cookyii\modules\Postman\backend
 */
class Permissions implements PermissionsModuleDictInterface
{

    const ACCESS = 'backend.postman.access';

    /**
     * @return array
     */
    public static function get()
    {
        return [
            RbacFactory::Permission(static::ACCESS, 'It has access to postman backend module'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function rules()
    {
        return [];
    }
}
