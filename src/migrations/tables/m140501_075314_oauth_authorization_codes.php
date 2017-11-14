<?php

use tecnocen\oauth2server\migrations\tables\CreateTable;

class m140501_075314_oauth_authorization_codes extends CreateTable
{
    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return 'oauth_authorization_codes';
    }

    /**
     * @inheritdoc
     */
    public function columns()
    {
        return [
            'authorization_code' => $this->primaryKey(40),
            'client_id' => $this->string(32)->notNull(),
            'user_id' => $this->normalKey()->null()->defaultValue(null),
            'redirect_uri' => $this->string(1000)->notNull(),
            'expires' => $this->datetime()->notNull(),
            'scope' => $this->string(2000)->notNull()->defaultValue(null),
        ];
    }

    /**
     * @inheritdoc
     */
    public function foreignKeys()
    {
        return [
            'client_id' => [
                'table' => 'oauth_clients',
                'columns' => ['client_id' => 'client_id'],
            ],
        ];
    }
}
