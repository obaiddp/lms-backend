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
        * {
            box-sizing: border-box;
        }
        /* general styling */

        /* Create four equal columns that floats next to each other */
        .column {
            margin-left: 3px;
            margin-right: 3px;
            display: inline-block;
            width: 24%;
            border-right: 1px dotted ;
            height: auto; /* Should be removed. Only for demonstration */
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }
        .bold{
            font-weight: bold;
        }
        .mb--15{
            margin-bottom: -15px;
        }
        .d-inline-block{
            display: inline-block;
            font-size: 16px;
            
        }
        .align-right{
            text-align: right;
        }
        .align-left{
            text-align: left;
        }
        .align-center{
            text-align: center;
            margin: 0 auto;
        }
        .space-between {
            text-align: justify;
        }
        .space-between::after {
            content: "";
            width: 100%;
            display: inline-block;
        }
        .float-right{
            float: right;
        }
        .float-left{
            float: left;
        }
        .circle-logo{
            width: 60px;
        }
        .logo{
            width: 220px;
        }
        .title{
            margin-top: 5px;
        }
        .student-name{
            margin-bottom: 10px;
        }
        .bar-code{
            width: 200px;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        .align-center{
            align-self: center;
        }
/*table*/
table {
    margin-top: 10px;
    
    border: 1px solid #ccc;
    border-collapse: collapse;
    margin: 0;
    padding: 0;
    width: 96%;
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
}
/*table end*/
hr{
    border-top: 1px solid #000;
}
.inlineb{
    display: inline-block;
    width: 48%;
    font-size: 16px;
}
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
            <div class="align-left ">
                    <?php 
                    $new=date('d-m-Y');
                    $a=date('d-m-Y',strtotime($data['payment_date']));
                    $due_date = strtotime($a); 
                    $b = strtotime("+30 days",$due_date) ;
                    $due_date=date('d-m-Y',$b);
                    ?>
                    <span>Due Date:</span>
                    <span><?php echo $due_date ; ?></span>
            </div>

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

                <div class="align-left" style="margin-left: -3px;">
                    <span>Title:</span>
                    <span>CEF-Character Education Foundation</span>
                </div>
            </div>

            <table style="margin-top: 5px;">
                <thead>
                    <tr>
                        <th scope="col left" colspan="2">Description</th>
                        <th scope="col right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Account" colspan="2"><?php echo $data['course_name'] ; ?></td>
                        <?php 
                       $symbol=$data['currency'];
                       if (isset($symbol)) {
                           if($symbol=="Rs"){
                        //        $amount=$data['course_price_1'];
                        //    $discount=$data['discount'];
                        //    $discounttype=$data['discount_type'];
                        //    if (isset($discount)) {
                        //    if ($discounttype=="0") {
                        //        $amount=$amount-$discount;
                        //    }
                        //    else{
                        //        $discount=$discount/100;
                        //        $discount=$discount*$amount;
                        //        $amount=$amount-$discount;
                        //    }
                        //    }
                           }
                           else{
                        //        $amount=$data['price_in_usd'];
                        //    $discount=$data['discount'];
                        //    $discounttype=$data['discount_type'];
                        //    if (isset($discount)) {
                        //    if ($discounttype=="0") {
                        //        $amount=$amount-$discount;
                        //    }
                        //    else{
                        //        $discount=$discount/100;
                        //        $discount=$discount*$amount;
                        //        $amount=$amount-$discount;
                        //    }
                        //    }
                           }
                       }
                       else{
                        $symbol='';
                        $data['amount'] ='';
                       }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['amount'] ; ?></td>
                    </tr>
                    <tr>
                        <td scope="row" data-label="Account" colspan="2">Discount</td>
                        <?php 
                        if (isset($symbol)) {
                            if($symbol=="Rs"){
                        $amount=$data['course_price_1'];
                        $discount=$data['discount'];
                        $discounttype=$data['discount_type'];
                        if (isset($discount)) {
                            if ($discounttype=="0") {
                                $discount=$discount;
                            }
                            else{
                                $discount=$discount/100;
                                $discount=$discount*$amount;
                            }
                        }
                        else{
                            $discount=0;  
                        }
                        }
                        else{
                            $amount=$data['course_price_1'];
                        $discount=$data['discount'];
                        $discounttype=$data['discount_type'];
                        if (isset($discount)) {
                            if ($discounttype=="0") {
                                $discount=$discount;
                            }
                            else{
                                $discount=$discount/100;
                                $discount=$discount*$amount;
                            }
                        }
                        else{
                            $discount=0;  
                        } 
                        }
                    }
                    else{
                        $discount= '';
                    }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $discount ; ?></td>
                    </tr>
                    <tr>
                        <td scope="row" data-label="Account" colspan="2" style="font-weight: bold; ">Total</td>
                        <?php 
                       $symbol=$data['currency'];
                    //    if (isset($symbol)) {
                    //        if($symbol=="Rs"){
                    //            $amount=$data['course_price_1'];
                    //        $discount=$data['discount'];
                    //        $discounttype=$data['discount_type'];
                    //        if (isset($discount)) {
                    //        if ($discounttype=="0") {
                    //            $amount=$amount-$discount;
                    //        }
                    //        else{
                    //            $discount=$discount/100;
                    //            $discount=$discount*$amount;
                    //            $amount=$amount-$discount;
                    //        }
                    //        }
                    //        }
                    //        else{
                    //            $amount=$data['price_in_usd'];
                    //        $discount=$data['discount'];
                    //        $discounttype=$data['discount_type'];
                    //        if (isset($discount)) {
                    //        if ($discounttype=="0") {
                    //            $amount=$amount-$discount;
                    //        }
                    //        else{
                    //            $discount=$discount/100;
                    //            $discount=$discount*$amount;
                    //            $amount=$amount-$discount;
                    //        }
                    //        }
                    //        }
                    //    }
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





    <div class="column">
        <div class="space-between mb--10">
                 <?php 
                 
                    $year=$data['year'];
                    $id=$data['id'];
                    ?>
                <div class="d-inline-block " >Invoice # <?php echo $year; ?>/ <?php echo $id; ?></div>
                <div class="d-inline-block "style="margin-right: 5px;" >Quran Edu Copy</div>
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
        <div class="align-left ">
                    <?php 
                    $new=date('d-m-Y');
                    $a=date('d-m-Y',strtotime($data['payment_date']));
                    $due_date = strtotime($a); 
                    $b = strtotime("+30 days",$due_date) ;
                    $due_date=date('d-m-Y',$b);
                    ?>
                    <span>Due Date:</span>
                    <span><?php echo $due_date ; ?></span>
                </div>

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

                <div class="align-left" style="margin-left: -3px;">
                    <span>Title:</span>
                    <span>CEF-Character Education Foundation</span>
                </div>
            </div>

            <table style="margin-top: 5px;">
                <thead>
                    <tr>
                        <th scope="col left" colspan="2">Description</th>
                        <th scope="col right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Account" colspan="2"><?php echo $data['course_name'] ; ?></td>
                        <?php 
                       $symbol=$data['currency'];
                       if (isset($symbol)) {
                           if($symbol=="Rs"){
                        //        $amount=$data['course_price_1'];
                        //    $discount=$data['discount'];
                        //    $discounttype=$data['discount_type'];
                        //    if (isset($discount)) {
                        //    if ($discounttype=="0") {
                        //        $amount=$amount-$discount;
                        //    }
                        //    else{
                        //        $discount=$discount/100;
                        //        $discount=$discount*$amount;
                        //        $amount=$amount-$discount;
                        //    }
                        //    }
                           }
                           else{
                        //        $amount=$data['price_in_usd'];
                        //    $discount=$data['discount'];
                        //    $discounttype=$data['discount_type'];
                        //    if (isset($discount)) {
                        //    if ($discounttype=="0") {
                        //        $amount=$amount-$discount;
                        //    }
                        //    else{
                        //        $discount=$discount/100;
                        //        $discount=$discount*$amount;
                        //        $amount=$amount-$discount;
                        //    }
                        //    }
                           }
                       }
                       else{
                        $symbol='';
                        $data['amount'] ='';
                       }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['amount'] ; ?></td>
                    </tr>
                    <tr>
                        <td scope="row" data-label="Account" colspan="2">Discount</td>
                        <?php 
                        if (isset($symbol)) {
                            if($symbol=="Rs"){
                        $amount=$data['course_price_1'];
                        $discount=$data['discount'];
                        $discounttype=$data['discount_type'];
                        if (isset($discount)) {
                            if ($discounttype=="0") {
                                $discount=$discount;
                            }
                            else{
                                $discount=$discount/100;
                                $discount=$discount*$amount;
                            }
                        }
                        else{
                            $discount=0;  
                        }
                        }
                        else{
                            $amount=$data['course_price_1'];
                        $discount=$data['discount'];
                        $discounttype=$data['discount_type'];
                        if (isset($discount)) {
                            if ($discounttype=="0") {
                                $discount=$discount;
                            }
                            else{
                                $discount=$discount/100;
                                $discount=$discount*$amount;
                            }
                        }
                        else{
                            $discount=0;  
                        } 
                        }
                    }
                    else{
                        $discount= '';
                    }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $discount ; ?></td>
                    </tr>
                    <tr>
                        <td scope="row" data-label="Account" colspan="2" style="font-weight: bold; ">Total</td>
                        <?php 
                       $symbol=$data['currency'];
                    //    if (isset($symbol)) {
                    //        if($symbol=="Rs"){
                    //            $amount=$data['course_price_1'];
                    //        $discount=$data['discount'];
                    //        $discounttype=$data['discount_type'];
                    //        if (isset($discount)) {
                    //        if ($discounttype=="0") {
                    //            $amount=$amount-$discount;
                    //        }
                    //        else{
                    //            $discount=$discount/100;
                    //            $discount=$discount*$amount;
                    //            $amount=$amount-$discount;
                    //        }
                    //        }
                    //        }
                    //        else{
                    //            $amount=$data['price_in_usd'];
                    //        $discount=$data['discount'];
                    //        $discounttype=$data['discount_type'];
                    //        if (isset($discount)) {
                    //        if ($discounttype=="0") {
                    //            $amount=$amount-$discount;
                    //        }
                    //        else{
                    //            $discount=$discount/100;
                    //            $discount=$discount*$amount;
                    //            $amount=$amount-$discount;
                    //        }
                    //        }
                    //        }
                    //    }
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
                <span class="align-center title">Quran Education Online Project</span>
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
        <div class="align-left ">
                    <?php 
                    $new=date('d-m-Y');
                    $a=date('d-m-Y',strtotime($data['payment_date']));
                    $due_date = strtotime($a); 
                    $b = strtotime("+30 days",$due_date) ;
                    $due_date=date('d-m-Y',$b);
                    ?>
                    <span>Due Date:</span>
                    <span><?php echo $due_date ; ?></span>
                </div>

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

                <div class="align-left" style="margin-left: -3px;">
                    <span>Title:</span>
                    <span>CEF-Character Education Foundation</span>
                </div>
            </div>

            <table style="margin-top: 5px;">
                <thead>
                    <tr>
                        <th scope="col left" colspan="2">Description</th>
                        <th scope="col right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Account" colspan="2"><?php echo $data['course_name'] ; ?></td>
                        <?php 
                       $symbol=$data['currency'];
                       if (isset($symbol)) {
                           if($symbol=="Rs"){
                        //        $amount=$data['course_price_1'];
                        //    $discount=$data['discount'];
                        //    $discounttype=$data['discount_type'];
                        //    if (isset($discount)) {
                        //    if ($discounttype=="0") {
                        //        $amount=$amount-$discount;
                        //    }
                        //    else{
                        //        $discount=$discount/100;
                        //        $discount=$discount*$amount;
                        //        $amount=$amount-$discount;
                        //    }
                        //    }
                           }
                           else{
                        //        $amount=$data['price_in_usd'];
                        //    $discount=$data['discount'];
                        //    $discounttype=$data['discount_type'];
                        //    if (isset($discount)) {
                        //    if ($discounttype=="0") {
                        //        $amount=$amount-$discount;
                        //    }
                        //    else{
                        //        $discount=$discount/100;
                        //        $discount=$discount*$amount;
                        //        $amount=$amount-$discount;
                        //    }
                        //    }
                           }
                       }
                       else{
                        $symbol='';
                        $data['amount'] ='';
                       }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['amount'] ; ?></td>
                    </tr>
                    <tr>
                        <td scope="row" data-label="Account" colspan="2">Discount</td>
                        <?php 
                        if (isset($symbol)) {
                            if($symbol=="Rs"){
                        $amount=$data['course_price_1'];
                        $discount=$data['discount'];
                        $discounttype=$data['discount_type'];
                        if (isset($discount)) {
                            if ($discounttype=="0") {
                                $discount=$discount;
                            }
                            else{
                                $discount=$discount/100;
                                $discount=$discount*$amount;
                            }
                        }
                        else{
                            $discount=0;  
                        }
                        }
                        else{
                            $amount=$data['course_price_1'];
                        $discount=$data['discount'];
                        $discounttype=$data['discount_type'];
                        if (isset($discount)) {
                            if ($discounttype=="0") {
                                $discount=$discount;
                            }
                            else{
                                $discount=$discount/100;
                                $discount=$discount*$amount;
                            }
                        }
                        else{
                            $discount=0;  
                        } 
                        }
                    }
                    else{
                        $discount= '';
                    }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $discount ; ?></td>
                    </tr>
                    <tr>
                        <td scope="row" data-label="Account" colspan="2" style="font-weight: bold; ">Total</td>
                        <?php 
                       $symbol=$data['currency'];
                    //    if (isset($symbol)) {
                    //        if($symbol=="Rs"){
                    //            $amount=$data['course_price_1'];
                    //        $discount=$data['discount'];
                    //        $discounttype=$data['discount_type'];
                    //        if (isset($discount)) {
                    //        if ($discounttype=="0") {
                    //            $amount=$amount-$discount;
                    //        }
                    //        else{
                    //            $discount=$discount/100;
                    //            $discount=$discount*$amount;
                    //            $amount=$amount-$discount;
                    //        }
                    //        }
                    //        }
                    //        else{
                    //            $amount=$data['price_in_usd'];
                    //        $discount=$data['discount'];
                    //        $discounttype=$data['discount_type'];
                    //        if (isset($discount)) {
                    //        if ($discounttype=="0") {
                    //            $amount=$amount-$discount;
                    //        }
                    //        else{
                    //            $discount=$discount/100;
                    //            $discount=$discount*$amount;
                    //            $amount=$amount-$discount;
                    //        }
                    //        }
                    //        }
                    //    }
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
        <div class="align-left ">
                    <?php 
                    $new=date('d-m-Y');
                    $a=date('d-m-Y',strtotime($data['payment_date']));
                    $due_date = strtotime($a); 
                    $b = strtotime("+30 days",$due_date) ;
                    $due_date=date('d-m-Y',$b);
                    ?>
                    <span>Due Date:</span>
                    <span><?php echo $due_date ; ?></span>
                </div>

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

                <div class="align-left" style="margin-left: -3px;">
                    <span>Title:</span>
                    <span>CEF-Character Education Foundation</span>
                </div>
            </div>

            <table style="margin-top: 5px;">
                <thead>
                    <tr>
                        <th scope="col left" colspan="2">Description</th>
                        <th scope="col right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td data-label="Account" colspan="2"><?php echo $data['course_name'] ; ?></td>
                        <?php 
                       $symbol=$data['currency'];
                       if (isset($symbol)) {
                           if($symbol=="Rs"){
                        //        $amount=$data['course_price_1'];
                        //    $discount=$data['discount'];
                        //    $discounttype=$data['discount_type'];
                        //    if (isset($discount)) {
                        //    if ($discounttype=="0") {
                        //        $amount=$amount-$discount;
                        //    }
                        //    else{
                        //        $discount=$discount/100;
                        //        $discount=$discount*$amount;
                        //        $amount=$amount-$discount;
                        //    }
                        //    }
                           }
                           else{
                        //        $amount=$data['price_in_usd'];
                        //    $discount=$data['discount'];
                        //    $discounttype=$data['discount_type'];
                        //    if (isset($discount)) {
                        //    if ($discounttype=="0") {
                        //        $amount=$amount-$discount;
                        //    }
                        //    else{
                        //        $discount=$discount/100;
                        //        $discount=$discount*$amount;
                        //        $amount=$amount-$discount;
                        //    }
                        //    }
                           }
                       }
                       else{
                        $symbol='';
                        $data['amount'] ='';
                       }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $symbol ; ?><?php echo $data['amount'] ; ?></td>
                    </tr>
                    <tr>
                        <td scope="row" data-label="Account" colspan="2">Discount</td>
                        <?php 
                        if (isset($symbol)) {
                            if($symbol=="Rs"){
                        $amount=$data['course_price_1'];
                        $discount=$data['discount'];
                        $discounttype=$data['discount_type'];
                        if (isset($discount)) {
                            if ($discounttype=="0") {
                                $discount=$discount;
                            }
                            else{
                                $discount=$discount/100;
                                $discount=$discount*$amount;
                            }
                        }
                        else{
                            $discount=0;  
                        }
                        }
                        else{
                            $amount=$data['course_price_1'];
                        $discount=$data['discount'];
                        $discounttype=$data['discount_type'];
                        if (isset($discount)) {
                            if ($discounttype=="0") {
                                $discount=$discount;
                            }
                            else{
                                $discount=$discount/100;
                                $discount=$discount*$amount;
                            }
                        }
                        else{
                            $discount=0;  
                        } 
                        }
                    }
                    else{
                        $discount= '';
                    }
                        ?>
                        <td data-label="Amount" style="text-align: center;"><?php echo $discount ; ?></td>
                    </tr>
                    <tr>
                        <td scope="row" data-label="Account" colspan="2" style="font-weight: bold; ">Total</td>
                        <?php 
                       $symbol=$data['currency'];
                    //    if (isset($symbol)) {
                    //        if($symbol=="Rs"){
                    //            $amount=$data['course_price_1'];
                    //        $discount=$data['discount'];
                    //        $discounttype=$data['discount_type'];
                    //        if (isset($discount)) {
                    //        if ($discounttype=="0") {
                    //            $amount=$amount-$discount;
                    //        }
                    //        else{
                    //            $discount=$discount/100;
                    //            $discount=$discount*$amount;
                    //            $amount=$amount-$discount;
                    //        }
                    //        }
                    //        }
                    //        else{
                    //            $amount=$data['price_in_usd'];
                    //        $discount=$data['discount'];
                    //        $discounttype=$data['discount_type'];
                    //        if (isset($discount)) {
                    //        if ($discounttype=="0") {
                    //            $amount=$amount-$discount;
                    //        }
                    //        else{
                    //            $discount=$discount/100;
                    //            $discount=$discount*$amount;
                    //            $amount=$amount-$discount;
                    //        }
                    //        }
                    //        }
                    //    }
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