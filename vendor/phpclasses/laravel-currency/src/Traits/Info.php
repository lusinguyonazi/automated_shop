<?php

namespace RaggiTech\Laravel\Currency\Traits;

trait Info
{

    /**
     * Get Model Name
     */
    public function getModelType()
    {
        if (!$this->RaggiTech_Model_Type) $this->RaggiTech_Model_Type = get_class($this);
        return $this->RaggiTech_Model_Type;
    }

    /**
     * Get Model Primary Key
     */
    public function getModelKey()
    {
        if (!$this->RaggiTech_Model_Key) $this->RaggiTech_Model_Key = $this->primaryKey;
        return $this->RaggiTech_Model_Key;
    }
}
