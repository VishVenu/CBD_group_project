<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$order_date = $product_name = $quantity_ordered = $price_each = $shipped_date='';
$order_date_err = $product_name_err = $quantity_ordered_err = $price_each_err = '';
// Processing form data when form is submitted
$order_number = $_GET['order_number'];

$order_line_number = $_GET['order_line_number'];

if(!empty($order_number) && !empty($order_line_number)){
    // Validate order date
    if(!isset($_POST["quantity_ordered"]))
    {
        $select_sql = "SELECT orderDate, productName, quantityOrdered, priceEach, shippedDate FROM orders INNER JOIN orderdetails USING (orderNumber) INNER JOIN products USING (productCode) WHERE orders.orderNumber = ? AND orderdetails.orderLineNumber = ?;";
        
        if($select_stmt = mysqli_prepare($link, $select_sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($select_stmt, "ii", $param_order_number, $param_order_line_number);
            
            // Set parameters
            $param_order_number = $order_number;
            $param_order_line_number = $order_line_number;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($select_stmt)){
                $select_result = mysqli_stmt_get_result($select_stmt);
                // Fetch result row as an associative array. Since the result set
                //contains only one row, we don't need to use while loop
                $select_row = mysqli_fetch_array($select_result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $order_date = $select_row["orderDate"];
                $product_name = $select_row["productName"];
                $quantity_ordered = $select_row["quantityOrdered"];
                $price_each = $select_row["priceEach"];
                $shipped_date=$select_row["shippedDate"];
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    else{
        $shipped_date = trim($_POST["shipped_date"]);
        
        // Validate order date
        $input_order_date = trim($_POST["order_date"]);
        if(empty($input_order_date)){
            $order_date_err="Please enter the order date.";
        }
        elseif(!filter_var($input_order_date, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/")))){
            $order_date_err = "Please enter a valid date in yyyy-mm-dd format.";
        }
        elseif($input_order_date > $shipped_date){
            $order_date_err="Order date exceeds shipped date : " . $shipped_date;
        }
        else{
            $order_date_err='';
            $order_date=$input_order_date;
        }
        
        // Validate product name
        $input_product_name = trim($_POST["product_name"]);
        if(empty($input_product_name)){
            $product_name_err="Please enter the order date.";
        }
        else{
            $product_name_err='';
            $product_name=$input_product_name;
        }
        
        // Validate quantity ordered
        $input_quantity_ordered = trim($_POST["quantity_ordered"]);
        if(empty($input_quantity_ordered)){
            $quantity_ordered_err = "Please enter the quantity ordered.";
        } else{
            $quantity_ordered_err ='';
            $quantity_ordered = $input_quantity_ordered;
        }
        
        
        // Validate price each
        $input_price_each = trim($_POST["price_each"]);
        if(empty($input_price_each))
        {
            $price_each_err = "Please enter price each value.";
        }
        else{
            $price_each_err ='';
            $price_each = $input_price_each;
        }
        
        
        // Check input errors before inserting in database
        if(empty($quantity_ordered_err) && empty($price_each_err) && empty($order_date_err) && empty($product_name_err)){
            // Prepare an update statement
            $sql = "UPDATE orderdetails INNER JOIN orders on orders.orderNumber=orderdetails.orderNumber INNER JOIN products on products.productCode=orderdetails.productCode SET orderDate = ? , productName = ?,quantityOrdered = ?, priceEach = ?  WHERE orderdetails.orderNumber = ? AND orderdetails.orderLineNumber = ?;";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sssssi", $param_order_date, $param_product_name, $param_quantity_ordered, $param_price_each, $param_order_number, $param_order_line_number);
                
                // Set parameters
                $param_order_date=$order_date;
                $param_product_name=$product_name;
                $param_quantity_ordered = $quantity_ordered;
                $param_price_each = $price_each;
                $param_order_number = $order_number;
                $param_order_line_number = $order_line_number;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Records updated successfully. Redirect to landing page
                    header("location: index.php");
                    exit();
                } else{
                    echo "Something went wrong. Please try again later.";
                }
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
        
        // Close connection
        mysqli_close($link);
    }
}else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Record</title>
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
<h2>Update Record</h2>
</div>
<p>Please edit the input values and submit to update the record.</p>
<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
<div class="form-group">
<label>Order Number</label>
<input type="text" name="order_number" class="form-control" value="<?php echo $order_number; ?>" readonly>
</div>
<div class="form-group">
<label>Order Line Number</label>
<input type="text" name="order_line_number" class="form-control" value="<?php echo $order_line_number; ?>" readonly>
</div>
<div class="form-group <?php echo (!empty($order_date_err)) ? 'has-error' : ''; ?>">
<label>Order Date</label>
<input type="text" name="order_date" class="form-control" value="<?php echo $order_date; ?>">
<span class="help-block"><?php echo $order_date_err;?></span>
</div>
<div class="form-group <?php echo (!empty($product_name_err)) ? 'has-error' : ''; ?>">
<label>Product Name</label>
<input type="text" name="product_name" class="form-control" value="<?php echo $product_name; ?>">
<span class="help-block"><?php echo $product_name_err;?></span>
</div>
<div class="form-group <?php echo (!empty($quantity_ordered_err)) ? 'has-error' : ''; ?>">
<label>Quantity Ordered</label>
<input type="text" name="quantity_ordered" class="form-control" value="<?php echo $quantity_ordered; ?>">
<span class="help-block"><?php echo $quantity_ordered_err;?></span>
</div>
<div class="form-group <?php echo (!empty($price_each_err)) ? 'has-error' : ''; ?>">
<label>Price Each</label>
<input type="text" name="price_each" class="form-control" value="<?php echo $price_each; ?>">
<span class="help-block"><?php echo $price_each_err;?></span>
</div>
<input type="hidden" name="shipped_date" value="<?php echo $shipped_date; ?>"/>
<input type="submit" class="btn btn-primary" value="Submit">
<a href="index.php" class="btn btn-default">Cancel</a>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
