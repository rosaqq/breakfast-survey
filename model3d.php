<?php
// connect to DB
$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=breakfast;user=postgres;password=postgres');

$set = $pdo->query("select aircraft_data.*, x, y, z from aircraft_data join parts_mapping on parts_mapping.side = aircraft_data.side and parts_mapping.component = aircraft_data.component")->fetchAll();

// parse query output to create input array for canvasJS
$date_points = [];
foreach ($set as $res) {
    $color = NULL;
    switch ($res['anomaly'])
    {
        case 'Corrosão':
            $color = 0xff0000;
            break;
        case 'Fratura':
            $color = 0x0000ff;
            break;
        case 'Outro':
            $color = 0x00ff00;
            break;
    }
    $data_points[] = [
            'x' => floatval($res['x']),
            'y' => floatval($res['y']),
            'z' => floatval($res['z']),
            'color' => $color
    ];
}
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

    <title>Breakfast Data!</title>
</head>
<body id="body">
    <script type="module">
        import * as THREE from 'https://unpkg.com/three@0.127.0/build/three.module.js';
        import { OrbitControls } from 'https://unpkg.com/three@0.127.0/examples/jsm/controls/OrbitControls.js';
        import { OBJLoader } from 'https://unpkg.com/three@0.127.0/examples/jsm/loaders/OBJLoader.js';

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // SETUP
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        const renderer  = new THREE.WebGLRenderer();
        const loader    = new OBJLoader();
        const scene     = new THREE.Scene();
        const camera    = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const controls  = new OrbitControls(camera, renderer.domElement);

        // Initial configs
        scene.add(camera);
        camera.position.set(75, 50, -50);
        renderer.setSize( window.innerWidth, window.innerHeight );
        renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.domElement.id  = 'yeet';

        // Insert canvas in document
        document.body.appendChild(renderer.domElement);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // LIGHTS
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        let light_above = new THREE.DirectionalLight( 0xffffff, .4);
        let light_below = new THREE.DirectionalLight( 0xffffff, .1);
        let ambient     = new THREE.AmbientLight(0x111111);

        light_above.position.set(0, 1000, 0);
        light_below.position.set(0, -1000, 0);

        scene.add(ambient);
        scene.add(light_above);
        scene.add(light_below);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // OBJECTS
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        // Load PLANE
        let plane;
        let speed = <?= $_GET['speed'] ?? 0 ?>; // Rotation speed from query str
        loader.load('media/plane787.obj', object => {
                // Extract mesh from obj & set material
                plane            = object.children[0];
                plane.material  = new THREE.MeshStandardMaterial({color: 0x049ef4, metalness: 1, roughness: 0.4});

                // Set initial properties & add to scene
                plane.position.set(0, 0, 0);
                plane.scale.set(1, 1, 1);
                scene.add(plane);
            }, undefined, error => console.error(error)
        );

        // SPHERE
        const sphere = new THREE.Mesh(new THREE.SphereGeometry(1, 32, 16), new THREE.MeshBasicMaterial({color: 0x00ffff}));
        scene.add(sphere);

        let data        = <?= json_encode($data_points) ?>;
        const dot_size  = 0.5;
        const noise     = 0.1;  // variate sphere coords by this %
        let varpos      = (val) => Math.random()*noise*val + val;
        const sph_grp   = new THREE.Group();

        data.forEach(point => {
            let sphere = new THREE.Mesh(new THREE.SphereGeometry(dot_size, 32, 16), new THREE.MeshBasicMaterial({color: point.color}));

            sphere.position.set(varpos(point.x), point.y, varpos(point.z));
            sph_grp.add(sphere);
        })
        scene.add(sph_grp);

        // AXIS
        // let axes = new THREE.AxesHelper(100);
        // scene.add(axes);

        // FOG for std background color
        scene.fog = new THREE.FogExp2( 0x9999ff, 0.00025 );

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // MOUSE INTERSECT CALCULATION
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        let rayCaster       = new THREE.Raycaster();
        let mousePosition   = new THREE.Vector2();

        function onPointerDown(event) {
            event.preventDefault();
            mousePosition.x = (event.clientX / window.innerWidth) * 2 - 1;
            mousePosition.y = - (event.clientY / window.innerHeight) * 2 + 1;

            rayCaster.setFromCamera(mousePosition, camera);
            let intersects = rayCaster.intersectObjects(scene.children);

            if (intersects.length)
            {
                let ip = intersects[0].point;
                sphere.position.set(ip.x, ip.y, ip.z);
                console.log(ip);
            }
            else
                console.log('no intersects');
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // EVENT LISTENERS
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        document.getElementById('yeet').addEventListener('dblclick', onPointerDown);
        window.addEventListener('resize', () => {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize( window.innerWidth, window.innerHeight );
            }
        );

        function animate() {
            requestAnimationFrame(animate);
            renderer.render(scene, camera);
            update();
        }

        function update() {
            controls.update();
            if(plane && speed){
                plane.rotation.y    += 0.001*speed;
                sph_grp.rotation.y  += 0.001*speed;
            }
        }

        animate();
    </script>
</body>
</html>