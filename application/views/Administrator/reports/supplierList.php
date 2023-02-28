<?php
$brunch=$this->session->userdata('BRANCHid');
?>
<!DOCTYPE html>
<html>
<head>
<title> </title>
<meta charset='utf-8'>
        <link href="<?php echo base_url()?>assets/css/prints.css" rel="stylesheet" />
</head>
<style type="text/css" media="print">
.hide{display:none}

</style>
<script type="text/javascript">
window.onload = async () => {
		await new Promise(resolve => setTimeout(resolve, 1000));
		window.print();
		window.close();
	}
</script>
<body style="background:none;">


      <table width="800px" >
         <tr>
          <td align="right" width="150"><img src="<?php echo base_url();?>uploads/company_profile_thum/<?php echo $branch_info->Company_Logo_org;; ?>" alt="Logo" style="width:100px;" /></td>
          <td align="left" width="750">
            <div class="">
				<div style="text-align:center;" >
				<strong style="font-size:18px;"><?php echo $branch_info->Company_Name; ?></strong><br>
				<?php echo $branch_info->Repot_Heading; ?><br>
              </div>
			</div>
          </td>
        </tr>
		
        <tr>
          <td style="float:right">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="250px" style="text-align:right;"><strong></td>
              </tr>
          </table>
          </td>
        </tr>
        <tr>
            <td colspan="2"> 
                <table class="border" cellspacing="0" cellpadding="0" width="100%">
                    <thead>
                        <tr class="header">
                            <th style="width:12%;text-align:center;">SL NO</th>
                            <th style="width:12%;text-align:center;">ID</th>
                            <th style="width:25%;text-align:center;">Supplier Name</th>
                            <th style="width:25%;text-align:center;">Address</th>
                            <th style="width:25%;text-align:center;">Contact No</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
						$sql = $this->db->query("SELECT * FROM tbl_supplier where Supplier_brinchid='$brunch' order by Supplier_Code asc");
						$result =$sql->result();
						$i=1;
						foreach($result as $row){ ?>
                        <tr align="center">
                            <td style="width:12%"><?php echo $i++; ?></td>
                            <td style="width:12%"><?php echo $row->Supplier_Code; ?></td>
                            <td style="width:25%"><?php echo $row->Supplier_Name; ?></td>
                            <td style="width:25%"><?php echo $row->Supplier_Address; ?></td>
                            <td style="width:25%"><?php echo $row->Supplier_Mobile; ?></td>
                            
                        </tr>  
                    <?php } ?>                
                    </tbody>
                </table>
            </td>
        </tr>
       
    </table>

<div class="provied">
  <span style="float:left;font-size:11px;">Software Provied By Link-Up Technology</span>
</div>
</body>
</html>

