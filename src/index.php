<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-fork-ribbon-css/0.2.3/gh-fork-ribbon.min.css" />

    <title>Breakfast Survey</title>

    <style>
        input[type="radio"] {
            appearance: none;
            border: 1px solid #d3d3d3;
            width: 25px;
            height: 25px;
            vertical-align: top;
            margin-top: 1px;
        }

        input[type="radio"]:checked::before{
            position: absolute;
            color: green !important;
            content: "\00A0\2713\00A0" !important;
            font-weight: bolder;
            font-size: 18px;
        }
    </style>

</head>
<body>
<?php

    // connect to DB
    $pdo = new PDO('pgsql:host=db;port=5432;dbname=db;user=bkfast;password=bekfast');
    // if table 'form' does not exist, create it.
    $pdo->query("CREATE TABLE IF NOT EXISTS form(id SERIAL PRIMARY KEY, name TEXT, country TEXT, breakfast TEXT, workout TEXT, timestamp TEXT)");

    $errors = [];
    $post_successful = false;
    if ($_POST) {

        $vars = ['name', 'country', 'breakfast', 'workout', 'timestamp'];
        $values = [];
        foreach ($vars as $key) {
            if (isset($_POST[$key]) && !($_POST[$key] === '')) {
                $values[":$key"] = $_POST[$key];
            }
            else {
                $errors[] = $key;
            }
        }
        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO form(name, country, breakfast, workout, timestamp) VALUES(:name, :country, :breakfast, :workout, :timestamp)");
            $stmt->execute($values);
            $post_successful = true;
        }
    }
?>
<a class="github-fork-ribbon left-top" href="https://github.com/rosaqq/breakfast-survey" data-ribbon="Fork me on GitHub" title="YEET!">Fork me on GitHub</a>
<div class="section">
    <div class="block has-text-centered">
        <h1 class="title">Breakfast survey!</h1>
        <h1 class="subtitle">Changing the world one flake at a time.</h1>
    </div>

    <div class="container">
        <form id="bkform" action="" method="post">
            <!-- Form will split in 2 columns if viewport > 768px -->
            <div class="columns">
                <div class="column is-4-tablet is-offset-2-tablet">
                    <!-- name field -->
                    <div class="field">
                        <label for="name" class="label">Name</label>
                        <div class="control has-icons-left">
                            <input id="name" class="input <?= in_array('name' ,$errors)?'is-danger':''?>" name="name" type="text" placeholder="Your name here...">

                            <!-- icons -->
                            <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                        </div>
                        <?= in_array('name' ,$errors)?"<p class='help is-danger'>Please enter a name.</p>":'' ?>
                    </div>

                    <!-- country field -->
                    <div class="field">
                        <label for="country" class="label">Country</label>
                        <div class="control has-icons-left">
                            <div class="select is-fullwidth <?= in_array('country' ,$errors)?'is-danger':''?>">
                                <select id="country" name="country">
                                    <option value="" selected disabled>Select your country</option>
                                    <?php
                                    $countries = file('countries.txt', FILE_IGNORE_NEW_LINES);
                                    foreach ($countries as $c) {
                                        echo "<option value='$c'>$c</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- icon -->
                            <span class="icon is-small is-left"><i class="fas fa-globe"></i></span>
                        </div>
                        <?= in_array('country' ,$errors)?"<p class='help is-danger'>Please select a country.</p>":'' ?>
                    </div>
                </div>

                <div class="column is-4-tablet">
                    <!-- breakfast field -->
                    <div class="field">
                        <label for="breakfast" class="label">Your breakfast today:</label>
                        <div class="control has-icons-left">
                            <input id="breakfast" class="input <?= in_array('breakfast' ,$errors)?'is-danger':''?>" name="breakfast" type="text" placeholder="What did you eat?">

                            <!-- icon -->
                            <span class="icon is-small is-left"><i class="fas fa-utensils"></i></span>
                        </div>
                        <?= in_array('breakfast' ,$errors)?"<p class='help is-danger'>Please specify your breakfast.</p>":'' ?>
                    </div>

                    <!-- workout field -->
                    <div class="field">
                        <div class="field-label has-text-left">
                            <label class="label">Do you workout regularly?</label>
                        </div>
                        <div class="field-body pt-2">
                            <div class="field">
                                <div class="control">
                                    <label class="radio">
                                        <input type="radio" name="workout" value="1">
                                        <span class="is-size-5">Yes</span>
                                    </label>
                                    <label class="radio">
                                        <input type="radio" name="workout" value="0">
                                        <span class="is-size-5">No</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?= in_array('workout' ,$errors)?"<p class='help is-danger'>Please select an option.</p>":'' ?>
                    </div>

                    <!-- hidden js timestamp field -->
                    <input id="timestamp" name="timestamp" type="hidden" value="">
                </div>
            </div>

            <!-- Tiles to align submit button -->
            <div class="tile is-ancestor">
                <div class="tile is-vertical">
                    <div class="tile is-parent">
                        <div class="tile is-child is-2"></div>
                        <div class="tile is-child is-8">
                            <button class="button is-link" type="button" onclick="sendForm()">Submit</button>
                            <button class="button is-info" type="button" onclick="location.href ='data.php'">Look at the data...</button>
                        </div>
                    </div>

                    <?php if ($post_successful): ?>
                        <div class="tile is-parent">
                            <div class="tile is-child is-2"></div>
                            <div class="tile is-child notification is-success is-8">
                                <p class="title">Submit Successful!</p>
                                <p class="subtitle">Thanks for sharing your breakfast!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </form>
    </div>
</div>
<script>
    function sendForm() {
        document.getElementById("timestamp").setAttribute('value', Date.now().toString());
        document.getElementById("bkform").submit();
    }
</script>
</body>
</html>
