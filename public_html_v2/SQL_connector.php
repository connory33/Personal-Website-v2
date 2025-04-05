<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <link rel="stylesheet" type="text/css" href="../resources/css/default_v2.css">

    <!-- Bootstrap core CSS -->
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../resources/css/album.css" rel="stylesheet">
  </head>
  <body>
    <div class="bg-dark text-white text-center">
        <?php
        $servername = "connoryoung.com";
        $username = "connor";
        $password = "PatrickRoy33";
        $dbname = "Spotify API";

        ini_set('display_errors', 'on');
        error_reporting(E_ALL);

        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        // Check connection
        if (mysqli_connect_error()) {
            die("Connection failed: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $searchTerm = $_POST['search'];
            print("Search term: " . $searchTerm . "<br><br>");
            $sql = "SELECT * FROM `artistids` WHERE `Name` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }
            
            // Check if there are results
            if (mysqli_num_rows($result) > 0) {
                print("Results found: " . mysqli_num_rows($result) . "<br><br>");
            } else {
                print("No results found.<br>");
            }

            $resultData = $result->fetch_assoc();

            echo "<br>";
            echo "<table style='margin: 0 auto; border: 1px solid white; width: 50%;'>";
            echo "<tr style='border: 1px solid white;'>";
            echo "<td style='font-weight: bold'>Artist Name</td>";
            echo "<td style='font-weight: bold'>Spotify ID</td>";
            echo "</tr>";
            foreach($result as $row) {
                echo "<tr style='border: 1px solid gray;'>";
                echo "<td>".$row['Name']."</td>";
                echo "<td>".$row['id']."</td>";
                echo "</tr>";
            }
            echo "</table>";


            $conn->close();
        }
        ?>

    <br>
    <br>
    </div>

    <footer class="text-muted">
      <div class="container">
        <p class="float-right">
          <a href="#">Back to top</a>
        </p>
        <p>Copyright &copy; 2021 Connor Young</p>
      </div>
    </footer>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="../js/vendor/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/vendor/holder.min.js"></script>
  </body>
</html>