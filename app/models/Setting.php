<?php

/**
 * Setting Model
 * Untuk system configuration management
 */
class Setting extends Model {
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description', 'order', 'is_public'];
    protected $timestamps = true;
    protected $softDelete = false;
    
    /**
     * Get setting by key
     */
    public function getByKey($key) {
        return $this->findOne(['key' => $key]);
    }
    
    /**
     * Get settings by group
     */
    public function getByGroup($group) {
        return $this->findAll(['group' => $group], 'order ASC');
    }
    
    /**
     * Get all settings as key-value pairs
     */
    public function getAllSettings() {
        $settings = $this->findAll([], 'order ASC');
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }
        
        return $result;
    }
    
    /**
     * Update or create setting
     */
    public function set($key, $value, $type = 'string', $group = 'general') {
        $existing = $this->getByKey($key);
        
        if ($existing) {
            return $this->update($existing['id'], ['value' => $value]);
        } else {
            return $this->create([
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'group' => $group
            ]);
        }
    }
    
    /**
     * Get setting value with type casting
     */
    public function get($key, $default = null) {
        $setting = $this->getByKey($key);
        
        if (!$setting) {
            return $default;
        }
        
        return $this->castValue($setting['value'], $setting['type']);
    }
    
    /**
     * Cast value based on type
     */
    private function castValue($value, $type) {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'number':
                return is_numeric($value) ? (strpos($value, '.') !== false ? (float) $value : (int) $value) : $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
