<?php
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/phpseclib/');

require_once('Net/SSH2.php');
require_once('Net/SFTP.php');
require_once('Crypt/RSA.php');

class Wt_Import_Export_For_Woo_Sftp
{
	private $link = false;

    /**
    *   Test SFTP connection
    *   @param array $profile Profile details
    */
    public function test_sftp($profile, $out)
    {
        if($this->connect($profile['server'], $profile['port']))
        {
            if($this->login($profile['user_name'], $profile['password']))
            {
                $out['msg']=__('Successfully tested.');
                $out['status'] = true;
            }else
            {
                $out['msg']=__('SFTP connection failed.');
            }
        }else
        {
            $out['msg']=__('Failed to establish SFTP connection.');
        }
        return $out;
    }

	public function download($profile, $local_file, $remote_file, $out)
	{
		$out['response'] = false;
		if($this->connect($profile['server'], $profile['port']))
		{
			if($this->login($profile['user_name'], $profile['password']))
			{
                $file_data=$this->get_contents($remote_file);
                if(!empty($file_data))
                {
                    if(@file_put_contents($local_file, $file_data))
                    {
                        $out['msg']=__('Downloaded successfully.');
                        $out['response'] = true;
                    }else
                    {
                        $out['msg']=__('Unable to create temp file.');
                    }                       
                }else
                {
                    $out['msg']=__('Failed to download file.');
                }
			}else
			{
				$out['msg']=__('SFTP connection failed.');
			}
		}else
		{
			$out['msg']=__('Failed to establish SFTP connection.');
		}
		return $out;
	}
    public function upload($profile, $local_file, $remote_file, $out)
    {
        $out['response'] = false;
        if($this->connect($profile['server'], $profile['port']))
        {
            if($this->login($profile['user_name'], $profile['password']))
            {
                if($this->put_contents($remote_file, $local_file))
                {
                    $out['msg']=__('Uploaded successfully.');
                    $out['response'] = true;
                }else
                {
                    $out['msg']=__('Failed to upload file.');
                }
            }else
            {
                $out['msg']=__('SFTP login failed.');
            }
        }else
        {
            $out['msg']=__('Failed to establish SFTP connection.');
        }
        return $out;
    }
	private function login($username, $password)
	{
		return $this->link->login($username, $password) ? true : false;
	} 
    private function connect($hostname, $port = 22)
    {
        $this->link=new Net_SFTP($hostname, $port);
        return ($this->link ? true : false);
    }

    private function put_contents($file, $local_file)
    {
        $ret = $this->link->put($file, $local_file, NET_SFTP_LOCAL_FILE);
        return false !== $ret;
    }

    private function chmod($file, $mode = false, $recursive = false)
    {
        return $mode === false ? false : $this->link->chmod($mode, $file, $recursive);
    }

    private function get_contents($file)
    {
        return $this->link->get($file);
    }

    private function size($file) {
        $result = $this->link->stat($file);
        return $result['size'];
    }

    function get_contents_array($file) {
        $lines = preg_split('#(\r\n|\r|\n)#', $this->link->get($file), -1, PREG_SPLIT_DELIM_CAPTURE);
        $newLines = array();
        for ($i = 0; $i < count($lines); $i+= 2)
            $newLines[] = $lines[$i] . $lines[$i + 1];
        return $newLines;
    }
    
    function delete_file($file){
        return $this->link->delete($file);
    }
    
    function getErrors($when = '') {
        if (!empty($when) && $when == 'last') {
            return $this->link->getLastSFTPError();
        }
        return $this->link->getSFTPErrors();
    }
    
    function getLog(){
        return $this->link->getSFTPLog();
    }
    
    
    function nlist($dir = '.', $file_types = array(), $recursive = false){                
        $list = $this->link->nlist($dir, $recursive);
        if(empty($file_types)){
            return $list; //return all items if not specifying any file types
        }
        $collection = array();
        foreach ($list as $item => $value) {

            $item_pathinfo = pathinfo($dir . DIRECTORY_SEPARATOR . $value);

            $item_extension = isset($item_pathinfo['extension']) ? $item_pathinfo['extension'] : '';

            if (!empty($file_types) && !in_array($item_extension, $file_types)) {
                continue;
            }

            $collection[$item] = $value;
        }
        return $collection;
    }
    
    
    function rawlist($dir = '.', $file_types = array(), $recursive = false) {
        $list = $this->link->rawlist($dir, $recursive);
        if(empty($file_types)){
            return $list; //return all items if not specifying any file types
        }
        $collection = array();
        foreach ($list as $item => $value) {

            $item_pathinfo = pathinfo($dir . DIRECTORY_SEPARATOR . $item);

            $item_extension = isset($item_pathinfo['extension']) ? $item_pathinfo['extension'] : '';

            if (!empty($file_types) && !in_array($item_extension, $file_types)) {
                continue;
            }

            $collection[$item] = $value;
        }
        return $collection;
    }
}
