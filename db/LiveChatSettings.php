<?php  
/**
* 
*/
class LiveChatSettings
{

	protected $db;	
	protected $table;	
 
	function __construct($db, $table)
	{
		$this->db = $db;
		$this->table = $table;
	}

	public function getClientSites($partner_id)
	{ 
		$response = $this->db->wpdb_get_result("select * from " . $this->table . " where s_accountid = " . $partner_id); 
		return $response;
	}  

}