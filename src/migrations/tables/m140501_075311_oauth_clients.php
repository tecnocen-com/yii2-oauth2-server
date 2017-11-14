<?php

use tecnocen\oauth2server\migrations\tables\CreateTable;

class m140501_075311_oauth_clients extends CreateTable
{
    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return 'oauth_clients';
    }

    /**
     * @inheritdoc
     */
    public function columns()
    {
        return [
            'client_id' => $this->primaryKey(32),
            'client_secret' => $this->string(32)->notNull(),
            'redirect_uri' => $this->string(1000)->notNull(),
            'grant_types' => $this->string(100)->notNull(),
            'scope' => $this->string(2000)->notNull()->defaultValue(null),
            'user_id' => $this->integer()->defaultValue(null),
        ];
    }
}
