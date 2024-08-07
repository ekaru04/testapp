<?php
include("../../assets/config/db.php");
session_start();

function productStockTest($products = array()){
    $messages = array();

    foreach($products as $product){
        //Langsung ke detail recipe
        $checkPro = mysql_query("
            SELECT 
            mrd.ingredientID, mrd.amount, ist.curStock, ist.minStock, mp.productName, mi.ingredient as ingredientName
            FROM mRecipeDetails mrd 
            JOIN ingredientStockKitchen ist ON ist.ingredientID = mrd.ingredientID
            JOIN mProduct mp ON mp.recipeID = mrd.recipeID
            JOIN mIngredient mi ON mi.ingredientID = mrd.ingredientID
            WHERE mrd.recipeID = '".$product['recipeID']."' AND mrd.status = 1");
        while($rowPro = mysql_fetch_array($checkPro)){
            $requestedAmount = $rowPro[amount] * $product[amount];
            if(($rowPro[curStock] - $requestedAmount) < $rowPro[minStock]){
                $messages[] = "[" . $rowPro[productName] . "] Bahan: \"" . $rowPro['ingredientName'] . "\" tidak cukup. Tidak dapat melanjutkan pemesanan.";
            }
        }
    }

    return $messages;
}

function processRecipeIngredient($product, $salesID){
    // admin/reqIn/approval_process.php:99
    $username = $_SESSION['username'];
    $recipeDetail = mysql_query("
        SELECT 
        mrd.ingredientID, mrd.amount, ist.curStock, 
        ist.minStock, mp.productName, mi.ingredient as ingredientName, 
        mp.outletID, mp.productID, ist.measurementID, mp.categoryID
        FROM mRecipeDetails mrd 
        JOIN ingredientStockKitchen ist ON ist.ingredientID = mrd.ingredientID
        JOIN mProduct mp ON mp.recipeID = mrd.recipeID
        JOIN mIngredient mi ON mi.ingredientID = mrd.ingredientID
        WHERE mrd.recipeID = '".$product['recipeID']."' AND mrd.status = 1");


        //Bikin request header & detail
        // admin/reqIn/reqin_process.php:47
        $per = date("Ym");
        $requestData = mysql_query("SELECT count(requestID)+1 AS countID FROM tabrequestheader");
        $rowRequestData = mysql_fetch_array($requestData);
        $countRequestData = $rowRequestData['countID'];
        $requestDate = date("Y-m-d");
        $requestID = "PCA/REQ/$per/".str_pad($countRequestData,4,"0",STR_PAD_LEFT); 
        $timestamp = date("Y-m-d H:i:s");
        //Tab Request Header
        $query = "INSERT INTO tabrequestheader (requestID,requestDate,categoryID,outletID,productID,amount,measurementID,username,status,remarks,dateCreated,lastChanged) VALUES('$requestID', '$requestDate', '$product[categoryID]', '$product[outletID]', '$product[productID]', '$product[amount]', '$product[measurementID]', '$username', '2', 'DIRECT_APPROVAL_FROM_SALES',  '$timestamp', '$timestamp')";
		$res = mysql_query($query);

        $journalID = date("YmdHis");
		$act = "INSERT_REQUESTING_FROM_SALES".$salesID;

		$queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,username,dateCreated,logCreated,status) VALUES('$journalID','$act','REQUEST_INGREDIENT','$username','$timestamp','$timestamp', 'SUCCESS')";
		$resJournal = mysql_query($queryJournal);

    $x = 0;
    while($_recipeDetail = mysql_fetch_array($recipeDetail)){
        //Masukkan ke tabrequestdetail
        //admin/approvReq/reqin_details_process.php:38
        $detailID = date('Ymdhis').str_pad($x, 3, "0", STR_PAD_LEFT);
        $requestDetail = mysql_query("INSERT INTO tabRequestDetail VALUES('".$detailID."', '".$requestID."', '".$_recipeDetail[ingredientID]."', '".($_recipeDetail[amount] * $product[amount])."', '".$_recipeDetail[measurementID]."', '2', '".$timestamp."', '".$timestamp."')");

        $journalID = date("YmdHis");
        $act = "INSERT_REQUESTING_FROM_SALES".$salesID."INGREDIENT($_recipeDetail[ingredientID])";

        $queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,username,dateCreated,logCreated,status) VALUES('$journalID','$act','REQUEST_DETAIL_INGREDIENT','$username','$timestamp','$timestamp', 'SUCCESS')";
        $resJournal = mysql_query($queryJournal);

        //Stok sebenarnya
        $lastChanged = date("Y-m-d H:i:s");
        $ingredientNewStock = $_recipeDetail[curStock] - ($_recipeDetail[amount] * $product[amount]);
        $res = mysql_query("UPDATE ingredientStockKitchen SET curStock = '$ingredientNewStock', lastChanged = '$lastChanged' WHERE ingredientID = '$_recipeDetail[ingredientID]' and status = 1");
    
        $journalID = date("YmdHis");
        $dateCreated = date("Y-m-d H:i:s");

        $actStock = "UPDATE_STOCK_INGREDIENT_".$_recipeDetail['ingredientID']."_FROM_SALES".$salesID;
    
        $queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,username,dateCreated,logCreated,status) VALUES('$journalID','$actStock','AMOUNT_STOCK','$username','$dateCreated','$lastChanged', 'SUCCESS')";
        $resJournal = mysql_query($queryJournal);
        
        //update saldo sisa (ingin berkata kasar)
        $resSaldo = mysql_query("SELECT * FROM tabItemSaldo s WHERE s.ingredientID = '$_recipeDetail[ingredientID]' AND s.outletID = '$_recipeDetail[outletID]' AND s.status = 1 ORDER BY s.dateCreated ASC");
        
        //Ini kurang tau gimana mekanismenya
        $amountLeft = $_recipeDetail[amount] * $product[amount]; // isinya jumlah produk yg ingin dibuat tadi
        $duitTotal = 0;
        $totalCost = 0;
        while($rowSaldo = mysql_fetch_array($resSaldo)){
            if($amountLeft <= 0){
    
            }else{
    
                $id = $rowSaldo[id];
                $amountSaldo = $rowSaldo[amount]; // bahan baku yang masuk dari form restock ingredient
                $amountUsed = $rowSaldo[amountUsed]; // bahan baku yg digunakan sebelumnya ada brp
                $itemPrice = $rowSaldo[itemPrice]; // harga bahan baku awal yg sebelumnya apabila belum habis saldonya
                $totalPrice = $rowSaldo[totalPrice]; // total harga bahan baku berdasarkan amount yg masuk dijumlahkan
                $saldoPrice = $rowSaldo[saldo]; // saldo yang tersisa
    
    
                $itemPriceAfterDiscount = $totalPrice / $amountSaldo; 
                // echo $itemPriceAfterDiscount; /* isinya 10000 */
                // exit;
                
                $saldo = $amountSaldo-$amountUsed;  
                // echo $saldo; /* Isinya sisa stok bahan baku */
                // echo $saldoPrice;
                // exit;
    
                if($amountLeft < $saldo){ 
                    // echo "habis <br/>";
                    $amountUsed = $amountUsed + $amountLeft; 
                    $saldoPrice = $saldoPrice - ($itemPriceAfterDiscount*$amountLeft); 
                    $duitTotal = $duitTotal + ($itemPriceAfterDiscount*$amountLeft);
                    $amountLeft = 0; 
                    // echo $saldoPrice."<br/>";
                    // exit;
                    
                }elseif($amountLeft == $saldo){ 
                    // echo "kebetulan borong";
                    $amountLeft = $saldo; 
                    $amountUsed = $amountUsed + $amountSaldo; 
                    $saldoPrice = $saldoPrice - ($itemPriceAfterDiscount*$amountSaldo);
                    $duitTotal = $duitTotal + ($itemPriceAfterDiscount*$amountSaldo);
                }else{ 
                    // echo "sisa";
                    $amountLeft = $amountLeft - $saldo; 
                    $amountUsed = $amountUsed + $saldo; 
                    $saldoPrice = $saldoPrice - ($itemPriceAfterDiscount*$saldo);
                    $duitTotal = $duitTotal + ($itemPriceAfterDiscount*$saldo);
                }
                // echo $saldoPrice;
                // exit;
                $res = mysql_query("UPDATE tabItemSaldo SET amountUsed = '$amountUsed', saldo='$saldoPrice' WHERE id ='$id' AND ingredientID = '$_recipeDetail[ingredientID]' AND outletID = '$_recipeDetail[outletID]' and status = 1");
    
                $journalID = date("YmdHis");
                $actStock = "UPDATE_SALDO_FROM_SALES".$salesID;
                $queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,username,dateCreated,logCreated,status) VALUES('$journalID','$actStock','AMOUNT_USED_AND_SALDO','$username','$dateCreated','$lastChanged', 'SUCCESS')";
                $resJournal = mysql_query($queryJournal);
    
            }
        }
        
        $queryHPP = mysql_query("INSERT INTO tabhppmovement(hppID, productID, ingredientID, amountUsed, measurementID, cost, username, statusHPP, dateCreated, lastChanged)
                                    VALUES('$salesID', '$_recipeDetail[productID]', '$_recipeDetail[ingredientID]', '".($_recipeDetail[amount] * $product[amount])."', 
                                            '$_recipeDetail[measurementID]', '$duitTotal', '$username', 1, '$dateCreated', '$lastChanged')");
        // echo $duitTotal;
        //insert ke history
        $journalID = date("YmdHis");
        $dateCreated = date("Y-m-d");
        $lastChanged = date("Y-m-d H:i:s");
        
        $queryT = "INSERT INTO tabitemhistory(id,transType,loc,ingredientID,amount,itemAmount,measurementID,cost,username,status,dateCreated,lastChanged) 
            VALUES('$journalID', 'OUT', '2', '$_recipeDetail[ingredientID]', '".($_recipeDetail[amount] * $product[amount])."', '$ingredientNewStock','$_recipeDetail[measurementID]',
            '$duitTotal','$username', 1, '$dateCreated', '$lastChanged')";						
        $resT = mysql_query($queryT);
    
    
        $journalID = date("YmdHis");
        $actStock = "INSERT_HISTORY_FROM_SALES".$salesID;
        $queryJournal = "INSERT INTO systemJournal(journalID,activity,menu,username,dateCreated,logCreated,status) VALUES('$journalID','$actStock','HISTORY','$username','$dateCreated','$lastChanged', 'SUCCESS')";
        // $resJournal = mysql_query($queryJournal);

        $x++;
    }

    //Reversal stok produk, kembalikan lagi jadi 100, karena produk by request
    $queryUpdate = mysql_query("UPDATE mproduct SET curStock = 100 WHERE productID = '".$product[productID]."' AND outletID = '".$product[outletID]."' AND measurementID = '".$product[measurementID]."'");   
}