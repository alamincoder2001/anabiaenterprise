<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Report</title>
    <link rel="stylesheet" href="<?php echo base_url();?>assets/css/bootstrap.min.css">
    <style>
        body{
            padding: 20px!important;
        }
        body, table{
            font-size: 13px;
        }
        table th{
            text-align: center;
        }
    </style>
</head>
<body>

            <?php 
            $companyInfo = $this->db->query("select * from tbl_company order by Company_SlNo desc limit 1")->row();
            $currentBranch = $this->db->query("select * from tbl_brunch where brunch_id = ?", $this->session->userdata("BRANCHid"))->row();
            $branchId = $this->session->userdata('BRANCHid');
                    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-2"><img src="<?php echo base_url();?>uploads/company_profile_thum/<?php echo $companyInfo->Company_Logo_org; ?>" alt="Logo" style="height:80px;" /></div>
            <div class="col-xs-10" style="padding-top:20px;">
                <strong style="font-size:18px;"><?php echo $currentBranch->Brunch_name; ?></strong><br>
                <p style="white-space: pre-line;"><?php echo $currentBranch->Brunch_address; ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div style="border-bottom: 4px double #454545;margin-top:7px;margin-bottom:7px;"></div>
            </div>
        </div>
    </div>
</body>
</html>