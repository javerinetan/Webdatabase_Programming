<?php 
require_once('../connection.php');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if(!isset($_SESSION['User']))
    {header("location:login.php");
} 

$userid = $_SESSION['User'];
$query="select * from account where id=".$_SESSION['User']."";
$instance = new DatabaseConnection();
$row=$instance->retrieveData($query);

// Count quizzes created by the user
$queryCreated = "SELECT COUNT(*) as quizzes_created FROM quiz WHERE creator_id = $userid";
$resultCreated = mysqli_query($con, $queryCreated);
if ($resultCreated == TRUE) {
    $rowCreated = mysqli_fetch_assoc($resultCreated);
    $quizzesCreated = $rowCreated['quizzes_created'];
    // $quizzesCreated = mysqli_num_rows($resultCreated);

} else {
    // created = 0 
    // echo "Error: " . $queryCreated . "<br>" . $con->error;

}

// Count quizzes taken by the user
$queryTaken = "SELECT COUNT(*) as quizzes_taken FROM quiz WHERE user_id = $userid";
$resultTaken = FALSE; // temporary until the table for it has been set
// $resultTaken = mysqli_query($con, $queryTaken);
// $rowTaken = mysqli_fetch_assoc($resultTaken);
// $quizzesTaken = $rowTaken['quizzes_taken'];
// if ($resultTaken == TRUE) {
//     $rowTaken = mysqli_fetch_assoc($resultTaken);
//     $quizzesTaken = $rowTaken['quizzes_taken'];
// } else {
//     // taken = 0
//     // echo "Error: " . $queryCreated . "<br>" . $con->error;
// }
// $quizzesTaken = $instance->retrieveData($queryTaken)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>

    <style>
        .dashboard{
            text-align:center;
        }

        .chart1{
            
        }
    </style>
    


</head>
<?php require '../navbar.php'?>


<body>

<!-- Dashboard for no. of quiz created & Quiz taken over a session -->

<div class="dashboard">
    <h2>Welcome, <?php echo $row['name'] ; ?>!</h2>
    <p>Number of quizzes created: <?php 
    if($resultCreated==True){
        echo $quizzesCreated;
    }else{
        echo "0";
    }; 
    ?></p>
    <p>Number of quizzes taken: <?php 
    if($resultTaken==TRUE){
        echo $quizzesTaken;
    }else{
        echo "0";
    }; 
    ?></p>
</div>

<?php 
    $queryforgraph1 = "SELECT YEAR(created_on) AS year, MONTH(created_on) AS month, COUNT(*) AS quizzes_created
                       FROM quiz
                       WHERE creator_id = $userid
                       GROUP BY year, month";

    // Fetch data for graph1
    $resultGraph1 = mysqli_query($con, $queryforgraph1);
    $dataGraph1 = [];

    while ($rowGraph1 = mysqli_fetch_assoc($resultGraph1)) {
        $dataGraph1[] = [
            'year' => $rowGraph1['year'],
            'month' => $rowGraph1['month'],
            'quizzes_created' => $rowGraph1['quizzes_created'],
        ];
    }

    $queryforgraph2 = "SELECT YEAR(created_on) AS year, MONTH(created_on) AS month, COUNT(*) AS quizzes_created
                       FROM quiz
                       WHERE creator_id = $userid
                       GROUP BY year, month";

    // Fetch data for graph1
    $resultGraph2 = mysqli_query($con, $queryforgraph2);
    $dataGraph2 = [];

    while ($rowGraph2 = mysqli_fetch_assoc($resultGraph2)) {
        $dataGraph2[] = [
            'year2' => $rowGraph2['year2'],
            'month2' => $rowGraph2['month2']
        ];
    }
?>

<!-- When i click on the quiz start, append +1 into a list so i have a counter -->

<div>
  <canvas id="Chart1"></canvas>
  <canvas id="Chart2"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('Chart1');

  new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php
            // an empty array to store formatted date strings
            $formattedDates = [];

            // Iterate through each entry in the $dataGraph1 array
            foreach ($dataGraph1 as $date) {
                // Extract month and year from the current entry
                $month = $date['month'];
                $year = $date['year'];

                // Convert the corresponding value for the dates
                // mktime = (hour, minute, second, month, day, year, is_dst)
                $timestamp = mktime(0, 0, 0, $month, 1, $year);

                // Format the timestamp as "M Y" and add it to the $formattedDates array
                $formattedDates[] = date("M Y", $timestamp);
            }

            // change the $formattedDates into a json-encoding format then print/echo the array
            echo json_encode($formattedDates);
        ?>,
        datasets: [{
            label: '# of Quiz Created per Month',
            data: <?php echo json_encode(array_column($dataGraph1, 'quizzes_created')); ?>,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                stepSize: 1,
                precision: 0
            }
        }
    }
});
</script>

<script>
  const ctx1 = document.getElementById('Chart2');

  new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?php
            $formattedDates = [];

            foreach ($dataGraph2 as $date) {

                $month = $date['month2'];
                $year = $date['year2'];

                $timestamp = mktime(0, 0, 0, $month, 1, $year);

                $formattedDates[] = date("M Y", $timestamp);
            }
            echo json_encode($formattedDates);
        ?>,
        datasets: [{
            label: '# of Quiz taken per Month',
            data: <?php echo json_encode(array_column($dataGraph2, 'quizzes_created')); ?>,
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                stepSize: 1,
                precision: 0
            }
        }
    }
});
</script>

</body>

<footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>

<script src="https://proxy-translator.app.crowdin.net/assets/proxy-translator.js"></script>
<script src='../language.js'></script>
</footer>

</html>