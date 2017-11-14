<?php

namespace tecnocen\oauth2server\migrations\tables;

abstract class CreateTable extends \tecnocen\migrate\CreateTableMigration
{
    /**
     * @inheritdoc
     */
    public function primaryKey($length = self::DEFAULT_KEY_LENGTH)
    {
        return $this->string($length)->notNull()->append('PRIMARY KEY');
    }
}
