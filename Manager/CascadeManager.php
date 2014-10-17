<?php

namespace SDLab\Bundle\SmartUtilsBundle\Manager;

use Symfony\Component\PropertyAccess\PropertyAccess;

class CascadeManager
{
    private $options;
    
    private $levelNb;

    /**
     * @param array|Collection $data
     * @param array $options
     * @return array
     */
    public function manage($data, $options)
    {
        $this->options = $options;
        $this->levelNb = count($options);
        
        return $this->manageLevel($data, 1);
    }
    
    /**
     * @param array|Collection $data
     * @param integer $level
     * @return array
     */
    private function manageLevel($data, $level)
    {
        if($level > $this->levelNb) {
            return array();
        }
        
        $accessor = PropertyAccess::createPropertyAccessor();
        
        $labelAccessor = isset($this->options[$level]['labelAccessor']) ? $this->options[$level]['labelAccessor'] : null;
        $valueAccessor = isset($this->options[$level]['valueAccessor']) ? $this->options[$level]['valueAccessor'] : null;
        $groupAccessor = isset($this->options[$level]['groupAccessor']) ? $this->options[$level]['groupAccessor'] : null;
        $extraDataAccessor = isset($this->options[$level]['extraDataAccessor']) ? $this->options[$level]['extraDataAccessor'] : null;
        
        
        $cascadeData = array();
        foreach($data as $element) {

            if($level < $this->levelNb) {
                $nextLevelAccessor = isset($this->options[$level]['nextLevelAccessor']) ? $this->options[$level]['nextLevelAccessor'] : null;
                $nextLevelData = $nextLevelAccessor ? $accessor->getValue($element, $nextLevelAccessor) : null;
                $children = $this->manageLevel($nextLevelData, $level + 1);
            } else {
                $children = null;
            }
            

            $cascadeDataRow = array(
                'label' => $labelAccessor ? $accessor->getValue($element, $labelAccessor) : (string) $element,
                'value' => $valueAccessor ? $accessor->getValue($element, $valueAccessor) : uniqid(),
                'children' => $children
            );
            
            if(null != $groupAccessor) {
                $cascadeDataRow['group'] = $accessor->getValue($element, $groupAccessor);
            }
            
            if(null != $extraDataAccessor) {
                $cascadeDataRow['data'] = $accessor->getValue($element, $extraDataAccessor);
            }
            
            $cascadeData[] = $cascadeDataRow;

        }            
        
        return $cascadeData;
    }
    
    
}
