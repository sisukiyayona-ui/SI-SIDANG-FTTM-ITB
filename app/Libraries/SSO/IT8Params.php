<?php

namespace App\Libraries\SSO;

/**
 * IT8 Extension Attributes
 */
class IT8Params
{
    protected $_propDict = [];

    public function __construct($initialData = [])
    {
        $this->_propDict = $initialData;
    }

    public function getProp($key)
    {
        return isset($this->_propDict[$key]) ? $this->_propDict[$key] : null;
    }

    public function setProp($key, $value)
    {
        $this->_propDict[$key] = $value;
    }

    public function getAllProps()
    {
        return $this->_propDict;
    }

    public function getNIP()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_it8NIP");
    }

    public function getNIM()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_it8NIM");
    }

    public function getPenugasan()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_it8JenisPenugasan");
    }

    public function getJenisPegawai()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_it8JenisPegawai");
    }

    public function getProxy()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_itbProxyStatus");
    }

    public function getEmailNonITB()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_it8EmailNonITB");
    }

    public function getJenisKelamin()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_it8JenisKelamin");
    }

    public function getNoreg()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_it8NomerRegistrasi");
    }

    public function getPenempatanKerja()
    {
        return $this->getProp("extension_59b1079e66434b8cbcade105f0db3466_it8PenempatanKerja");
    }
}
