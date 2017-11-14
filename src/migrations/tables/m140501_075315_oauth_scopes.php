<?php

use tecnocen\oauth2server\migrations\tables\CreateTable;

class m140501_075315_oauth_scopes extends CreateTable
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
            'scope' => $this->primaryKey(200),
            'is_default' => $this->boolean(),
        ];
    }
}
