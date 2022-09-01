<?php
class bookMark {
    public function __construct($page, $query_type, $filter) {
        $this->uid         = $filter['uid'];
        $this->hidden      = $filter['hidden'];
        $this->private     = $filter['private'];
        $this->membership  = $filter['membership']; 
        $this->public      = $filter['public'];
        $this->page        = $page; 
        $this->query_type  = $query_type;
    }

    private $hidden;
    private $private;
    private $membership;
    private $public; 
    
    private $obj           = 0;
    private $query_type;   
    private $uid           = null;
    private $page          = null; 
    private $url           = null; 
        
    private function buildUrl() {
    /*    $this->url ='"../'.$this->page.
                    '?obj_id='.$this->obj.
                    '&uid='.$this->uid.
                    '&hidden='.$this->hidden.
                    '&private='.$this->private.
                    '&membership='.$this->membership.
                    '&public='.$this->public.  
                    '"';
    */                
        $this->url = '"../'.$this->page.'"';            
        return $this->url;             
    }
    
    public function __set($item, $value) { 
        if ($item == 'filter') {
            $this->uid         = $value['uid']; 
            $this->hidden      = $value['hidden'];
            $this->private     = $value['private'];
            $this->membership  = $value['membership'];
            $this->public      = $value['public']; 
            return;  
        }
        
        if ($item == 'url') {
            return;
        }

        $this->$item = $value; 
        return; 
    }
    
    public function __get($item) {
        if ($item == 'url') {
            return $this->buildUrl();  
        }
        
        if ($item == 'filter') {
            if (!isset($this->hidden))     {$this->hidden = false;}  
            if (!isset($this->private))    {$this->private = false;}
            if (!isset($this->membership)) {$this->membership = false;}  
            if (!isset($this->public))     {$this->public = false;} 
            
            $f = array('uid'         => $this->uid,
                       'hidden'      => $this->hidden,
                       'private'     => $this->private,
                       'membership'  => $this->membership,
                       'public'      => $this->public);
            return $f;                    
        }

           
        return $this->$item;
    }
}
?>
