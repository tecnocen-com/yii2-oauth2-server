<?php

use tecnocen\oauth2server\migrations\tables\CreateTable;

class m140501_075316_oauth_public_keys extends CreateTable
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
            'client_id' => $this->string(32)->notNull(),
            'public_key' => $this->string(200)->notNUll(),
            'private_key' => $this->string(200)->notNUll(),
            'encription_algorithm' => $this->string(100)->notNull()
                ->defaultValue('RS256'),
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

    /**
     * @inheritdoc
     */
    public function compositePrimaryKeys()
    {
        return ['client_id', 'public_key'];
    }
}
