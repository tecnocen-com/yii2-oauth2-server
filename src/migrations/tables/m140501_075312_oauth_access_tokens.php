<?php

use tecnocen\rmdb\migrations\CreatePivot;

class m140501_015312_oauth_access_tokens extends CreatePivot
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
            'access_token' => $this->primaryKey(40),
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
