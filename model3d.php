<?php
// connect to DB
//$pdo = new PDO('pgsql:host=localhost;port=5432;dbname=breakfast;user=postgres;password=postgres');
//// if table 'form' does not exist, create it.
//$pdo->query("CREATE TABLE IF NOT EXISTS form(id SERIAL PRIMARY KEY, name TEXT, email TEXT, country TEXT, breakfast TEXT, workout TEXT, timestamp TEXT)");
//
//$set = $pdo->query("select breakfast, count(breakfast) from form group by breakfast")->fetchAll();
//
//// parse query output to create input array for canvasJS
//$data_points = [];
//foreach ($set as $res) {
//    $data_points[] = ['y'=>$res['count'], 'label'=>$res['breakfast']];
//}
//
//// sort it descending breakfasts
//usort($data_points, function ($a, $b) {
//    return $a['y'] - $b['y'];
//});
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

        const renderer = new THREE.WebGLRenderer();
        renderer.setSize( window.innerWidth, window.innerHeight );
        renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.domElement.id = 'yeet';
        document.body.appendChild( renderer.domElement );

        const loader = new OBJLoader();

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 0.1, 1000 );
        camera.position.set(-10, 10, 10);

        const controls = new OrbitControls( camera, renderer.domElement );

        let light_above = new THREE.DirectionalLight( 0xffffff, .2);
        light_above.position.set(0, 100, 0);
        scene.add( light_above );

        let light_below = new THREE.DirectionalLight( 0xffffff, .1);
        light_below.position.set(0, -100, 0);
        scene.add( light_below );

        let plane;

        loader.load( 'media/F22jet.obj',
            function ( object ) {
                plane = object.children[0];
                plane.material = new THREE.MeshStandardMaterial( { color: 0x049ef4, metalness: 1, roughness: 0.4 } );
                plane.position.set(0, 0, 0);
                plane.rotation.set(- Math.PI / 2, 0, 0);
                plane.scale.set(0.1, 0.1, 0.1);
                scene.add( plane );
            }, undefined,
            function ( error ) {
                console.error( error );
            }
        );

        let parts = {
            'leftwing': {
                'root': new THREE.Vector3(),
                'mid': new THREE.Vector3(),
                'tip': new THREE.Vector3(),
            },
            'rightwing': {
                'root': new THREE.Vector3(),
                'mid': new THREE.Vector3(),
                'tip': new THREE.Vector3(),
            }
        }

        const geometry = new THREE.SphereGeometry( 1, 32, 16 );
        const material = new THREE.MeshBasicMaterial( { color: 0x330000} );
        const sphere = new THREE.Mesh( geometry, material );
        scene.add( sphere );

        function animate() {
            requestAnimationFrame( animate );

            renderer.render( scene, camera );
        }

        let rayCaster = new THREE.Raycaster();
        let mousePosition = new THREE.Vector2();

        function onPointerDown( event ) {
            event.preventDefault();
            mousePosition.x = ( event.clientX / window.innerWidth ) * 2 - 1;
            mousePosition.y = - ( event.clientY / window.innerHeight ) * 2 + 1;

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

        document.getElementById("yeet").addEventListener("click", onPointerDown);

        animate();
    </script>
</body>
</html>