<?php
// Check existence of id parameter before processing further
if(isset($_GET["order_line_number"]) && !empty(trim($_GET["order_line_number"]))
&& isset($_GET["order_number"]) && !empty(trim($_GET["order_number"]))
){
    // Include config file
    require_once "config.php";
    
    // Prepare a select statement
    $sql = "SELECT orderdetails.orderNumber, orderdetails.orderLineNumber, orderdetails.productCode, 
    orderdetails.quantityOrdered, orderdetails.priceEach, 
    orders.orderDate, products.productName FROM orderdetails 
    INNER JOIN orders USING(orderNumber) INNER JOIN products USING (productCode) 
    WHERE orderdetails.orderNumber = ? AND orderdetails.orderLineNumber = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ii", $param_orderNumber, $param_orderLineNumber);
        
        // Set parameters
        $param_orderNumber = trim($_GET["order_number"]);
        $param_orderLineNumber = trim($_GET["order_line_number"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $priceEach = $row["priceEach"];
                $quantityOrdered = $row["quantityOrdered"];
                $orderDate = $row["orderDate"];
                $productCode = $row["productCode"];
                $order_number = $row["orderNumber"];
                $order_line_number = $row["orderLineNumber"];

            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: error.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h1>View Record</h1>
                    </div>
                    <div class="form-group">
                        <label>Order Number</label>
                        <p class="form-control-static"><?php echo $_GET["order_number"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Order Date</label>
                        <p class="form-control-static"><?php echo $row["orderDate"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Order Line Number</label>
                        <p class="form-control-static"><?php echo $_GET["order_line_number"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Product Name</label>
                        <p class="form-control-static"><?php echo $row["productName"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Quantity Ordered</label>
                        <p class="form-control-static"><?php echo $row["quantityOrdered"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>Price Each</label>
                        <p class="form-control-static"><?php echo $row["priceEach"]; ?></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Back</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>