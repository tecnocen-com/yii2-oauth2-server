<?php

use tecnocen\rmdb\migrations\CreatePivot;

class m140501_015314_oauth_authorization_codes extends CreatePivot
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
            'client_id' => 'oauth_clients',
        ];
    }
}
