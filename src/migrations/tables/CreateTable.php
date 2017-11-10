<?php

namespace tecnocen\oauth2server\migrations\tables;

class CreateTable extends \tecnocen\migrate\CreateTable
{
    /**
     * @inheritdoc
     */
    public function primaryKey($length = self::DEFAULT_KEY_LENGTH)
    {
        return $this->string($length)->notNull()->append('PRIMARY KEY');
    }
}
