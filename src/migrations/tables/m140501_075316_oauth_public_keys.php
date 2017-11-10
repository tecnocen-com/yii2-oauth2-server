<?php

use tecnocen\rmdb\migrations\CreatePivot;

class m140501_015316_oauth_public_keys extends CreateTable
{
    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return 'oauth_public_keys';
    }

    /**
     * @inheritdoc
     */
    public function columns()
    {
        return [
            'client_id' => $this->string(40)->notNull(),
            'public_key' => $this->string(2000)->notNUll(),
            'private_key' => $this->string(2000)->notNUll(),
            'encription_algorithm' => $this->string(100)->notNull()
                ->defaultValue('RS256'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function compositePrimaryKeys()
    {
        return ['client_id', 'public_key'];
    }
}
