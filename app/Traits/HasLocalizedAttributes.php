<?php

namespace App\Traits;

trait HasLocalizedAttributes
{
    /**
     * Get the localized attributes for this model
     * Automatically detects fields ending with _ar and _en
     * 
     * @return array Array of base field names (without _ar/_en suffix)
     */
    protected function getLocalizedAttributes(): array
    {
        $localized = [];
        $fillable = $this->getFillable();
        
        foreach ($fillable as $field) {
            if (str_ends_with($field, '_ar')) {
                $baseField = str_replace('_ar', '', $field);
                $enField = $baseField . '_en';
                
                // Check if _en field exists
                if (in_array($enField, $fillable) || $this->hasAttribute($enField)) {
                    $localized[] = $baseField;
                }
            }
        }
        
        return $localized;
    }

    /**
     * Boot the trait
     */
    protected static function bootHasLocalizedAttributes()
    {
        static::retrieved(function ($model) {
            $model->setupLocalizedAttributes();
        });
    }

    /**
     * Setup hidden and appended attributes for localized fields
     */
    protected function setupLocalizedAttributes()
    {
        $localizedFields = $this->getLocalizedAttributes();
        
        if (empty($localizedFields)) {
            return;
        }

        $hiddenFields = [];
        $appendedFields = [];

        foreach ($localizedFields as $baseField) {
            $arField = $baseField . '_ar';
            $enField = $baseField . '_en';
            
            $hiddenFields[] = $arField;
            $hiddenFields[] = $enField;
            $appendedFields[] = $baseField;
        }

        // Merge with existing hidden/appended arrays
        $this->hidden = array_unique(array_merge($this->hidden ?? [], $hiddenFields));
        $this->appends = array_unique(array_merge($this->appends ?? [], $appendedFields));
    }

    /**
     * Get localized attribute value
     */
    protected function getLocalizedValue($baseField)
    {
        $locale = app()->getLocale();
        $arField = $baseField . '_ar';
        $enField = $baseField . '_en';
        
        if ($locale === 'ar') {
            return $this->attributes[$arField] ?? null;
        }
        
        // For English, prefer _en, fallback to _ar if _en is null
        return $this->attributes[$enField] ?? $this->attributes[$arField] ?? null;
    }

    /**
     * Handle dynamic accessor calls (e.g., getNameAttribute)
     */
    public function __call($method, $parameters)
    {
        // Check if it's an accessor call (getXxxAttribute)
        if (str_starts_with($method, 'get') && str_ends_with($method, 'Attribute')) {
            $baseField = lcfirst(str_replace(['get', 'Attribute'], '', $method));
            $localizedFields = $this->getLocalizedAttributes();
            
            if (in_array($baseField, $localizedFields)) {
                return $this->getLocalizedValue($baseField);
            }
        }

        return parent::__call($method, $parameters);
    }

    /**
     * Magic method to handle dynamic accessors for localized fields
     */
    public function __get($key)
    {
        $localizedFields = $this->getLocalizedAttributes();
        
        if (in_array($key, $localizedFields)) {
            return $this->getLocalizedValue($key);
        }

        return parent::__get($key);
    }
}

