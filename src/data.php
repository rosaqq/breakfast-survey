<?php
    // connect to DB
    $pdo = new PDO('pgsql:host=db;port=5432;dbname=db;user=bkfast;password=bekfast');
    // if table 'form' does not exist, create it.
    $pdo->query("CREATE TABLE IF NOT EXISTS form(id SERIAL PRIMARY KEY, name TEXT, email TEXT, country TEXT, breakfast TEXT, workout TEXT, timestamp TEXT)");

    $set = $pdo->query("select breakfast, count(breakfast) from form group by breakfast")->fetchAll();

    // parse query output to create input array for canvasJS
    $data_points = [];
    foreach ($set as $res) {
        $data_points[] = ['y'=>$res['count'], 'label'=>$res['breakfast']];
    }

    // sort it descending breakfasts
    usort($data_points, function ($a, $b) {
        return $a['y'] - $b['y'];
    });
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js" integrity="sha512-FJ2OYvUIXUqCcPf1stu+oTBlhn54W0UisZB/TNrZaVMHHhYvLBV9jMbvJYtvDe5x/WVaoXZ6KB+Uqe5hT2vlyA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <title>Breakfast Data!</title>

    <script>
        window.onload = function () {

            let chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,

                title:{
                    text:"Top breakfasts eaten"
                },
                axisX:{
                    interval: 1
                },
                axisY2:{
                    interlacedColor: "rgba(1,77,101,.2)",
                    gridColor: "rgba(1,77,101,.1)",
                    title: "Number of Breakfasts"
                },
                data: [{
                    type: "bar",
                    name: "breakfasts",
                    axisYType: "secondary",
                    color: "#014D65",
                    dataPoints: <?php echo json_encode($data_points, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();

        }
    </script>
</head>
<body>
    <section class="section">
        <div class="tile is-ancestor">
            <div class="tile is-parent">
                <div class="tile is-child is-2"></div>
                <div class="tile is-child is-8">
                    <button class="button is-success" onclick="location.href='/'">
                        <span class="icon">
                          <i class="fas fa-arrow-left"></i>
                        </span>
                        <span>Back to the form</span>
                    </button>
                </div>
            </div>
        </div>
        <div id="chartContainer" style="height: 370px; width: 80%; margin: 0 10%"></div>
    </section>
</body>
</html>