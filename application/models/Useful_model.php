<?php
	class useful_model extends CI_Model {

		function __construct() {
			parent::__construct();
		}

		function get_where($table, $col, $value){
			$this->db->where($col, $value);
			$query=$this->db->get($table);
			return $query;
		}

		function get_where_custom($table, $col, $value, $col2, $value2) {
			$this->db->where($col, $value);
			$this->db->where($col2, $value2);
			$query=$this->db->get($table);
			return $query;
		}

		function _insert($table, $data){
			$this->db->insert($table, $data);
		}

		function _update($table,$col, $param, $data){
			$this->db->where($col, $param);
			$this->db->update($table, $data);
		}
	}
?>