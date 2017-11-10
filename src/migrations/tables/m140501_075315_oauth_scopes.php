<?php

use tecnocen\rmdb\migrations\CreatePivot;

class m140501_015315_oauth_scopes extends CreatePivot
{
    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return 'oauth_scopes';
    }

    /**
     * @inheritdoc
     */
    public function columns()
    {
        return [
            'scope' => $this->primaryKey(2000),
            'is_default' => $this->active(),
        ];
    }
}
