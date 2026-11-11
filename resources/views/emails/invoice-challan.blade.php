<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
  

    <style>
@page { margin: 5px; margin-top: 95px; }
body { margin: 5px; margin-top: 95px; }
* { box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; }

/* layout: three columns side-by-side */
.row {
  /* clearfix */
  overflow: hidden;
}

/* use floats (more reliable with mPDF) */
.column {
  float: left;
  width: 30.333%;                /* three equal columns */
  padding: 5px;                  /* internal spacing */
  box-sizing: border-box;
  border-right: 1px dotted #000; /* vertical divider */
  vertical-align: top;
  page-break-inside: avoid;      /* keep column content on same page */
}

/* remove right border on last column so total width stays sane */
.column:last-child {
  border-right: none;
}

/* small helpers */
.bold{ font-weight: bold; }
.mb--15{ margin-bottom: -15px; }
.d-inline-block{ display: inline-block; font-size: 14px; }
.align-right{ text-align: right; }
.align-left{ text-align: left; }
.align-center{ text-align: center; margin: 0 auto; }
.space-between { text-align: justify; }
.space-between::after { content: ""; width: 100%; display: inline-block; }
.float-right{ float: right; }
.float-left{ float: left; }
.circle-logo{ width: 60px; }
.logo{ width: 220px; }
.title{ margin-top: 5px; }
.student-name{ margin-bottom: 10px; }
.bar-code{ width: 200px; margin-top: 5px; margin-bottom: 10px; }

/* table: make it fill the column and not overflow */
table {
  margin-top: 10px;
  border: 1px solid #ccc;
  border-collapse: collapse;
  margin: 0;
  padding: 0;
  width: 100%;        /* use full column width */
  table-layout: fixed;
}
table tr {
  background-color: #fff;
  border: 1px solid #000;
  padding: .35em;
}
table th,
table td {
  padding: .625em;
  border: 1px solid #000;
  word-wrap: break-word;
}

/* optional: reduce font-size slightly so everything fits better */
body, table, td, th, span { font-size: 10px; }

/* clearfix for safety after columns */
.row:after { content: ""; display: table; clear: both; }

/* prevent page break inside important elements if needed */
thead { display: table-header-group; }
tbody { display: table-row-group; }


</style>

</head>
<body>
    <div class="row">
    <div class="column">
        <div class="space-between mb--10">
                 <?php 
                 
                    $year=$data['year'];
                    $id=$data['id'];
                    ?>
                <div class="d-inline-block " >Invoice # <?php echo $year; ?>/ <?php echo $id; ?></div>
                <div class="d-inline-block "style="margin-right: 5px;" >Bank Copy</div>
            </div>
            <div class="align-center">
         
                <h4 class="align-center title">Character Education Foundation</h4>
                <span class="align-center title">CEF Online Project</span>
            </div>
        </br>
            <div class="align-left">
                    <span class="align-left student-name">Student Name: <?php echo $data['s_name'] ; ?></span><br>
                <?php 
                $a=$data['s_gender'];
                if($a=='Male'){
                    $b='S/O: ';
                }
                else{
                    $b='D/O: ';
                }
                ?>
          
                <span class="align-left"><?php if($data['s_parent_name']) {echo $b;} ?></strong><?php if($data['s_parent_name']) {echo $data['s_parent_name'] ;} ?></strong></span>
               
            </div>
         
            <div class="space-between mb--10">
            <div class="align-left ">
                    <?php 
                     $new=date('d-m-Y',strtotime($data['payment_date']));
                    ?>
                    <span>Issue Date:</span>
                    <span><?php echo $new ; ?></span>
                </div>
            {{-- <div class="align-left ">
                    <?php 
                    $new=date('d-m-Y');
                    $a=date('d-m-Y',strtotime($data['payment_date']));
                    $due_date = strtotime($a); 
                    $b = strtotime("+30 days",$due_date) ;
                    $due_date=date('d-m-Y',$b);
                    ?>
                    <span>Due Date:</span>
                    <span><?php echo $due_date ; ?></span>
            </div> --}}

                <div class="align-left ">
                    <?php 
                    $month=$data['month'];
                    ?>
                    <span>Month:</span>
                    <span><?php echo $month ; ?></span>
                </div>

                
              
                <div class="align-left">
                    <span>Bank Islami Multan Road Branch</span>
                    <span>PK20BKIP0205400315500001</span>
                </div>

                {{-- <div class="align-left" style="margin-left: -3px;">
                    <span>Title:</span>
                    <span>CEF-Character Education Foundation</span>
                </div> --}}
            </div>

             <table style="margin-top: 5px;">
                <thead>
                    <tr>
                        <th scope="col left" colspan="2">Description</th>
                        <th scope="col right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>  <!--------Course Amount --->
                        <td data-label="Account" colspan="2"><?php echo $data['course_name'] ; ?></td>
                        <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                            $symbol='Rs';
                       }
                        if($data['discount_type'] == 0){
                            $discountValue = $data['discount'];
                        }
                        else if($data['discount_type'] == 1){
                            $temp1 = $data['discount']/100;
                            $discountValue = $data['net_course_price_1'] * $temp1;  
                        }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['course_price_1'] ; ?> (-{{$discountValue}}) ({{$data['net_course_price_1']}})</td>

                    </tr>

                    <tr>  <!--------Admission Fee Amount --->
                        <td scope="row" data-label="Account" colspan="2">Admission Fee</td>
                          <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                            $symbol='Rs';
                       }
                        if($data['discount_type_admission_fee'] == 0){
                            $discountValueAdm = $data['discount_admission_fee'];
                        }
                        else if($data['discount_type_admission_fee'] == 1){
                            $temp2 = $data['discount_admission_fee']/100;
                            $discountValueAdm = $data['net_course_price_1'] * $temp2;  
                        }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['admission_fee'] ; ?> (-{{$discountValueAdm}}) ({{$data['net_admission_fee']}})</td>
                    </tr>

                    <tr> <!--------Net Amount --->
                        <td scope="row" data-label="Account" colspan="2" style="font-weight: bold; ">Total</td>
                        <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                        $symbol='';
                        $data['amount'] ='';
                       }
                        ?>
                        <td data-label="Amount" style="text-align: center; font-weight: bold; "><?php echo $symbol ; ?><?php echo $data['amount']; ?></td>
                    </tr>
                </tbody>
            </table>
 
     
        <div class="d-inline-block " style="margin-top:5px">
            <span>For Bank Use Only</span><br>
            <span>Received Payment Rs.</span>
        </div><br/>
        <div class="d-inline-block  float-right" style="margin-right:5px">
            <span>Signature and Stamp</span>
            <span>Bank Officer</span>
        </div><br/><br/>
        <div class="d-inline-block">
            <span>Date:</span>
            <span>______________</span>
        </div>

    </div>




    <div class="column">
        <div class="space-between mb--10">
                 <?php 
                 
                    $year=$data['year'];
                    $id=$data['id'];
                    ?>
                <div class="d-inline-block " >Invoice # <?php echo $year; ?>/ <?php echo $id; ?></div>
                <div class="d-inline-block "style="margin-right: 5px;" >CEF Copy</div>
            </div>
            <div class="align-center">
         
                <h4 class="align-center title">Character Education Foundation</h4>
                <span class="align-center title">CEF Online Project</span>
            </div>
        </br>
            <div class="align-left">
                    <span class="align-left student-name">Student Name: <?php echo $data['s_name'] ; ?></span><br>
                <?php 
                $a=$data['s_gender'];
                if($a=='Male'){
                    $b='S/O: ';
                }
                else{
                    $b='D/O: ';
                }
                ?>
          
                <span class="align-left"><?php if($data['s_parent_name']) {echo $b;} ?></strong><?php if($data['s_parent_name']) {echo $data['s_parent_name'] ;} ?></strong></span>
               
            </div>
         
            <div class="space-between mb--10">
            <div class="align-left ">
                    <?php 
                     $new=date('d-m-Y',strtotime($data['payment_date']));
                    ?>
                    <span>Issue Date:</span>
                    <span><?php echo $new ; ?></span>
                </div>
        {{-- <div class="align-left ">
                    <?php 
                    $new=date('d-m-Y');
                    $a=date('d-m-Y',strtotime($data['payment_date']));
                    $due_date = strtotime($a); 
                    $b = strtotime("+30 days",$due_date) ;
                    $due_date=date('d-m-Y',$b);
                    ?>
                    <span>Due Date:</span>
                    <span><?php echo $due_date ; ?></span>
                </div> --}}

                <div class="align-left ">
                    <?php 
                    $month=$data['month'];
                    ?>
                    <span>Month:</span>
                    <span><?php echo $month ; ?></span>
                </div>

                
              
                <div class="align-left">
                    <span>Bank Islami Multan Road Branch</span>
                    <span>PK20BKIP0205400315500001</span>
                </div>

                {{-- <div class="align-left" style="margin-left: -3px;">
                    <span>Title:</span>
                    <span>CEF-Character Education Foundation</span>
                </div> --}}
            </div>

            <table style="margin-top: 5px;">
                <thead>
                    <tr>
                        <th scope="col left" colspan="2">Description</th>
                        <th scope="col right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>  <!--------Course Amount --->
                        <td data-label="Account" colspan="2"><?php echo $data['course_name'] ; ?></td>
                        <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                            $symbol='Rs';
                       }
                        if($data['discount_type'] == 0){
                            $discountValue = $data['discount'];
                        }
                        else if($data['discount_type'] == 1){
                            $temp1 = $data['discount']/100;
                            $discountValue = $data['net_course_price_1'] * $temp1;  
                        }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['course_price_1'] ; ?> (-{{$discountValue}}) ({{$data['net_course_price_1']}})</td>

                    </tr>

                    <tr>  <!--------Admission Fee Amount --->
                        <td scope="row" data-label="Account" colspan="2">Admission Fee</td>
                          <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                            $symbol='Rs';
                       }
                        if($data['discount_type_admission_fee'] == 0){
                            $discountValueAdm = $data['discount_admission_fee'];
                        }
                        else if($data['discount_type_admission_fee'] == 1){
                            $temp2 = $data['discount_admission_fee']/100;
                            $discountValueAdm = $data['net_course_price_1'] * $temp2;  
                        }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['admission_fee'] ; ?> (-{{$discountValueAdm}}) ({{$data['net_admission_fee']}})</td>
                    </tr>

                    <tr> <!--------Net Amount --->
                        <td scope="row" data-label="Account" colspan="2" style="font-weight: bold; ">Total</td>
                        <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                        $symbol='';
                        $data['amount'] ='';
                       }
                        ?>
                        <td data-label="Amount" style="text-align: center; font-weight: bold; "><?php echo $symbol ; ?><?php echo $data['amount']; ?></td>
                    </tr>
                </tbody>
            </table>
       
      
     
        <div class="d-inline-block " style="margin-top:5px">
            <span>For Bank Use Only</span><br>
            <span>Received Payment Rs.</span>
        </div><br/>
        <div class="d-inline-block  float-right" style="margin-right:5px">
            <span>Signature and Stamp</span>
            <span>Bank Officer</span>
        </div><br/><br/>
        <div class="d-inline-block">
            <span>Date:</span>
            <span>______________</span>
        </div>

    </div>






    
    <div class="column">
        <div class="space-between mb--10">
                 <?php 
                 
                    $year=$data['year'];
                    $id=$data['id'];
                    ?>
                <div class="d-inline-block " >Invoice # <?php echo $year; ?>/ <?php echo $id; ?></div>
                <div class="d-inline-block "style="margin-right: 5px;" >Student Copy</div>
            </div>
            <div class="align-center">
         
                <h4 class="align-center title">Character Education Foundation</h4>
                <span class="align-center title">CEF Online Project</span>
            </div>
        </br>
            <div class="align-left">
                    <span class="align-left student-name">Student Name: <?php echo $data['s_name'] ; ?></span><br>
                <?php 
                $a=$data['s_gender'];
                if($a=='Male'){
                    $b='S/O: ';
                }
                else{
                    $b='D/O: ';
                }
                ?>
          
                <span class="align-left"><?php if($data['s_parent_name']) {echo $b;} ?></strong><?php if($data['s_parent_name']) {echo $data['s_parent_name'] ;} ?></strong></span>
               
            </div>
         
            <div class="space-between mb--10">
            <div class="align-left ">
                    <?php 
                     $new=date('d-m-Y',strtotime($data['payment_date']));
                    ?>
                    <span>Issue Date:</span>
                    <span><?php echo $new ; ?></span>
                </div>
        {{-- <div class="align-left ">
                    <?php 
                    $new=date('d-m-Y');
                    $a=date('d-m-Y',strtotime($data['payment_date']));
                    $due_date = strtotime($a); 
                    $b = strtotime("+30 days",$due_date) ;
                    $due_date=date('d-m-Y',$b);
                    ?>
                    <span>Due Date:</span>
                    <span><?php echo $due_date ; ?></span>
                </div> --}}

                <div class="align-left ">
                    <?php 
                    $month=$data['month'];
                    ?>
                    <span>Month:</span>
                    <span><?php echo $month ; ?></span>
                </div>

                
              
                <div class="align-left">
                    <span>Bank Islami Multan Road Branch</span>
                    <span>PK20BKIP0205400315500001</span>
                </div>

                {{-- <div class="align-left" style="margin-left: -3px;">
                    <span>Title:</span>
                    <span>CEF-Character Education Foundation</span>
                </div> --}}
            </div>

             <table style="margin-top: 5px;">
                <thead>
                    <tr>
                        <th scope="col left" colspan="2">Description</th>
                        <th scope="col right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>  <!--------Course Amount --->
                        <td data-label="Account" colspan="2"><?php echo $data['course_name'] ; ?></td>
                        <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                            $symbol='Rs';
                       }
                        if($data['discount_type'] == 0){
                            $discountValue = $data['discount'];
                        }
                        else if($data['discount_type'] == 1){
                            $temp1 = $data['discount']/100;
                            $discountValue = $data['net_course_price_1'] * $temp1;  
                        }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['course_price_1'] ; ?> (-{{$discountValue}}) ({{$data['net_course_price_1']}})</td>

                    </tr>

                    <tr>  <!--------Admission Fee Amount --->
                        <td scope="row" data-label="Account" colspan="2">Admission Fee</td>
                          <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                            $symbol='Rs';
                       }
                        if($data['discount_type_admission_fee'] == 0){
                            $discountValueAdm = $data['discount_admission_fee'];
                        }
                        else if($data['discount_type_admission_fee'] == 1){
                            $temp2 = $data['discount_admission_fee']/100;
                            $discountValueAdm = $data['net_course_price_1'] * $temp2;  
                        }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['admission_fee'] ; ?> (-{{$discountValueAdm}}) ({{$data['net_admission_fee']}})</td>
                    </tr>

                    <tr> <!--------Net Amount --->
                        <td scope="row" data-label="Account" colspan="2" style="font-weight: bold; ">Total</td>
                        <?php 
                       $symbol=$data['currency'];
                       if(!isset($symbol)){
                        $symbol='';
                        $data['amount'] ='';
                       }
                        ?>
                        <td data-label="Amount" style="text-align: center; font-weight: bold; "><?php echo $symbol ; ?><?php echo $data['amount']; ?></td>
                    </tr>
                </tbody>
            </table>
       
        <!-- <div class="d-flex justify-content-between">
            <strong>Payable after due date</strong>
            <span>14,580.0</span>
        </div> -->

     
        <div class="d-inline-block " style="margin-top:5px">
            <span>For Bank Use Only</span><br>
            <span>Received Payment Rs.</span>
        </div><br/>
        <div class="d-inline-block  float-right" style="margin-right:5px">
            <span>Signature and Stamp</span>
            <span>Bank Officer</span>
        </div><br/><br/>
        <div class="d-inline-block">
            <span>Date:</span>
            <span>______________</span>
        </div>

    </div>






</div>
</body>
</html>