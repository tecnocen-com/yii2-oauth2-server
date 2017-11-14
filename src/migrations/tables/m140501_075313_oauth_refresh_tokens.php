<?php

use tecnocen\oauth2server\migrations\tables\CreateTable;

class m140501_075313_oauth_refresh_tokens extends CreateTable
{
    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return 'oauth_refresh_tokens';
    }

    /**
     * @inheritdoc
     */
    public function columns()
    {
        return [
            'refresh_token' => $this->primaryKey(40),
            'client_id' => $this->string(32)->notNull(),
            'user_id' => $this->integer()->defaultValue(null),
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
