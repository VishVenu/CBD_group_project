<?php
// Process delete operation after confirmation
if(isset($_POST['order_number']) && !empty($_POST['order_number'])){
    // $order_details = (array) $_POST['order'];
    // $order_number = $order_details[0];
    // $order_line_number = $order_details[1];

    $order_number = $_POST['order_number'];
    $order_line_number = $_POST['order_line_number'];

    // Include config file
    require_once "config.php";
    
    // Prepare a delete statement
    $sql = "DELETE FROM orderdetails WHERE orderNumber = ? AND orderLineNumber = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ii", $param_order_number, $param_order_line_number);
        
        // Set parameters
        $param_order_number = $order_number;
		$param_order_line_number = $order_line_number;
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
            header("location: index.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of order parameter
    if(empty($_GET['order_number'])){
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
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
                        <h1>Delete Record</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger fade in">
                            <input type="" name="order_number" value="<?php echo $_GET["order_number"]; ?>"/>
							<input type="" name="order_line_number" value="<?php echo $_GET["order_line_number"]; ?>"/>
                            <p>Are you sure you want to delete this record?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="index.php" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>