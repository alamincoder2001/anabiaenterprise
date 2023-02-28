<?php
/**
 * Created by PhpStorm.
 * User: Arup
 * Date: 11/29/2018
 * Time: 3:33 PM
 */

class HR_model extends CI_Model
{
	public $BRANCHid;

	public function __construct()
	{
		$this->BRANCHid=$this->session->userdata('BRANCHid');
	}

	public function get_all_employee_list()
	{
		$this->db->select('tbl_employee.*,tbl_department.*,tbl_designation.*')->from('tbl_employee');
		$this->db->join('tbl_department', 'tbl_department.Department_SlNo=tbl_employee.Department_ID', 'left');
		$this->db->join('tbl_designation', 'tbl_designation.Designation_SlNo=tbl_employee.Designation_ID', 'left');
		$this->db->where('tbl_employee.status', 'a');
		$this->db->where('tbl_employee.Employee_brinchid', $this->BRANCHid);
		$res = $this->db->order_by('tbl_employee.Employee_Name', 'asc')->get()->result();

		if($res){
			return $res;
		}else{
			return false;
		}
	}
}