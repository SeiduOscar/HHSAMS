<?php
include'../Includes/dbcon.php';
                                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAdminId'])) {
                                  $deleteId = $_POST['deleteAdminId'];
                                  if (!empty($deleteId)){
                                  $deleteQuery = "DELETE FROM tbladmin WHERE Id = $deleteId";
                                  if (mysqli_query($conn, $deleteQuery)) {
                                    header("Location: index.php");
                                    exit();
                                  } else {
                                    echo "<div class='alert alert-danger'>Failed to delete admin user.</div>";
                                  }
                                  } else {
                                    echo "<script>alert('no user Id found')</script>";
                                  }
                                }
                                ?>