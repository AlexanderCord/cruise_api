<?php
namespace GdsApiClient\components;

class ParseData
{
    private $_data;

    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    public function parseDictList()
    {
        $parseStr = '';
        if(is_array($this->_data) && !empty($this->_data)) {
            foreach($this->_data as $dict_name)
            {
                $parseStr .= $dict_name . '<br/>';
            }
        }

        return $parseStr;
    }

    public function parseDict()
    {
        $parseStr = '';
        if(is_array($this->_data) && !empty($this->_data)) {
            foreach($this->_data as $dict)
            {
                if(is_array($dict) && !empty($dict))
                {
                    $parseStr .= implode(';', $dict) . '<br/>';
                }
            }
        }

        return $parseStr;
    }

    public function parseCruiseList()
    {
        $parseStr = '';
        if(is_array($this->_data) && !empty($this->_data)) {
            foreach($this->_data as $cruise)
            {
                if(is_array($cruise) && !empty($cruise))
                {
                    foreach($cruise as $field => $field_val)
                    {
                        if(in_array($field, array('route', 'prices')))
                        {
                            $parseStr .= '--' . $field . ':<br/>';
                            if(is_array($field_val) && !empty($field_val))
                            {
                                foreach($field_val as $val_arr)
                                {
                                    if(is_array($val_arr) && !empty($val_arr))
                                    {
                                        foreach($val_arr as $val)
                                        {
                                            $parseStr .=  '----' . $val . '<br/>';
                                        }
                                    }
                                    $parseStr .=  '--------<br/>';
                                }
                            }
                        } else
                        {
                            $parseStr .= $field_val . '<br/>';
                        }
                    }

                    $parseStr .= '<br/><br/>';
                }
            }
        }

        return $parseStr;
    }

    public function parsePriceList()
    {
        $parseStr = '';
        if(is_array($this->_data) && !empty($this->_data)) {
            foreach($this->_data as $price)
            {
                if(is_array($price) && !empty($price))
                {
                    $parseStr .= implode(';', $price) . '<br/>';
                }
            }
        }

        return $parseStr;
    }

}