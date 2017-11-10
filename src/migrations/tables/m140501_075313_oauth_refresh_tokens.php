<?php

use tecnocen\rmdb\migrations\CreatePivot;

class m140501_015313_oauth_refresh_tokens extends CreatePivot
{
    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return 'oauth_refresh_token';
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
            'client_id' => 'oauth_clients',
        ];
    }
}
