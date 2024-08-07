<?php
session_start();
if (!isset($_SESSION["username"])) 
{
    $URL="/picapos/admin"; 
    echo "<script type='text/javascript'>location.replace('$URL');</script>";
}
include("../../assets/config/db.php");      
include('../../assets/template/navbar_app.php');

    $per = date("Ym");
    $getNewID = mysql_query("SELECT COUNT(returID)+1 as count FROM tabreturproduct");
	$rowNewID = mysql_fetch_array($getNewID);
	$newID = $rowNewID['count'];
	$returID = "PCA/RTN/$per/".str_pad($newID, 4, "0", STR_PAD_LEFT);
?>
<div>
       <div class='clear height-20 mt-3'></div>
        <div class="container-fluid">
         <div class='entry-box-basic'>
           <h1 class="text-center">
               RETUR PRODUK
           </h1>
          <div class="container-fluid">
           <div class="row">
            <div class="col-8">
                <form id='formAdd' method='POST' action='retur_process.php' enctype="multipart/form-data">
                    <div class="row mt-3">
                        <div class="col-3"></div>
                        <div class="col-3">
                            <input type="text" class="form-control type-input" name='returID' style='width:300px;' readonly value='<?php echo $returID; ?>'>
                        </div>
                    </div>
                    <div class='row  mt-3 '>
                        <div class='col-3 label'>
                           <input type='text' class='form-control-plaintext' disabled value='OUTLET' />
                        </div>
                        <div class='col-3'>
                            <select class='select-cust form-control' name='outletID' id='outletID' style='width:300px;' required>
                                <!-- <option value=''>-CHOOSE ONE-</option> -->
                                <?php
                                    $queryOutlet="select * from moutlet where status != 0";
                                    $resOutlet=mysql_query($queryOutlet);
                                    while($rowOutlet=mysql_fetch_array($resOutlet)){
                                        if($rowOutlet[outletID] == $_SESSION[outletID]){
                                            echo "<option selected value='$rowOutlet[outletID]'>$rowOutlet[outletName]</option>";
                                        }else{
                                            echo "<option value='$rowOutlet[outletID]'>$rowOutlet[outletName]</option>";
                                        }

                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class='row mt-3'>
                        <div class='col-3 label'>
                           <input type='text' class='form-control-plaintext' disabled value='CATEGORY' />
                        </div>
                        <select class='select-cust ml-3 form-control' name='categoryID' id='categoryID' style='width:300px;' required>
                                <option value=''>-PILIH SATU-</option>
                                <?php
                                    $queryCategory="select * from mcategory where status != 0";
                                    $resCategory=mysql_query($queryCategory);
                                    while($rowCategory=mysql_fetch_array($resCategory)){
                                        if($rowCategory[categoryID] == $row[categoryID]){
                                            echo "<option selected value='$rowCategory[categoryID]'>$rowCategory[categoryName]</option>";
                                        }else{
                                            echo "<option value='$rowCategory[categoryID]'>$rowCategory[categoryName]</option>";
                                        }

                                    }
                                ?>
                            </select>
                    </div>   

                    <div class='row mt-3'>
                        <div class='col-3 label'>
                           <input type='text' class='form-control-plaintext' disabled value='PRODUCT' />
                        </div>
                        <select class='select-cust ml-3 form-control' name='productID' id='productID' style='width:300px;' selected>
                            <option value=''>-PILIH SATU-</option>
                            <?php
                              $queryProduct="select * from mproduct where status != 0";
                              $resProduct=mysql_query($queryProduct);
                               while($rowProduct=mysql_fetch_array($resProduct)){
                                if($rowProduct[productID] == $row[productID]){
                                 echo "<option selected value='$rowProduct[productID]'>$rowProduct[productName]</option>";
                                }else{
                                 echo "<option value='$rowProduct[productID]'>$rowProduct[productName]</option>";
                                }
                              }
                            ?>
                        </select>
                    </div>                  

                    <div class='row mt-3'>
                        <div class='col-3 label'>
                           <input type='text' class='form-control-plaintext' disabled value='STOK TOKO' />
                        </div>
                        <div class='col-2'>
                            <input type='text' class='form-control type-input' name='curStock' id='curStock' placeholder='' style='width:300px' readonly />
                        </div>
                    </div>
                    
                    <div class='row mt-3'>
                        <div class='col-3 label'>
                           <input type='text' class='form-control-plaintext' disabled value='STOK DIRETUR' />
                        </div>
                        <div class='col-2'>
                            <input type='text' class='form-control type-input' name='stockRetur' id='stockRetur' placeholder='' style='width:300px' required />
                        </div>
                    </div>

                    <div class='row  mt-3'>
                        <div class='col-3 label'>
                           <input type='text' class='form-control-plaintext' disabled value='REMARKS' />
                        </div>
                        <div class='col-3'>
                            <textarea class='type-input form-control' name='remarks' placeholder='Insert notes here' rows='4' cols='50'></textarea>
                        </div>
                    </div>
                    
                    <div class='row  mt-3'>
                        <div class='col-3'>
                            <input id='save' type='submit' value='SUBMIT' name='submit' class='btn btn-success' />
                            <button type='button' id='cancel' class='btn btn-danger' >CANCEL</button>  
                        </div>
                    </div>
                    
                </form>
                            
                    </div>
                  </div>
                </div>
            </div>
        </div>
        </div>
    </body>
</html>
<script type="text/javascript">
    
    $(document).on('change','#categoryID',function(){
        var val = $('#categoryID').val();
        var val2 = $('#outletID').val();
        $.ajax({
            url: 'product_list.php',
            data: {categoryID:val,outletID:val2},
            type: 'GET',
            dataType: 'html',
            success: function(result){
                $('#productID').html(); 
                $('#productID').html(result); 
            }
        });  
    });

    $('#productID').change(function(){
        var product = $(this).val();
        // console.log(product);
        var getProduct = "product_detail.php?productID="+product;
        $.get(getProduct, function( data ) {
            // console.log(data.curStock);
            var curstock = isNaN(data.curStock) ? 0 : parseFloat(data.curStock);
            console.log(curstock);
            $('#curStock').val(curstock);
        }, "json" );
    });

    $('#stockRetur').keyup(function(){
        var retur = $(this).val();
        var stock = $('#curStock').val();
        var submit = $('#save');
        if(retur > stock){
            alert("Stok yang diretur melebihi stok");
            $('#stockRetur').val(0);
            submit.attr("disabled", "disabled");
        }else{
            submit.attr("enabled", "enabled");
        }
    });
    
    $("#cancel").click(function(){
        alert("Data tidak tersimpan");
        location.replace('/picaPOS/admin/products/');
    });
    
    $("#del").click(function(){
        var r = confirm("Apakah yakin ingin menghapus data ini?");
        if(r){
            <?php echo "location.replace('product_delete.php?productID=$productID');"?>
        }
    });

    
</script>

<script>
  $(window).load(function() { $(".se-pre-con").fadeOut("slow"); });  
</script>
<script src="../../assets/dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../../assets/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../../assets/dist/js/demo.js"></script>